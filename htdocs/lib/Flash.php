<?php
class Flash {
  public static function add($msg, $type='info') {
    $_SESSION['flash'][] = ['msg'=>$msg,'type'=>$type];
  }
  public static function getAll() {
    $msgs = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $msgs;
  }
}