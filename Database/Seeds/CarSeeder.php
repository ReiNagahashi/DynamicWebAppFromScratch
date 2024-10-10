<?php
    namespace Database\Seeds;

    use Database\AbstractSeeder;
    use Faker\Factory;

    class CarSeeder extends AbstractSeeder{
        // TODO: tableName文字列を割り当ててください
        protected ?string $tableName = 'cars';
        // TODO: tableColumns配列を割り当ててください
        protected array $tableColumns = [
            [
                'data_type' => 'string',
                'column_name' => 'make'
            ],
            [
                'data_type' => 'string',
                'column_name' => 'model'
            ],
            [
                'data_type' => 'string',
                'column_name' => 'year'
            ],
            [
                'data_type' => 'string',
                'column_name' => 'color'
            ],
            [
                'data_type' => 'int',
                'column_name' => 'price'
            ],
            [
                'data_type' => 'float',
                'column_name' => 'mileage'
            ],
            [
                'data_type' => 'string',
                'column_name' => 'transmission'
            ],
            [
                'data_type' => 'string',
                'column_name' => 'engine'
            ],
            [
                'data_type' => 'string',
                'column_name' => 'status'
            ]
        ];

        public function createRowData(): array{
            $listOfCars = $this->generateListOfCarsData();
            
            return $listOfCars;
        }


        private static function generateCarData(): array{
            $faker = Factory::create();

            return[
                $faker->word(),
                $faker->word(),
                $faker->date(),
                $faker->colorName(),
                $faker->randomNumber(),
                $faker->randomFloat(),
                $faker->word(),
                $faker->word(),
                $faker->word()                
            ];
        }


        private function generateListOfCarsData(): array{
            $cars = [];
            for($i = 0; $i < $this->numberOfData; $i++){
                $cars[] = self::generateCarData();
            }

            return $cars;
        }

    }