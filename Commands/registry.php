<!-- 利用可能なコマンドのリストを保持
ここで定義されているコマンドクラスが、コンソールアプリケーションで使用できるようになる-->
<?php
return [
    Commands\Programs\Migrate::class,
    Commands\Programs\CodeGeneration::class,
    // 上の２つはサンプルコマンド
];