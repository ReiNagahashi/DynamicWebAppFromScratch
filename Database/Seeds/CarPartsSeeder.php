<?php
namespace Database\Seeds;

use Database\AbstractSeeder;
use Database\MySQLWrapper;
use Exception;
use Faker\Factory;

class CarPartsSeeder extends AbstractSeeder{
    // TODO: tableName文字列を割り当ててください
    protected ?string $tableName = 'car_parts';
    // TODO: tableColumns配列を割り当ててください
    protected array $tableColumns = [
        [
            'data_type' => 'string',
            'column_name' => 'name'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'description'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'price'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'quantityInStock'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'carID'
        ]
    ];

    public function createRowData(): array{
        $listOfCarParts = $this->generateListOfCarPartsData();

        return $listOfCarParts;
    }

    private static function getCarIds(): array{
        $mysqli = new MySQLWrapper();

        // クエリをprepareステートメントで行うことでSQLインジェクション対策になるようだ 
        $stmt = $mysqli->prepare("SELECT id FROM cars");
        if($stmt === false){
            throw new Exception('Could not excute query.');
        }

        // クエリの実行
        $stmt->execute();
        // 結果の保存
        $stmt->store_result();
        // 取得データが存在するか
        // ⚠️bind_resultを使う必要がある
        $stmt->bind_result($id);
        $carIds = [];
        while($stmt->fetch()){
            $carIds[] = ['id' => $id];
        }

        $stmt->close();
        $mysqli->close();

        return $carIds;
    }

    private static function generateCarPartsData(int $carId): array{
        $faker = Factory::create();

        return[
            $faker->word(),
            $faker->word(),
            $faker->randomFloat(),
            $faker->randomNumber(),
            $carId
        ];
    }


    function generateListOfCarPartsData():array{
        $carParts = [];
        // ここにcarsテーブルからidを全て取得した配列を用意する。
        // その上で、その配列内の要素をランダムに取得した値を外部キーとする
        $forgein_keys = self::getCarIds();

        if(count($forgein_keys) == 0) return [];

        for($i = 0; $i < $this->numberOfData; $i++){
            $carParts[] = self::generateCarPartsData(array_rand($forgein_keys));
        }

        return $carParts;
    }
}