<?php
namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Exception;

class Wipe extends AbstractCommand{
    protected static ?string $alias = "wipe";
    protected static bool $requiredCommandValue = true;

    // 今回は短縮系を許しているので、-bと打つだけでbackup機能が実行されるようにしている
    public static function getArguments(): array{
        return [(new Argument('backup'))->description("Excute back-up function to create dump or retrieving from the dump for the database given in the commands")->required(false)->allowAsShort(true)];
    }

    public function execute(): int{
        $dbName = $this->getCommandValue();
        $backup = $this->getArgumentValue("backup");

        if($backup){
            if(gettype($backup) != "string") throw new Exception("File name for backup is invalid");
            $this->log($backup);
            if($backup) $this->backupDB($dbName, $backup);

        }

        // db内をリセット
        $this->wipeDB($dbName);
        
        // 同名の空のdbを作成
        $this->createDB($dbName);

        return 0;
    }

    
    public function wipeDB(string $dbName): void{
        $this->log("Wiping Database...");
        
        // MySQL コマンドでデータベース削除 現在はユーザー名等ををハードコーディング
        $command = sprintf("mysql -u user_project_5 -p -e 'DROP DATABASE %s'", $dbName);

        $output = null;
        $returnVar = null;

        exec($command, $output, $returnVar);

        if($returnVar !== 0){
            throw new Exception(sprintf("Command execution failed -> %s", $command));
        }

        else $this->log("Database deleted successfully!");
    
    }

    
    public function backupDB(string $dbName, string $fileName): void{
        $this->log("Backing up Database...");
        
        // MySQLコマンドでデータベースバックアップを取る
        $command = sprintf("mysqldump -u user_project_5 -p %s > %s.sql", $dbName, $fileName);
        $output = null;
        $returnVar = null;

        exec($command, $output, $returnVar);

        if($returnVar !== 0){
            throw new Exception(sprintf("Command execution failed -> %s", $command));
        }

        else $this->log("Database backuped successfully!");
    }


    public function createDB(string $dbName): void{
        $this->log("Initializing Database...");

        $command = sprintf("mysql -u user_project_5 -p -e 'CREATE DATABASE %s';", $dbName);
        $output = null;
        $returnVar = null;

        exec($command, $output, $returnVar);

        if($returnVar !== 0){
            throw new Exception(sprintf("Command execution failed -> %s", $command));
        }

        else $this->log("Database initialization successfully!");
    }

}