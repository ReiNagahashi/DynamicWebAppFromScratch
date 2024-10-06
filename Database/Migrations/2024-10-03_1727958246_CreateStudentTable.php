<?php
namespace Database\Migrations;
use Database\SchemaMigration;


class CreateStudentTable implements SchemaMigration
{
    public function up(): array{
        return [
            "
                CREATE TABLE students (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(100),
                    age INT,
                    major VARCHAR(50)
                )
            "
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE students"
        ];
    }
}
