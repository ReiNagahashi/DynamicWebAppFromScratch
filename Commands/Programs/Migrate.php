<?php
namespace Commands\Programs;
// すべてのマイグレーションロジックを含んでいる
// マイグレーション・ロールバック・新しいスキーマインストールを実行できる
// マイグレーションテーブルの設定もここでする

use Commands\Abstractcommand;
use Commands\Argument;
use Database\MySQLWrapper;

class Migrate extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'migrate';

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [
            // Argumentオブジェクトのrequiredはtrueにしたら、この場合は必ずrollbackという引数を取らなければエラーを返すようにする
            // Argument がコマンド上での--OO という形で書く部分になる
            (new Argument('rollback'))->description('Roll backwards. An integer n may also be provided to rollback n times.')->required(false)->allowAsShort(false),
            (new Argument('init'))->description("Create the migrations table if it doesn't exist.")->required(false)->allowAsShort(true),
        ];
    }

    public function execute(): int
    {
        // rollbackキーワードがコマンド上に存在する場合、rollback機能に必要な引数をrollback変数に格納している→以下の条件式でキャストする必要あり
        $rollback = $this->getArgumentValue('rollback');
        if($this->getArgumentValue("init")) $this->createMigrationstable();
        if($rollback === false){
            $this->log("Starting migration......");
            $this->migrate();
        }
        else{
            // ロールバックは設定されている場合はtrue、またはそれに添付されている値が整数として表されます。
            $rollback = $rollback === true ? 1 : (int) $rollback;
            $this->log("Running rollback....");
            for($i = 0; $i < $rollback; $i++){
                $this->rollback();
            }
        }

        return 0;
    }


    // このテーブルはid, ファイル名の項目に基づいてバージョン管理を目的としている
    private function createMigrationsTable(): void{
        $this->log("Creating migrations table if necessary...");

        $mysqli = new MySQLWrapper();

        $result = $mysqli->query("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL
            );
        ");

        if($result === false) throw new \Exception("Failed to create migration table.");

        $this->log("Done setting up migration tables.");
        
    }


    private function migrate(): void {
        $this->log("Running migrations...");
        $lastMigration = $this->getLastMigration();
        // ファイル名を日付順(ASC)に並べた配列を返す
        $allMigrations = $this->getAllMigrationFiles();
        // まだmigrationsテーブル上に何もデータがない時は0でinit
        $startIndex = ($lastMigration)? array_search($lastMigration, $allMigrations) + 1 : 0;

        for($i = $startIndex; $i < count($allMigrations); $i++){
            $filename = $allMigrations[$i];

            // まだインクルードされていない場合、ファイルをインクルードする
            include_once($filename);

            $migrationClass = $this->getClassnameFromMigrationFilename($filename);
            $migration = new $migrationClass();
            $this->log(sprintf("Processing up migration for %s", $migrationClass));
            // ここで各マイグレーションファイル上で書いた名前のテーブルをマイグレートするクエリを返す
            $queries = $migration->up();
            if(empty($queries)) throw new \Exception("Must have queries to run for . " . $migrationClass);

            $this->processQueries($queries);
            $this->insertMigration($filename);
        }
        $this->log("Migration ended...\n");
    }


    private function getLastMigration(): ?string{
        $mysqli = new MySQLWrapper();
        // 降順にして一番最初のデータを取る
        $query = "SELECT filename FROM migrations ORDER BY id DESC LIMIT 1";

        $result = $mysqli->query($query);

        if($result && $result->num_rows > 0){
            $row = $result->fetch_assoc();
            return $row['filename'];
        }

        return null;
    }

