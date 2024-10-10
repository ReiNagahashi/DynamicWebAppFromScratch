<?php
namespace Database\Migrations;
use Database\SchemaMigration;


class CreateCarPartsTable1 implements SchemaMigration
{
    public function up(): array{
        return [
            "CREATE TABLE car_parts(
                id BIGINT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                price FLOAT NOT NULL,
                quantityInStock DECIMAL(12,2), 
                carID BIGINT,
                FOREIGN KEY (carID) REFERENCES cars(id) ON DELETE CASCADE
            )" 
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE car_parts"
        ];
    }
}
