<?php
namespace Response\Render;

use Response\HTTPRenderer;

// HTMLページ用のRendererで、サーバサイドレンダリングを簡単にセットアップして管理する
// ビューファイルのパスと、オプションでデータアイテムのハッシュマップを受け取り、各キーがビューで使用する変数に変換される
// HTMLRendererはMVC のアプローチを採用します。モデル、ビュー、コントローラが分離され、
// コントローラが Renderer クラスのインスタンスを作成して返す役割を果たします。
// コントローラは、OOP クラスやデータベーススキーマにマッピングされたデータなどのモデルを使ってデータを準備し、このデータをビューに渡してコンテンツを作成します。
class HTMLRenderer implements HTTPRenderer{
    private string $viewFile;
    private array $data;

    public function __construct(string $viewFile, array $data = []){
        $this->viewFile = $viewFile;
        $this->data = $data;
    }

    public function getFields(): array
    {
        return[
            'Content-type' => 'text/html; charset=URF-8'
        ];
    }

    public function getContent(): string{
        $viewPath = $this->getViewPath($this->viewFile);
        if(!file_exists($viewPath)){
            throw new \Exception("View file {$viewPath} does not exist");
        }

        // ob_startは全ての出力をバッファに取り組む
        // このバッファはob_get_cleanによって取得することができ、バッファの内容を返し、バッファをクリアする
        ob_start();
        // キーを変数として現在のシンボルテーブルにインポートする 
        // シンボルテーブルとは、フィイル上の関数や変数をそれぞれ名前をキーに、スコープやデータタイプを管理しているコンパイラ・インタプリタ上にあるデータ構造
        extract($this->data);
        require $viewPath;

        return $this->getHeader() . ob_get_clean() . $this->getFooter();
    }


    private function getHeader() : string{
        ob_start();
        require $this->getViewPath('layout/header');

        return ob_get_clean();
    }


    private function getFooter(): string{
        ob_start();
        require $this->getViewPath('layout/footer');

        return ob_get_clean();
    }


    private function getViewPath(string $path): string{
        return sprintf("%s/%s/Views/%s.php", __DIR__, '../../', $path);
    }


}