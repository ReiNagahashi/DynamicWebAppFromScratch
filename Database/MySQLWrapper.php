<?php
namespace Database;

use mysqli;
use Helpers\Settings;

// 接続パラメータをまとめて管理し、初期設定をより効果的に管理するため
// mysqli クラスを拡張してデフォルトのコンストラクタを上書きする
class MySQLWrapper extends mysqli {
    // この新しいコンストラクタには、DB 設定データを取得し
    // 接続エラーが発生した際にエラーを投げ、親クラスのコンストラクタを呼び出すロジックが含まれています
    public function __construct(?string $hostname = 'localhost', ? string $username = null,
    ?string $password = null, ?string $database = null, ?int $port = null, ?string $socket = null)
    {
        // 接続の失敗時にエラーを報告し、例外を投げる
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $username = $username??Settings::env('DATABASE_USER');
        $password = $password??Settings::env('DATABASE_USER_PASSWORD');
        $database = $database??Settings::env('DATABASE_NAME');

        parent::__construct($hostname, $username, $password, $database, $port, $socket);
    }

    // 現在の接続のデータベース名を取得するための追加機能
    public function getDatabaseName(): string{
        return $this->query("SELECT database() AS the_db")->fetch_row()[0];
    }
}

