<?php
namespace Commands\Programs;

use Helpers\Settings;

class Mysql_config{
    public static $mysql_username;
    public static $mysql_password;

    public static function init(){
        self::$mysql_username = Settings::env("DATABASE_USER");
        self::$mysql_password = Settings::env("DATABASE_USER_PASSWORD");
    }
}

Mysql_config::init();
