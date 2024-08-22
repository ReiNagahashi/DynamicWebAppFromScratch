<?php
namespace Database;

use Database\MySQLWrapper;
use Exception;
use mysqli;

$mysqli = new MySQLWrapper();
// 各カラム値を引数とした関数を作ってsprintfを返すことでクリーンな実装になるよ！
function insertCarQuery(
    string $make,
    string $model,
    int $year,
    string $color,
    float $price,
    float $mileage,
    string $transmission,
    string $engine,
    string $status
): string{
    return sprintf(
        "INSERT INTO cars(make, model, year, color, price, mileage, transmission, engine, status)
        VALUES('%s', '%s', '%d', '%s', '%f', '%f', '%s', '%s', '%s');",
        $make, $model, $year, $color, $price, $mileage, $transmission, $engine, $status
    );
}


function insertPartQuery(
    string $name,
    string $description,
    float $price,
    int $quantityInStock
): string {
    return sprintf(
        "INSERT INTO parts (name, description, price, quantityInStock)
        VALUES ('%s', '%s', %f, %d);",
        $name, $description, $price, $quantityInStock
    );
}


function runQuery(mysqli $mysqli, string $query): void{
    $result = $mysqli->query($query);
    if($result === false){
        throw new Exception('Could not excute query.');
    }else{
        echo "Query exuted successfully. \n";
    }
}

runQuery($mysqli, insertCarQuery('Toyota', 'Corolla', 2020, 'Blue', 200000, 1500, 'Automatic', 'Gasoline', 'Available'));

runQuery($mysqli, insertPartQuery(
    name: 'Brake Pad',
    description: 'High Quality Brake Pad',
    price: 45.99,
    quantityInStock: 100
));

$mysqli->close();