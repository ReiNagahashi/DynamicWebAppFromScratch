<?php

namespace Database;

use Database\MySQLWrapper;

// このクラスを拡張して必要な変数やメソッドを提供することで
// 具体的なシーダークラスを作っていく
// 簡潔なバリデーション・dbへのデータ挿入を行うメソッドが実装されているよ

abstract class AbstractSeeder implements Seeder{
    protected MySQLWrapper $conn;
    protected ?string $tableName = null;
    // テーブルカラムは、'data_type'と'column_name'を含む連想配列
    protected array $tableColumns = [];
    protected int $numberOfData = 0;

    // 使用可能なカラムのタイプ。これらはバリデーションとbind_param()のために使われる
    // キーはタイプの文字列で、値はbind_param()の文字列
    const AVAILABLE_TYPES = [
        'int' => 'i',
        // phpではfloatは実際にはdoubleの精度なのでdとする
        'float' => 'd',
        'string' => 's',
    ];

    public function __construct(MySQLWrapper $conn){
        $this->conn = $conn;
    }

    public function seed(int $numberOfData): void{
        $this->numberOfData = $numberOfData;

        $data = $this->createRowData();

        if($this->tableName === null) throw new \Exception("Class requires table name");
        if(empty($this->tableColumns)) throw new \Exception("Class requires collumns");

        foreach($data as $row){
            // 行を検証し、問題がなければ行を挿入する
            $this->validateRow($row);
            $this->insertRow($row);
        }
    }

    // 各行をtableColumnsと照らし合わせて検証する
    protected function validateRow(array $row): void{
        if(count($row) !== count($this->tableColumns)) throw new \Exception('Row does not match the table columns');

        foreach($row as $i=>$value){
            $columnDataType = $this->tableColumns[$i]['data_type'];
            $columnName = $this->tableColumns[$i]['column_name'];

            if(!isset(static::AVAILABLE_TYPES[$columnDataType])) throw new \InvalidArgumentException(sprintf("The data type %s is not an available data type.", $columnDataType));
            // phpは値のデータタイプを返すget_debug_type()関数とgettype()関数の両方を提供する(クラス名でも機能するよ)
            // get_debug_typeはネイティブのphp8タイプを返す。例えば、floatsのgettype(4.5)は、ネイティブのデータタイプ'float'ではなく、文字列'double'を返す
            if(get_debug_type($value) !== $columnDataType) throw new \InvalidArgumentException(sprintf("Value for %s should be of type %s. Here is the current is the current value: %s", $columnName, $columnDataType, json_encode($value)));
        }
    }

    protected function insertRow(array $row): void{
        // カラム名の取得
        $columnNames = array_map(function($columnInfo){ return $columnInfo['column_name'];}, $this->tableColumns);

        // クエリを準備する際、count($row)のプレースホルダー'?'がある。bind_param関数はこれらにデータを挿入する
        $placeholders = str_repeat('?,', count($row) - 1) . '?';

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->tableName,
            implode(', ', $columnNames),
            $placeholders
        );

        $stmt = $this->conn->prepare($sql);
        // implodeは配列を１つの文字列に結合して、その文字列を返す
        $dataTypes = implode(array_map(function($columnInfo){ return static::AVAILABLE_TYPES[$columnInfo['data_type']];}, $this->tableColumns));

        // bind paramsは文字の配列を取り、それぞれに値を挿入する
        // 例：$stmt->bind_param('iss', ...array_values([1, 'John', 'john@example.com'])) は、ステートメントに整数、文字列、文字列を挿入します。
        $stmt->bind_param($dataTypes, ...array_values($row));

        $stmt->execute();
    }
}