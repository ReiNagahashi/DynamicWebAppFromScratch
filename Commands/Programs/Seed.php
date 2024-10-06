<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Database\MySQLWrapper;
use Database\Seeder;

// createRowData関数によって返された全ての行が、ComputerPartsSeeder.phpで定義された通りにcomputer_partsテーブルに挿入する
// →このシーディングされたダミーでたは、アプリのテストと開発を非常に簡単にしてくれるのだ

class Seed extends AbstractCommand{
    protected static ?string $alias = 'seed';

    public static function getArguments(): array{
        return [];
    }

    public function execute(): int{
        $this->runAllSeeds();
        return 0;
    }


    function runAllSeeds(): void{
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
                    $seeder->seed();
                }
                else throw new \Exception('Seeder must be a class that subclass the seeder interface');

            }
        }
    }
}