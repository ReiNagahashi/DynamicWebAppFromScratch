<?php
namespace Helpers;
// セキュリティ対策として、ユーザー入力をバリデートするヘルパー関数

class ValidationHelper {
    public static function integer($value, float $min = -INF, float $max = INF): int{
        // PHPにはデータを検証する組み込み関数がある
        $value = filter_var($value, FILTER_VALIDATE_INT, ["min_range" => (int) $min, "max_range" => (int) $max]);
        // falseの場合、フィルターは失敗したことを意味する
        if($value === false) throw new \InvalidArgumentException("The provided value is an invalid integer.");

        // 値が全てのチェックをパスしたら、そのまま返す
        return $value;
    }
}