// ここでmigrationsディレクトリ内のマイグレーションファイル群を取得してソートしている
    private function getAllMigrationFiles(string $order = "asc"): array{
        $directory = sprintf("%s/../../Database/Migrations", __DIR__);
        $this->log($directory);

        $allFiles = glob($directory . "/*.php");

        usort($allFiles, function ($a, $b) use ($order) {
            $compareResult = strcmp($a, $b);
            return ($order === 'desc')? -$compareResult : $compareResult;
        });

        return $allFiles;

    }


    private function getClassnameFromMigrationFilename(string $filename): string{
        // マイグレーションのクラス名を正規表現で取得する
        // /: これは正規表現パターンの開始と終了のデリミタ(情報の一部を区切るための記号や文字のこと)
        // ([^_]+): "_"以外の全ての文字を一致させる。
            // ()はグループをキャプチャするためのもの。
            // [^abc]はabc以外を意味する。
            // キャプチャグループは個別に一致させるために使用される
        // \.php: "."が"\"でエスケープされているので、これは終端が'.php'に一致しなければならないことを意味してる
        if(preg_match('/([^_]+)\.php$/', $filename, $matches)) return sprintf("%s\%s", "Database\Migrations", $matches[1]);
        else throw new \Exception("Unexpected migration filename format: " . $filename);
    }


    private function processQueries(array $queries): void{
        $mysqli = new MySQLWrapper();
        foreach($queries as $query){
            $result = $mysqli->query($query);
            if($result === false) throw new \Exception(sprintf("Query {%s} failed.", $query));
            else $this->log("Run query: " . $query);
        }
    }


    private function insertMigration(string $filename): void{
        $mysqli = new MySQLWrapper();

        // statementはprepareが返すもの
            // →ここから、オリジナルのクエリ文字列に実際の値を挿入して、クエリを実行することもできる。
        $statement = $mysqli->prepare("INSERT INTO migrations (filename) VALUES (?)");
        if(!$statement){
            throw new \Exception("Prepare failed: (" . $mysqli->errno . ")" . $mysqli->error);
        }

        // クエリが準備されたので、準備されたクエリに文字列値を挿入する
        $statement->bind_param("s", $filename);

        if(!$statement->execute()){
            throw new \Exception("Excute failed: (" . $statement->errno . ") " . $statement->error);
        }

        $statement->close();
    }


    // ロールバック：マイグレーションで問題があったときなどにマイグレーション前の状態に戻る処理
        // 最後に実行されたマイグレーションのインデックスを探して、それを全てのロールバックの開始インデックスとして設定する
        // それから１つずつマイグレーションのdownを実行し、次に進むためにインデックスを減らしていく
    private function rollback(int $n = 1): void {
        $this->log("Rolling back {$n} migration(s)...\n");

        $lastMigration = $this->getLastMigration();
        $allMigrations = $this->getAllMigrationFiles();

        // ソートされたリストで最後のマイグレーションのインデックスを探す
        $lastMigrationIndex = array_search($lastMigration, $allMigrations);

        if($lastMigrationIndex === false){
            $this->log("Could not find the last migration ran: " . $lastMigration);
            return;
        }

        $count = 0;
        // 毎回、マイグレーションのダウン関数を実行する
        for($i = $lastMigrationIndex; $count < $n && $i >= 0; $i--){
            $filename = $allMigrations[$i];
            $this->log("Rolling back: {$filename}");

            include_once($filename);

            $migrationClass = $this->getClassnameFromMigrationFilename($filename);
            $migration = new $migrationClass();

            $queries = $migration->down();
            if(empty($queries)) throw new \Exception("Must have queries to run for . " . $migrationClass);

            $this->processQueries($queries);
            $this->removeMigration($filename);
            $count++;
        }

        $this->log("Rollback completed.\n");

    }


    private function removeMigration(string $filename): void{
        $mysqli = new MySQLWrapper();
        $statement = $mysqli->prepare("DELETE FROM migrations WHERE filename = ?");
        if(!$statement) throw new \Exception("Prepare failed: (" . $mysqli->errno . ")" . $mysqli->error);

        $statement->bind_param("s", $filename);
        if(!$statement->execute()) throw new \Exception("Execution failed: (" . $statement->errno . ")" . $statement->error);

        $statement->close();
    }
}