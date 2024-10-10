<?php
namespace Database\Migrations;
use Database\SchemaMigration;


class CreateCarTable1 implements SchemaMigration
{
    public function up(): array{
        return [
            "CREATE TABLE cars(
                    id BIGINT PRIMARY KEY AUTO_INCREMENT,
                    make VARCHAR(255) NOT NULL,
                    model VARCHAR(255) NOT NULL,
                    year DATE NOT NULL,
                    color VARCHAR(255) NOT NULL,
                    price FLOAT NOT NULL,
                    mileage FLOAT NOT NULL,
                    transmission VARCHAR(255) NOT NULL,
                    engine VARCHAR(255) NOT NULL,
                    status VARCHAR(255) NOT NULL
                )"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE cars"
        ];
    }
}
