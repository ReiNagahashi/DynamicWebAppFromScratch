<?php
namespace Commands\Programs;
// 新しいコマンドを生成し、新しいマイグレーションを生成する2つの機能が利用できる
use Commands\AbstractCommand;

class CodeGeneration extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'code-gen';
    protected static bool $requiredCommandValue = true;

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        $codeGenType = $this->getCommandValue();
        $this->log('Generating code for.......' . $codeGenType);
        return 0;
    }
}
