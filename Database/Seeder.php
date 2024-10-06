<?php

namespace Database;

// テーブルの初期データ投入のことをSeedというようだ
// ここでは各テーヴルのシードをするときにデータの整合性や管理のしやすさのために、シードのルールを作ることにする
interface Seeder{
    // createRowメソッドを実行し、その結果のバリデーション・挿入を実行する
    public function seed(): void;
    // 実際にデータ投入をする
    public function createRowData(): array;
}