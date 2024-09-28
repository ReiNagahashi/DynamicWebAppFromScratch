<?php
namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;
use Exception;
use mysqli;

class bookSearch extends AbstractCommand{
    protected static ?string $alias = "book-search";
    protected static bool $requiredCommandValue = false;
    
    public static function getArguments(): array
    {
        return [
            (new Argument('isbn'))->description('Fetch book data by provided isbn')->required(true)->allowAsShort(false)
        ];
    }

    public function execute(): int{
        $isbn = $this->getArgumentValue('isbn');
        if(strlen($isbn) != 10 && strlen($isbn) != 13) throw new Exception("Input isbn is invalid...");

        $mysqli = new MySQLWrapper();
        
        // キャッシュテーブルの作成 
        $this->runQuery($mysqli, $this->createCacheTableQuery(), "Creating the table...", "Created the table successfully!");
        
        // キャッシュテーブルにisbnが存在するか
        if($this->isSearchedBookExists($mysqli, $isbn)){
            // 30日経過しているかをチェック
            $isExpired = $this->isBookExpired($mysqli, $isbn);

            if($isExpired){ 
                // 経過していた場合は一度そのデータをキャッシュから削除して再度データを作り直す
                // 削除
                $this->runQuery($mysqli, $this->deleteSearchedBookQuery($isbn), "Deleting the book...", "Deleted the book successfully!");
                // インサート
                $this->insertSearchedBook($mysqli, $isbn);
            }
            else
                // 経過していないので何もしない
                $this->log("The searched book is already added.");
        }else{
            // 存在しない場合はデータをインサート
            $this->insertSearchedBook($mysqli, $isbn);
        }

        return 0;
    }


    function insertSearchedBook(mysqli $mysqli, string $isbn): void{
        // isgnをもとにAPIに接続
        $apiUrl = sprintf("https://openlibrary.org/isbn/%s.json", $isbn);
        // cURLでAPIからデータを取得
        $this->log("Fetching data from Open library...");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);

        if($response === false){
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL Error: " . $error);
        }

        curl_close($ch);
        // 取ってきたデータをでコード 第二引数をtrueとすることで返り値は連想配列になる
        $jsonData = json_decode($response, true);

        if(empty($jsonData)) throw new Exception("Returned value is empty...");

        $this->log("Fetching data from Open library completed!");

        // デコードしたデータをインサート jsonデータはそのままjson形式でインサートできる
        $this->runQuery($mysqli, $this->insertSearchedBookQuery(sprintf("book-search-isbn-%s", $isbn), $jsonData), "Data inserting...", "Data inserting completed!");
    }


    function insertSearchedBookQuery(
        string $cache_key,
        array $contents
    ): string{
        $contents_str = json_encode($contents);

        return sprintf(
            "INSERT INTO book_cache(cache_key, contents)
            VALUES ('%s', '%s');",
            $cache_key, $contents_str
        );
    }
    

    function isSearchedBookExists(mysqli $mysqli, string $isbn): bool{
        // クエリをprepareステートメントで行うことでSQLインジェクション対策になるようだ 
        $stmt = $mysqli->prepare("SELECT * FROM book_cache WHERE cache_key = ?");
        if($stmt === false){
            throw new Exception('Could not excute query.');
        }

        // プレースホルダに値をバインドする
        $cacheKey = sprintf("book-search-isbn-%s", $isbn);
        $stmt->bind_param("s", $cacheKey);
        // クエリの実行
        $stmt->execute();
        // 結果の保存
        $stmt->store_result();
        // 取得データが存在するか
        $exists = $stmt->num_rows() > 0;

        return $exists;
    }


    function isBookExpired(mysqli $mysqli, string $isbn): bool{
        $selectExpiredBook = $mysqli->prepare("SELECT * FROM book_cache WHERE cache_key = ? 
            AND updated_at <= DATE_SUB(NOW(), INTERVAL 1 MONTH)");

        if($selectExpiredBook === false){
            throw new Exception('Could not excute query.');
        }

        // プレースホルダに値をバインドする
        $cacheKey = sprintf("book-search-isbn-%s", $isbn);
        $selectExpiredBook->bind_param("s", $cacheKey);

        // クエリの実行
        $selectExpiredBook->execute();
        // 結果の保存
        $selectExpiredBook->store_result();
        // 行が存在するかどうか → 存在する場合は期限が切れている
        $exists = $selectExpiredBook->num_rows() !== 0;

        return $exists;
    }


    function runQuery(mysqli $mysqli, string $query, string $startMsg, string $endMsg): void{
        $this->log($startMsg);
        $result = $mysqli->query($query);
        if($result === false){
            throw new Exception('Could not excute query.');
        }else{
            $this->log($endMsg);
        }
    }


    public function createCacheTableQuery(): string{
        return "
            CREATE TABLE IF NOT EXISTS book_cache(
                cache_key VARCHAR(50) PRIMARY KEY,
                contents JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
        ";
    }


    function deleteSearchedBookQuery(string $isbn): string{
        $cache_key = sprintf("book-search-isbn-%s", $isbn);
        return sprintf("DELETE FROM book_cache WHERE cache_key = '%s'", $cache_key);
    }

}