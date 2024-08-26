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


function insertUserQuery(
    string $username,
    string $email,
    string $password,
    string $created_at,
    string $updated_at,
    string $subscription,
    string $subscription_status,
    string $subscription_created_at,
    string $subscription_end_at
){
    return sprintf(
        "INSERT INTO users (user_name, email, password, created_at, updated_at, subscription,
        subscription_status, subscription_created_at, subscription_end_at)
        VALUES ('%s', '%s','%s', '%s','%s',
            '%s','%s', '%s','%s');",
            $username, $email, $password, $created_at, $updated_at, $subscription,
            $subscription_status, $subscription_created_at, $subscription_end_at 
    );
}


function insertPostQuery(
    string $title,
    string $content,
    string $created_at,
    string $updated_at,
    int $user_id
){
    return sprintf(
        "INSERT INTO posts(title, content, created_at, updated_at, user_id)
        VALUES ('%s', '%s', '%s', '%s', '%d');",
        $title, $content, $created_at, $updated_at, $user_id
    );
}


function runQuery(mysqli $mysqli, string $query): void{
    $result = $mysqli->query($query);
    if($result === false){
        throw new Exception('Could not excute query.');
    }else{
        echo "Query excuted successfully. \n";
    }
}

// runQuery($mysqli, insertCarQuery('Toyota', 'Corolla', 2020, 'Blue', 200000, 1500, 'Automatic', 'Gasoline', 'Available'));

// runQuery($mysqli, insertPartQuery(
//     name: 'Brake Pad',
//     description: 'High Quality Brake Pad',
//     price: 45.99,
//     quantityInStock: 100
// ));


// runQuery($mysqli, insertUserQuery('Rei', 'akiba@dmail.com', 'abcdefg', '3033-12-21', '4020-12-24', "yes", "silver", "3033-12-21", "3033-12-30"));

runQuery($mysqli, insertPostQuery('Tate no kai', 'Finally Japan is collapsed!', "2025-01-23", "2030-01-01", 1));

$mysqli->close();