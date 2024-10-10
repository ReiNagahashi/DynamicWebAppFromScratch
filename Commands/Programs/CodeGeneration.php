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
        // マイグレーションファイルの作成
        if($codeGenType === "migration"){
            $migrationName = $this->getArgumentValue("name");
            $this->generateMigrationFile($migrationName);
        }
        else if($codeGenType === "seeder"){
            $seederName = $this->getArgumentValue("name");
            $this->generateSeederFile($seederName);
        }
        // コマンドファイルの作成
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
        use Database\SchemaMigration;


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


    private function generateSeederFile(string $seederName): void{
        if(strlen($seederName) < 6 || substr($seederName, -6, 6) != "Seeder"){
            $seederName = $seederName . "Seeder";
        }

        
        $seederContent = $this->getSeederContent($seederName);
        
        $seederName = $seederName . ".php";
        // 移行ファイルを保存するパスを指定
        $path = sprintf("%s/../../Database/Seeds/%s", __DIR__, $seederName);

        file_put_contents($path, $seederContent);
        $this->log("Seeder file {$seederName} has been generated!");
    }


    private function getSeederContent(string $seederName): string{
        $className = $this->pascalCase($seederName);

        return <<< SEEDER
        <?php
            namespace Database\Seeds;

            use Database\AbstractSeeder;

            class {$className} extends AbstractSeeder{
                // TODO: tableName文字列を割り当ててください

                // TODO: tableColumns配列を割り当ててください

                public function createRowData(): array{
                    // TODO: createRowData()メソッドを実装してください
                    return [];
                }
            }
        SEEDER;

    }

    private function pascalCase(string $string): string{
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }    



}
