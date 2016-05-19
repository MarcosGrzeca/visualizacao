<?php

class Conexao {

    private $host;
    private $port;
    private $db;
    private $user;
    private $password;


    function __construct() {
        $this->host = "localhost";
        $this->port = 5432;
        $this->db = "postgres";
        $this->user = "postgres";
        $this->password = "imortal";
        $this->conectar();
     }
    
    function conectar() {
        $con_string = pg_connect("host=" . $this->host . " port=" . $this->port . " dbname=" . $this->db . " user=" . $this->user . " password=" . $this->password) or die ("Não foi possível conectar ao BD");   
    }
    
    function query($query) {
        $result = pg_query($query) or die('Query failed: ' . pg_last_error());
        return $result;
    }
}
?>