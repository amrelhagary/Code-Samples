<?php
/**
 * sqlite database
 * @author: amr
 */

class DB
{
    private static $instance;
    private static $conn;
    private static $dbname;
    private static $logExist;

    public function __construct($dbName)
    {
        self::$dbname = $dbName;
        self::$conn = $this->connect();
        self::init();
    }

    public static function getInstance($dbName)
    {
        if(is_null(self::$instance))
        {
            self::$instance = new self($dbName);
        }
        return self::$instance;
    }

    private function connect()
    {
        if(file_exists(self::$dbname)){
            $conn = sqlite_open(self::$dbname,0666,$error);
            if(!$conn)
                throw new Exception($error);
        }else{
            throw new Exception("Database file not found");
        }

        return $conn;
    }

    public function query($sql)
    {
        $errors = '';
        $result = @sqlite_query($sql,self::$conn,SQLITE_BOTH,$errors);

        // sql syntax error
        if(!empty($errors)){
            throw new Exception(sqlite_error_string($errors));
        }

        if($error = sqlite_last_error(self::$conn)){
            throw new Exception(sqlite_error_string($error));
        }

        if(sqlite_num_rows($result) > 0){
            return sqlite_fetch_all($result,SQLITE_ASSOC);
        }
    }

    private function init()
    {
        if(self::$logExist === true)
        {
            return;
        }

        try{
            self::$conn = $this->connect();
        }catch (Exception $e){
            echo $e->getMessage();
        }

        $query= sqlite_query("SELECT name FROM sqlite_master WHERE type='table' AND name='logs'",self::$conn);
        if(sqlite_num_rows($query) == 0 )
        {
            sqlite_exec(self::$conn,$this->getInitSql());
            if($error = sqlite_last_error(self::$conn)){
                throw new Exception(sqlite_error_string($error));
            }else{
                self::$logExist = true;
            }
        }else{
            self::$logExist = true;
        }
    }

    private function getInitSql()
    {
        return  'CREATE TABLE logs(
                id INTEGER NOT NULL PRIMARY KEY,
                url VARCHAR(200),
                code VARCHAR(20),
                type VARCHAR(20),
                start_time CURRENT_TIMESTAMP,
                end_time CURRENT_TIMESTAMP,
                condition VARCHAR(10),
                condition_result VARCHAR(100),
                http_status_code INT,
                response_text TEXT,
                curl_info TEXT
                )';
    }

    public function __destruct()
    {
        sqlite_close(self::$conn);
        self::$instance = null;
    }
}