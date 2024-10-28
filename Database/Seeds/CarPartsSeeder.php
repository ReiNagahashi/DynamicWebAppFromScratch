<?php
namespace Database\Seeds;

use Database\AbstractSeeder;
use Faker\Factory;
use Helpers\DatabaseHelper;

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
        for($i = 0; $i < $this->numberOfData; $i++){
            // 配列内の要素をランダムに取得したid値を外部キーとする
            $random_part_id = DatabaseHelper::getRandomCarPart()['id'];
            $carParts[] = self::generateCarPartsData($random_part_id);
        }

        return $carParts;
    }
}