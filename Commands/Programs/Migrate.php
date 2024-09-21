<?php
namespace Commands\Programs;
// すべてのマイグレーションロジックを含んでいる
// マイグレーション・ロールバック・新しいスキーマインストールを実行できる

use Commands\Abstractcommand;
use Commands\Argument;

class Migrate extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'migrate';

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [
            // Argumentオブジェクトのrequiredはtrueにしたら、この場合は必ずrollbackという引数を取らなければエラーを返すようにする
            (new Argument('rollback'))->description('Roll backwards. An integer n may also be provided to rollback n times.')->required(false)->allowAsShort(false)
        ];
    }

    public function execute(): int
    {
        // rollbackキーワードがコマンド上に存在する場合、rollback機能に必要な引数をrollback変数に格納している→以下の条件式でキャストする必要あり
        $rollback = $this->getArgumentValue('rollback');
        if($rollback === false){
            $this->log("Starting migration......");
            $this->migrate();
        }
        else{
            // ロールバックは設定されている場合はtrue、またはそれに添付されている値が整数として表されます。
            $rollback = $rollback === true ? 1 : (int) $rollback;
            $this->log("Running rollback....");
            for($i = 0; $i < $rollback; $i++){
                $this->rollback();
            }
        }

        return 0;
    }
    private function migrate(): void {
        $this->log("Running migrations...");
        $this->log("Migration ended...\n");
    }

    // マイグレーションで問題があったときなどにマイグレーション前の状態に戻る処理
    private function rollback(): void {
        $this->log("Rolling back migration...\n");
    }
}