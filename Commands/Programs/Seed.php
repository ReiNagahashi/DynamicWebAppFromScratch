<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Database\MySQLWrapper;
use Database\Seeder;

// createRowData関数によって返された全ての行が、ComputerPartsSeeder.phpで定義された通りにcomputer_partsテーブルに挿入する
// →このシーディングされたダミーでたは、アプリのテストと開発を非常に簡単にしてくれるのだ

class Seed extends AbstractCommand{
    protected static ?string $alias = 'seed';
    protected static bool $requiredCommandValue = true;

    public static function getArguments(): array{
        return [];
    }

    public function execute(): int{
        $numberOfFakeData = $this->getCommandValue();
        if(!is_numeric($numberOfFakeData)) throw new \Exception("Argument Error: Only digit value is accepted.");
        
        $this->runAllSeeds(intval($numberOfFakeData));
        return 0;
    }


    function runAllSeeds(int $numberOfFakeData): void{
        $directoryPath = __DIR__ . '/../../Database/Seeds';
        // 引数に持ってきたパスであるディレクトリ内の、ファイルを全て抽出する
        $files = scandir($directoryPath);

        foreach($files as $file){
            if(pathinfo($file, PATHINFO_EXTENSION) === 'php'){
                // ファイル名からクラス名を抽出
                $className = 'Database\Seeds\\' . pathinfo($file, PATHINFO_FILENAME);

                // シードファイルをインクルードする
                include_once $directoryPath . '/' . $file;

                if(class_exists($className) && is_subclass_of($className, Seeder::class)){
                    $seeder = new $className(new MySQLWrapper());
                    $seeder->seed($numberOfFakeData);
                }
                else throw new \Exception('Seeder must be a class that subclass the seeder interface');

            }
        }
    }
}