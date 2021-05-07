<?php
    class grilldb{
        private $dbHost = 'mysql-10842-0.cloudclusters.net';
        private $dbPort = '10880';
        private $dbUser = 'lions';
        private $dbPass = 'Kongo2020$';
        private $dbName = 'grilldb';

        function __construct()
        {
            $a = func_get_args();
            $i = func_num_args();
            if (method_exists($this,$f='__construct'.$i)) {
                call_user_func_array(array($this,$f),$a);
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