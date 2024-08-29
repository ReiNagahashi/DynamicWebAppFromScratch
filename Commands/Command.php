<?php
namespace Commands;
// すべてのコマンドが持っているメソッドを定義するコマンドインターフェース

interface Command
{
    // インスタンス化せずにデータにアクセスできるように、静的関数を採用
    public static function getAlias(): string;
    /** @return Argument[]  */
    public static function getArguments(): array;
    public static function getHelp(): string;
    public static function isCommandValueRequired(): bool;

    /** @return bool | string
     * 値が存在する場合は値の文字列か、パラメータが存在する場合はtrueを返す
     * 引数が設定されていない場合はfalseを返す */
    public function getArgumentValue(string $arg): bool | string;
    public function execute(): int;
}