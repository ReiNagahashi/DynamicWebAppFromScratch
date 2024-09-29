<?php
namespace Commands\Programs;
// 新しいコマンドを生成し、新しいマイグレーションを生成する2つの機能が利用できる
use Commands\AbstractCommand;
use Exception;

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
        $commandName = $this->getCommandValue();
        if(substr($commandName, -4) != '.php') throw new Exception("Only .php format is accepted");

        $filePath = sprintf("Commands/Programs/%s", $commandName);
        $ok = file_put_contents($filePath, file_get_contents('Commands/boilerplate.php'));

        if($ok === false) throw new Exception("Generating command file failed");

        return 0;
    }
}
