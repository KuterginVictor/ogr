<?php

class DB
{
    /**
     * Instance variable.
     * @var null
     */
    public static $instance = null;

    /**
     * DB constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Get database Instance
     *
     * @return PDO|null
     * @throws Exception
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {

            //Database Config
            $db_info = [
                "db_host" => "mysql",
                "db_port" => "3306",
                "db_user" => "chess",
                "db_pass" => "pass",
                "db_name" => "chess",
            ];

            try {
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ];
                self::$instance = new PDO("mysql:host=".$db_info['db_host'].';port='.$db_info['db_port'].';dbname='.$db_info['db_name'],
                    $db_info['db_user'], $db_info['db_pass'], $options);
                self::$instance->exec("set names utf8");
            } catch (PDOException $error) {
                throw new Exception("Database error: ".$error->getMessage(), 500);
            }
        }
        return self::$instance;
    }

    /**
     * Prevent Clone.
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialize.
     */
    private function __wakeup()
    {
    }
}