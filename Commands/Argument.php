<?php
namespace Commands;
// コマンドが使用できる引数を定義するビルダークラス
// コマンドは必要に応じて全てのオプション引数を作る必要がある。
// (ビルダーを採用する理由：引数をさらにカスタマイズできるようになるから
// ex: 値が必要か、引数の短縮形が許可されているかどうかなど)


class Argument
{
    private string $argument;
    private string $description = '';
    private bool $required = true;
    private bool $allowAsShort = false;
    public function __construct(string $argument){
        $this->argument = $argument;
    }

    public function getArgument(): string{
        return $this->argument;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function description(string $description): Argument
    {
        $this->description = $description;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function required(bool $required): Argument
    {
        $this->required = $required;
        return $this;
    }

    public function isShortAllowed(): bool
    {
        return $this->allowAsShort;
    }

    public function allowAsShort(bool $allowAsShort): Argument
    {
        $this->allowAsShort = $allowAsShort;
        return $this;
    }
}