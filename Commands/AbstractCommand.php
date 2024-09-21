<?php
// 全てのコマンドの基底となる抽象コマンドクラス
// 新しいコマンドは単に AbstractCommand を拡張し、そのコマンド名と引数オプションを定義し、コマンドの核となる実行内容を設定する
// この抽象コマンドは、子クラスに stdout に出力する log() メソッドや引数オプションを取得する方法など、使いやすいヘルパーメソッドを提供する
// このクラスの主な仕事は、シェルを通じて渡されたすべての引数を解析し、コマンドが定義するすべての引数と値のペアのハッシュマップを設定すること
namespace Commands;

use Exception;

abstract class AbstractCommand implements Command
{
    protected ?string $value;
    protected array $argsMap = [];
    protected static ?string $alias = null;

    protected static bool $requiredCommandValue = false;

    /**
     * @throws Exception
     */
    public function __construct(){
        $this->setUpArgsMap();
    }

    /*
     * シェルからすべての引数を読み込み、それをこのクラスのgetArguments()と整列するハッシュマップを作成
     * このargsMapは getArgumentValue()のために使用される
     * すべての引数は短縮バージョンでは'-'で、完全なバージョンでは'--'で始まる
     */

    private function setUpArgsMap(): void{
        //オリジナルのマッピングを設定
        $args = $GLOBALS['argv'];
        // エイリアスのインデックスが見つかるまで探索 array_searchはfind_index的な存在
        $startIndex  = array_search($this->getAlias(), $args);

        if($startIndex === false) throw new Exception(sprintf("Could not find alias %s", $this->getAlias()));
        else $startIndex++;

        $shellArgs = [];

        // メインコマンドの値である初期値を取得
        if(!isset($args[$startIndex]) || ($args[$startIndex][0] === '-')){
            if($this->isCommandValueRequired()) throw new Exception(sprintf("%s's value is required.", $this->getAlias()));
        }
        else{
            $this->argsMap[$this->getAlias()] = $args[$startIndex];
            $startIndex++;
        }

        // すべての引数を$argsハッシュに格納
        for($i = $startIndex; $i < count($args); $i++){
            $arg = $args[$i];

            if($arg[0].$arg[1] === '--') $key = substr($arg,2);
            else if($arg[0] === '-') $key = substr($arg,1);
            else throw new Exception('Options must start with - or --');

            $shellArgs[$key] = true;

            // 次のargsエントリがオプションでない場合は、引数値となる(iも同様にインクリメント)
            if(isset($args[$i+1]) && $args[$i+1] !== '-') {
                $shellArgs[$key] = $args[$i+1];
                $i++;
            }
        }

        // このコマンドの引数マップを設定
        foreach ($this->getArguments() as $argument) {
            $argString = $argument->getArgument();
            $value = null;

            if($argument->isShortAllowed() && isset($shellArgs[$argString[0]])) $value = $shellArgs[$argString[0]];
            else if(isset($shellArgs[$argString])) $value = $shellArgs[$argString];

            if($value === null){
                if($argument->isRequired()) throw new Exception(sprintf('Could not find the required argument %s', $argString));
                else $this->argsMap[$argString] = false;
            }
            else $this->argsMap[$argString] = $value;
        }

        $this->log(json_encode($this->argsMap));
    }

    public static function getHelp(): string
    {
        $helpString = "Command: " . static::getAlias() . (static::isCommandValueRequired()?" {value}":"") . PHP_EOL;

        $arguments = static::getArguments();
        if(empty($arguments)) return $helpString;

        $helpString .= "Arguments:" . PHP_EOL;

        foreach ($arguments as $argument) {
            $helpString .= "  --" . $argument->getArgument();  // long argument name
            if ($argument->isShortAllowed()) {
                $helpString .= " (-" . $argument->getArgument()[0] . ")";  // short argument name
            }
            $helpString .= ": " . $argument->getDescription();
            $helpString .= $argument->isRequired() ? " (Required)" : "(Optional)";
            $helpString .= PHP_EOL;
        }

        return $helpString;
    }

    public static function getAlias(): string
    {
        // staticはselfと比べて遅延バインディングを行い、子クラスが$aliasをオーバーライドするとその値を使用。
        // selfは常にこのクラスの値($alias = null)を使用。
        return static::$alias !== null ? static::$alias : static::class;
    }

    public static function isCommandValueRequired(): bool{
        return static::$requiredCommandValue;
    }

    public function getCommandValue(): string{
        return $this->argsMap[static::getAlias()]??"";
    }

    // 引数の値の文字列を返し、存在するが値が設定されていない場合はtrue、存在しない場合はfalseを返す
    public function getArgumentValue(string $arg): bool|string
    {
        return $this->argsMap[$arg];
    }

    // 子コマンドにログを取る方法を提供
    protected function log(string $info): void
    {
        fwrite(STDOUT, $info . PHP_EOL);
    }

    /** @return Argument[]  */
    public abstract static function getArguments(): array;
    public abstract function execute(): int;
}