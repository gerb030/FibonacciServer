<?php

class DbFactory
{
    /**
     * @static
     * @return PDO
     */
    public static function build()
    {
        $dbname  = Config::instance()->getOption('db_database');
        $host    = Config::instance()->getOption('db_hostname');
        $user    = Config::instance()->getOption('db_app_user');
        $pass    = Config::instance()->getOption('db_app_password');
        try {
            $db = new PDO(
                'mysql:host='.$host.';dbname='.$dbname,
                (string) $user,
                (string) $pass,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            throw $e;
        }
        return $db;
    }
}