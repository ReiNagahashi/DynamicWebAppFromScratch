<?php
namespace Response\Render;


// エンドポイントはapi/というキーワードが必ず含まれる。
// →その理由はこのJSONRendererオブジェクトで返すことで、データがJSONデータとして渡されることを
// 意味するので、フロントエンド側等の他のプログラムがそれを処理することができるのだ！
// なので、Web APIエンドポイントやSPAのような
// クライアントサイドレンダリングのためのクライアントでよく使われる
use Response\HTTPRenderer;

class JSONRenderer implements HTTPRenderer{
    private array $data;

    public function __construct(array $data){
        $this->data = $data;
    }

    public function getFields(): array{
        return [
            'Content-Type' => 'application/json; charset=UTF-8'
        ];
    }

    public function getContent(): string{
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }
}