<?php

require_once("list.model.php");

class DadosModel extends ResultListModel {

    function listar($psq, $paginacao, $nroRegistros = 20, $exibirTodosCampos = false) {
        if ($exibirTodosCampos) {
            $query = "SELECT s.* ";
        } else {
            $query = "SELECT s.numerodo, s.dtobito, s.causabas, s.anobase, s.uf, cuf.ufsigla ";
        }
        $query .=  "FROM sim s ". 
                  "LEFT JOIN caduf cuf on cuf.ufcod = s.uf ";
        if (trim($psq) != "") {
            $query .=   "WHERE (" .
                            "numerodo = '" . $psq . "') ";
        }
        $query .= "ORDER by s.uf, s.anobase, s.numerodo ";
        if ($paginacao > 0) {
            $query .= "LIMIT " . $nroRegistros . " OFFSET " . ($paginacao - 1) * 20;
        }
        $this->result = executaSql($query);  
    }
    
    function obterDadosExportacao($psq, $paginacao, $nroRegistros = 20) {
        $query = "SELECT s.*, cuf.ufsigla, m.munnome " .
                 "FROM sim s ". 
                 "LEFT JOIN cadmun m on m.muncoddv = s.codmunocor " .
                 "LEFT JOIN caduf cuf on cuf.ufcod = s.uf ";
        if (trim($psq) != "") {
            $query .=   "WHERE (" .
                            "numerodo = '" . $psq . "') ";
        }
        if ($paginacao > 0) {
            $query .= "LIMIT " . $nroRegistros . " OFFSET " . ($paginacao - 1) * $nroRegistros;
        }
        $this->result = executaSql($query);  
    }
    
    function obterTotalRegistros($psq) {
        $query =    "SELECT count(*) as total " .
                    "FROM sim ";
        if (trim($psq) != "") {
            $query .=   "WHERE (" .
                            "numerodo = '" . strtolower(

        $psq) . "') ";
        }
        $this->result = executaSql($query);  
    }
}
?>