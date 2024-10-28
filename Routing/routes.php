<?php
// URLルーティングの章まではエンドポイントをキーに、viewファイル名を値に持った連想配列をindex.phpに読み込ませていた
// その上で、その連想配列を持ったroutesを使ってviewパスの生成と存在しているかのバリデーションからviewのレンダリングまでを全てindex.php上で行っていた
// →ここで、viewファイルパスに加えてhtmlをレンダリングする際にオプションとして必要となるデータ群を処理するレンダラーをフロントエンド、バックエンドの2つの側に分けて実装
// →その上で、連想配列の値を、viewファイル名の代わりに、レンダラーのコールバック関数を返すことで、キーとして指定したエンドポイントに応じたhtmlの一連のレンダリング処理を行うことができるようになった
// →つまり、index.html上で色んな処理をごっちゃにやるのではなく、それぞれの役割を明確に分けて、例えばroutes上ではエンドポイントとレンダリングの際に必要なデータの用意だけでエンドポイントとビューの登録ができるようになった！
// このシナリオでは、HTTPRenderer オブジェクトがビュー、ルートのコールバックがコントローラ、データベースデータがモデル
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

return [
    'random/part' => function(): HTTPRenderer{
        return new HTMLRenderer('component/random-part', ['part' => $part]);
    },
    'parts' => function(): HTTPRenderer{
        return new HTMLRenderer('component/parts', ['part' => $part]);
    },
    // JSONRendererオブジェクトなので、送るのはjSON形式にしたデータのみなので
    // viewパスは不要だよね！
    'api/random/part' => function(): HTTPRenderer{
        return new JSONRenderer(['part' => $part]);
    },
    'api/parts' => function(): HTTPRenderer{
        return new JSONRenderer(['part' => $part]);
    },
];