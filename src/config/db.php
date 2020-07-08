<?php
    class db{
        private $dbHost = 'localhost';
        private $dbUser = 'root';
        private $dbPass = '';
        private $dbName = '';

        function __construct()
        {
            $a = func_get_args();
            $i = func_num_args();
            if (method_exists($this,$f='__construct'.$i)) {
                call_user_func_array(array($this,$f),$a);
            }
        }
       
        function __construct1($a1)
        {
            if($a1 == 1){
                $this->dbName = 'lionsdbcrc';
            }else if($a1 == 2){
                $this->dbName = 'lionsdbhon';
            }else if($a1 == 3){
                $this->dbName = 'lionsdbpan';
            }else {
                echo "fallo comparacion";
                die();
            }
        }

        //conexión
        public function dbConnection() {
            $mysqlConnect = "mysql:host = $this->dbHost;dbname=$this->dbName";
            $dbConnection = new PDO($mysqlConnect, $this->dbUser, $this->dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbConnection;
        }
    }