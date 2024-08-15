<?php
namespace Helpers;

use Exceptions\ReadAndParseEnvException;

class Settings{
    private const ENV_PATH = '.env';

    public static function env(string $pair): string{
        // dirnameは指定したファイルの親パスを返してくれる。デフォルトは1。今回.envファイルに行き着くためには2と指定
        $config = parse_ini_file( dirname(__FILE__, 2) . '/' . self::ENV_PATH);

        if($config === false){
            throw new ReadAndParseEnvException();
        }
        return $config[$pair];
    }

    
}