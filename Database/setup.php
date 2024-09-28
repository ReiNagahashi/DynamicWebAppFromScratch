<?php
// 今回はinit-app.php内でmigrateというコマンドがあった場合にincludeでこのファイルを実行している
namespace Database;

use Exception;

$mysqli = new MySQLWrapper();

$result = $mysqli->query("
    CREATE TABLE IF NOT EXISTS cars (
        id INT PRIMARY KEY AUTO_INCREMENT,
        make VARCHAR(50),
        model VARCHAR(50),
        year INT,
        color VARCHAR(20),
        price FLOAT,
        mileage FLOAT,
        transmission VARCHAR(20),
        engine VARCHAR(20),
        status VARCHAR(10)
    );
");

if($result == false) throw new Exception('Could not excute query.');
else print("Successfully ran all SQL setup queries.".PHP_EOL);

// partテーブル
$result = $mysqli->query("
    CREATE TABLE IF NOT EXISTS parts (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(50),
        description VARCHAR(50),
        price FLOAT,
        quantityInStock INT
    );
");

if($result == false) throw new Exception('Could not excute query.');
else print("Successfully ran all SQL setup queries.".PHP_EOL);

// carとpartの中間テーブル作成 carID, partID, quantity
$result = $mysqli->query("
    CREATE TABLE IF NOT EXISTS cars_parts (
        carID INT,
        partID INT,
        quantity INT,
        PRIMARY KEY (carID, partID)
    );
");


if($result == false) throw new Exception('Could not excute query.');
else print("Successfully ran all SQL setup queries.".PHP_EOL);
