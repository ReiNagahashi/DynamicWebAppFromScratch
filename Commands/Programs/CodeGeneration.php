<?php
namespace Commands\Programs;
// 新しいコマンドを生成し、新しいマイグレーションを生成する2つの機能が利用できる
use Commands\AbstractCommand;
use Commands\Argument;
use Exception;

class CodeGeneration extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'code-gen';
    protected static bool $requiredCommandValue = true;

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [
            (new Argument('name'))->description('Name of the file that is to be generated.')->required(false),
        ];
    }

    public function execute(): int
    {
        $codeGenType = $this->getCommandValue();
        if($codeGenType === "migration"){
            $migrationName = $this->getArgumentValue("name");
            $this->generateMigrationFile($migrationName);
        }
        else{
            if(substr($codeGenType, -4) != '.php') throw new Exception("Only .php format is accepted");
            $filePath = sprintf("Commands/Programs/%s", $codeGenType);
            $ok = file_put_contents($filePath, file_get_contents('Commands/boilerplate.php'));
    
            if($ok === false) throw new Exception("Generating command file failed");
        }

        return 0;
    }


    private function generateMigrationFile(string $migrationName): void{
        $fileName = sprintf(
            "%s_%s_%s.php",
            date("Y-m-d"),
            time(),
            $migrationName
        );

        $migrationContent = $this->getMigrationContent($migrationName);

        // 移行ファイルを保存するパスを指定
        $path = sprintf("%s/../../Database/Migrations/%s", __DIR__, $fileName);

        file_put_contents($path, $migrationContent);
        $this->log("Migration file {$fileName} has been generated!");
    }


    private function getMigrationContent(string $migrationName): string{
        $className = $this->pascalCase($migrationName);

        return <<<MIGRATION
        <?php
        namespace Database\Migrations;

        class {$className} implements SchemaMigration
        {
            public function up(): array{
                return [];
            }

            public function down(): array
            {
                return [];
            }
        }

        MIGRATION;

    }

    private function pascalCase(string $string): string{
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

}
