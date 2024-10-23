<?php
namespace Helpers;

use Database\MySQLWrapper;
use Exception;

class HelperFunction{
    static function getIdsFromTable(string $tableName): array{

        $mysqli = new MySQLWrapper();
    
        $query = "SELECT id FROM $tableName";
        $stmt = $mysqli->prepare($query);
        if($stmt === false){
            throw new Exception('Could not execute query.');
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
