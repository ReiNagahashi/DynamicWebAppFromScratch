<?php
namespace Database\Seeds;

use Database\AbstractSeeder;
use Faker\Factory;

// 具体的なシーダークラスはすべて、Seedsフォルダに格納される
// →ここを一旦書いたら、もう一度AbstractSeederを見ると何をしているのがわかると思うよ
// これらは全てAbstractSeederを拡張する
// これらの各クラスはシーダーシステムの利用者であるため、ルールに従い、tableName, tableColumns, createRowData()を定義する必要がある

class ComputerPartsSeeder extends AbstractSeeder{
    protected ?string $tableName = 'computer_parts';
    protected array $tableColumns = [

        [
            'data_type' => 'string',
            'column_name' => 'name'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'type'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'brand'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'model_number'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'release_date'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'description'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'performance_score'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'market_price'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'rsm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'power_consumptionw'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'lengthm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'widthm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'heightm'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'lifespan'
        ]

    ];

    public function createRowData(): array{
        $listOfComputerParts = $this->generateListOfComputerPartsData();

        return $listOfComputerParts;
    }

    // 多分ここでfakerで記述するよりも、Abstractクラス状にあるデータタイプの変数の各フィールドに応じてデータを生成する方がいいかも？
    private static function generateComputerPartsData(): array{
        $faker = Factory::create();
        return [
            $faker->word(),
            $faker->word(),
            $faker->word(),
            $faker->ean13(),
            $faker->date(),
            $faker->word(),
            $faker->randomDigit(),
            $faker->randomFloat(),
            $faker->randomFloat(),
            $faker->randomFloat(),
            $faker->randomFloat(),
            $faker->randomFloat(),
            $faker->randomFloat(),
            $faker->randomDigit()
        ];
    }

    
    private function generateListOfComputerPartsData(): array{
        $parts = [];
        for($i = 0; $i < $this->numberOfData; $i++){
            $parts[] = self::generateComputerPartsData();
        }

        return $parts;
    }





    }