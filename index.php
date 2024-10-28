<?php
// ここからルートのマッチングを処理し、ロード、ヘッダー、メタデータ
// などを設定するインデックスファイルを作成することができます。
spl_autoload_extensions(".php");
spl_autoload_register();

$DEBUG = true;

// ルートのロード
$routes = include('Routing/routes.php');

// リクエストURIを解析してパスだけを取得
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

if(isset($routes[$path])){
    $renderer = $routes[$path]();
    
    try{
        // getFields関数の返り値を展開→httpレスポンスの設定をする。その中の1つのキーが有名なContent-typeだったりする
        foreach($renderer->getFields() as $name => $value){
            // ヘッダーに対する単純な検証を実行
            $sanitized_value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

            if($sanitized_value && $sanitized_value === $value){
                header("{$name}: {$sanitized_value}");
            }else{
                // ヘッダー設定に失敗した場合、ログに記録するか処理する
                // エラー処理によっては、例外をスローするか、デフォルトのまま続けることもできる
                http_response_code(500);
                if($DEBUG) print("Failed setting header - '$value', sanitized: '$sanitized_value'");
                exit;
            }

            print($renderer->getContent());
        }
    }catch (Exception $e){
        http_response_code(500);
        print("Internal error, please contact the admin. <br>");
        if($DEBUG) print($e->getMessage());
    }

}else{
    // 一致するルートがない場合、404エラーを投げる
    http_response_code(404);
    echo "404 Not Found: The requested route was not found on this server.";
}