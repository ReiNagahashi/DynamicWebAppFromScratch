<?php
spl_autoload_extensions(".php");
spl_autoload_register();

use Database\MySQLWrapper;
use Helpers\Settings;

// 接続の失敗時にエラーを例外とともに出力→これはdb接続の初期化の際は必ず実装してテストをすること
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// getopt関数はCLIで渡された引数を取ってくる 今回はmigrateという引数を渡している
$opts = getopt('mi', ['migrate', 'insert']);
// isset関数でコマンドラインに'migrate'という引数が含まれているかをチェックする
if(isset($opts['migrate'])){
    printf('Database migration enabled.');
    // includeで記述時に指定したファイルを実行する
    include('Database/setup.php');
    printf('Database migration ended.');
}else if(isset($opts['insert'])){
    printf('Database insertion enabled.');
    include('Database/insert.php');
    printf('Database insertion ended.');
}


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