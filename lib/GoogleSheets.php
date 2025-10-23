
<?php
class GoogleSheets {
  private $cfg;
  private $token;

  public function __construct($googleCfg) { $this->cfg = $googleCfg; }

  private function loadServiceAccount() {
    $path = $this->cfg['service_account_json'];
    if (!file_exists($path)) throw new Exception("Service account JSON not found: $path");
    $json = json_decode(file_get_contents($path), true);
    if (!$json) throw new Exception("Invalid service account JSON");
    return $json;
  }

  private function base64url($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }

  private function fetchAccessToken() {
    if ($this->token && $this->token['exp'] > time()+60) return $this->token['access_token'];
    $sa = $this->loadServiceAccount();
    $header = ['alg'=>'RS256','typ'=>'JWT'];
    $now = time();
    $claims = [
      'iss' => $sa['client_email'],
      'scope' => implode(' ', $this->cfg['scopes']),
      'aud' => $this->cfg['token_uri'],
      'exp' => $now + 3600,
      'iat' => $now,
    ];
    $segments = [ $this->base64url(json_encode($header)), $this->base64url(json_encode($claims)) ];
    $unsigned = implode('.', $segments);
    $key = openssl_pkey_get_private($sa['private_key']);
    if (!$key) throw new Exception("Failed to load private key");
    $signature = '';
    openssl_sign($unsigned, $signature, $key, 'sha256');
    $jwt = $unsigned . '.' . $this->base64url($signature);

    $resp = $this->httpPostForm($this->cfg['token_uri'], [
      'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
      'assertion'  => $jwt,
    ]);
    $data = json_decode($resp, true);
    if (!isset($data['access_token'])) throw new Exception("Failed to obtain access token: " . $resp);
    $this->token = [ 'access_token' => $data['access_token'], 'exp' => $now + $data['expires_in'] - 60 ];
    return $this->token['access_token'];
  }

  private function httpGet($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $this->fetchAccessToken()],
    ]);
    $out = curl_exec($ch);
    if ($out === false) throw new Exception("curl GET error: " . curl_error($ch));
    curl_close($ch);
    return $out;
  }

  private function httpRequestJson($url, $payload, $method='POST') {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $this->fetchAccessToken(),
        "Content-Type: application/json"
      ],
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
    $out = curl_exec($ch);
    if ($out === false) throw new Exception("curl {$method} error: " . curl_error($ch));
    curl_close($ch);
    return $out;
  }

  private function httpPostForm($url, $fields) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => http_build_query($fields),
    ]);
    $out = curl_exec($ch);
    if ($out === false) throw new Exception("curl FORM POST error: " . curl_error($ch));
    curl_close($ch);
    return $out;
  }

  // FULL SHEET fetch (entire used range)
  public function getAllRecords($spreadsheetId, $sheetName, $expectedHeaders) {
    $range = rawurlencode($sheetName);
    $url = "https://sheets.googleapis.com/v4/spreadsheets/" . $spreadsheetId . "/values/" . $range;
    $resp = json_decode($this->httpGet($url), true);
    $values = $resp['values'] ?? [];
    if (!$values) return ['headers'=>[], 'rows'=>[], 'raw'=>[]];

    $headers = array_map(function($h){ return mb_strtolower(trim($h)); }, $values[0]);
    $rows = [];
    for ($i=1; $i<count($values); $i++) {
      $row = $values[$i];
      if (!is_array($row) || count(array_filter($row, fn($v)=>$v!=='')) === 0) continue;
      $assoc = [];
      $max = max(count($headers), count($row));
      for ($c=0; $c<$max; $c++) {
        $key = $headers[$c] ?? ("col".($c+1));
        $assoc[$key] = $row[$c] ?? '';
      }
      $rows[] = $assoc;
    }
    return ['headers'=>$headers,'rows'=>$rows,'raw'=>$values];
  }

  // Update a single cell (numbers stay numbers)
  public function updateCell($spreadsheetId, $sheetName, $row, $col, $value) {
    $range = $sheetName . '!' . self::colToLetter($col) . $row;
    $url = "https://sheets.googleapis.com/v4/spreadsheets/" . $spreadsheetId . "/values/" . rawurlencode($range) . "?valueInputOption=RAW";
    $payload = [
      'range' => $range,
      'majorDimension' => 'ROWS',
      'values' => [[ $value ]] // keep numeric if numeric
    ];
    return $this->httpRequestJson($url, $payload, 'PUT');
  }

  // Append a row to the bottom of a sheet
  public function appendRow($spreadsheetId, $sheetName, $rowValues) {
    $range = rawurlencode($sheetName);
    $url = "https://sheets.googleapis.com/v4/spreadsheets/" . $spreadsheetId . "/values/" . $range . ":append?valueInputOption=RAW&insertDataOption=INSERT_ROWS";
    $payload = [
      'range' => $sheetName,
      'majorDimension' => 'ROWS',
      'values' => [ array_values($rowValues) ]
    ];
    return $this->httpRequestJson($url, $payload, 'POST');
  }

  public static function colToLetter($col) {
    $str = '';
    while ($col > 0) { $col--; $str = chr(65 + ($col % 26)) . $str; $col = intdiv($col, 26); }
    return $str;
  }
}
