<?php
    class db{
        private $dbHost = 'mysql-10842-0.cloudclusters.net';
        private $dbPort = '10880';
        private $dbUser = 'lions';
        private $dbPass = 'Kongo2020$';
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

        public function dbConnection() {
        $link = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName, $this->dbPort);

        if (!$link) {
            echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
            echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
            echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }        
        return $link;
        }
    }