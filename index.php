<?php
// ここからルートのマッチングを処理し、ロード、ヘッダー、メタデータ
// などを設定するインデックスファイルを作成することができます。
spl_autoload_extensions(".php");
spl_autoload_register();

// ルートのロード
$routes = include('Routing/routes.php');

// リクエストURIを解析してパスだけを取得
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

if(isset($routes[$path])){
    $view = $routes[$path];
    $viewPath = sprintf("%s/Views/%s.php", __DIR__, $view);

    if(file_exists($viewPath)){
        // ヘッダー・フッターの設定
        include 'Views/layout/header.php';
        include $viewPath;
        include 'Views/layout/footer.php';
    }else{
        http_response_code(500);
        printf("<br>debug info:<br>%s<br>%s", json_encode($routes),$path);

    }
}else{
    // 一致するルートがない場合、404エラーを投げる
    http_response_code(404);
    echo "404 Not Found: The requested route was not found on this server.";

    printf("<br>debug info:<br>%s<br>%s", json_encode($routes),$path);
}