<?php
class Response {
  public static function render($view, $vars = []) {
    extract($vars);
    include __DIR__ . '/../views/_header.php';
    include __DIR__ . '/../views/' . $view . '.php';
    include __DIR__ . '/../views/_footer.php';
    exit;
  }
  public static function redirect($url) {
    header("Location: $url");
    exit;
  }
  public static function json($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
  }
}