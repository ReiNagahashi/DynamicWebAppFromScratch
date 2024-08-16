<?php
spl_autoload_extensions(".php");
spl_autoload_register();

use Database\MySQLWrapper;
use Helpers\Settings;

// 接続の失敗時にエラーを例外とともに出力→これはdb接続の初期化の際は必ず実装してテストをすること
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = new MySQLWrapper('localhost', Settings::env('DATABASE_USER'),
Settings::env('DATABASE_USER_PASSWORD'), Settings::env('DATABASE_NAME'));

$charset = $mysqli->get_charset();

if($charset === null) throw new Exception('Charset could not be read');

printf(
    "%s's charset: %s.%s",
    Settings::env('DATABASE_NAME'),
    $charset->charset,
    PHP_EOL
);

$mysqli->close();