<?php
namespace Helpers;

use Database\MySQLWrapper;
use Exception;

class DatabaseHelper{
    public static function getRandomCarPart(): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM car_parts ORDER BY RAND() LIMIT 1");
        $stmt->execute();

        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }
    public static function getRandomComputerPart(): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }

    public static function getComputerPartById(int $id): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }

    public static function getComputerPartsByType(string $type): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE type = ?");
        $stmt->bind_param('s', $type);
        $stmt->execute();
        $result = $stmt->get_result();

        $parts = [];
        // fetch_assocは1つのレコードのみを返すので、今回はwhileループでレコード数だけ回してレコードを配列に入れていく
        while($row = $result->fetch_assoc()){
            $parts[] = $row;
        }

        return $parts;
    }


    public static function getDataListPerPage(array $data, int $perpage): array{
        if(sizeof($data) === 0) return [];

        $listSize = ceil(sizeof($data) / $perpage);
        $dataList = [];
        $k = 0;
        for($i = 0; $i < $listSize; $i++){
            $list = [];
            for($j = 0; $j < $perpage && $k < sizeof($data); $j++, $k++){
                $list[] = $data[$k];
            }
            $dataList[] = $list;
        }

        return $dataList;
    }

    public static function getDataByPage(array $data, int $pageNumber): array{
        if($pageNumber < 1 || sizeof($data) === 0) return [];

        if(sizeof($data) < $pageNumber) return $data[sizeof($data)-1];

        return $data[$pageNumber-1];
    }
}