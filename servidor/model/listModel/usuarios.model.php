<?php

require_once("list.model.php");

class UsuariosModel extends ResultListModel {
    
    function logar($usuario, $senha) {
        $query =  "SELECT * " .
                "FROM usuario " .
                "WHERE login = '" . $usuario . "' " .
                "AND senha = '" . $senha . "'";
        $this->result = executaSql($query);
    }
    
    function listar($psq, $ordenacao = "") {
        $query =    "SELECT * " .
                    "FROM usuario ";
        if (trim($psq) != "") {
            $query .=   "WHERE (" .
                            "lower(login) like ('" . strtolower($psq) . "') " .
                            "OR " .
                            "lower(nome) like ('" . strtolower($psq) . "%')) ";
        }
        if ($ordenacao == "D") {
           $query .= "ORDER BY id DESC";    
        } else {
            $query .= "ORDER BY id";
        }
        debug($query);
        $this->result = executaSql($query);  
    }
    
    function pesquisarPorLogin($usuario) {
        $query =  "SELECT * " .
                "FROM usuario " .
                "WHERE login = '" . $usuario . "' ";
        $this->result = executaSql($query);
    }
}
?>