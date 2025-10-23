<?php
header('Content-Type: text/plain; charset=utf-8');

function o($k,$v){ echo str_pad($k.':', 24).' '.(is_bool($v)?($v?'OK':'FAIL'):($v?:'-'))."\n"; }

o('PHP version', PHP_VERSION);
o('OpenSSL loaded', extension_loaded('openssl'));
o('cURL loaded', extension_loaded('curl'));

$cfg_path = __DIR__.'/config.php';
o('config.php exists', file_exists($cfg_path));
$cfg = @require $cfg_path;
o('config loaded', is_array($cfg));

$sa_path = $cfg['google']['service_account_json'] ?? (__DIR__.'/service-account.json');
o('service-account.json path', $sa_path);
o('service-account.json exists', file_exists($sa_path));

if (file_exists($sa_path)) {
  $j = json_decode(file_get_contents($sa_path), true);
  o('service json parse', is_array($j));
  o('has client_email', !empty($j['client_email']));
  o('has private_key', !empty($j['private_key']));
}

o('account_spreadsheet_id', $cfg['google']['account_spreadsheet_id'] ?? '');
o('student_spreadsheet_id', $cfg['google']['student_spreadsheet_id'] ?? '');

// Quick token call
if (extension_loaded('curl') && file_exists($sa_path)) {
  $j = json_decode(file_get_contents($sa_path), true);
  if (!empty($j['client_email']) && !empty($j['private_key'])) {
    $header = ['alg'=>'RS256','typ'=>'JWT'];
    $now = time();
    $claims = [
      'iss' => $j['client_email'],
      'scope' => 'https://www.googleapis.com/auth/spreadsheets https://www.googleapis.com/auth/drive.readonly',
      'aud' => 'https://oauth2.googleapis.com/token',
      'exp' => $now + 3600,
      'iat' => $now,
    ];
    $b64 = fn($x)=> rtrim(strtr(base64_encode(json_encode($x)),'+/','-_'),'=');
    $unsigned = $b64($header).'.'.$b64($claims);
    $sig = '';
    openssl_sign($unsigned, $sig, $j['private_key'], 'sha256');
    $jwt = $unsigned.'.'.rtrim(strtr(base64_encode($sig), '+/', '-_'), '=');

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER=>true,
      CURLOPT_POST=>true,
      CURLOPT_POSTFIELDS=>http_build_query([
        'grant_type'=>'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'=>$jwt
      ])
    ]);
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($resp !== false) {
      $data = json_decode($resp, true);
      o('token success', !empty($data['access_token']));
      if (empty($data['access_token'])) {
        echo "token error payload: $resp\n";
      }
    } else {
      echo "curl error: $err\n";
    }
  }
}

echo "\nIf anything says FAIL, fix that first.\n";
