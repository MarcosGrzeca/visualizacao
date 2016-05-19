<?php

require_once("list.model.php");

class MunicipiosModel extends ResultListModel {

    function listar($psq, $limit = 0) {
        $query = "SELECT * " .
                "FROM cadmun ";
        if (trim($psq) != "") {
            $query .= "WHERE LOWER(munnome) like LOWER('" . $psq . "%') ";
        }
        $query .= "ORDER BY munnome ";
        if ($limit > 0) {
            $query .= " LIMIT $limit";
        }
        $this->result = executaSql($query);
    }

    function obterDadosMunicipioPorCodigo($codigo) {
        $query = "SELECT * " .
                "FROM cadmun " .
                "WHERE muncoddv = '" . $codigo . "' ";
        $this->result = executaSql($query);
    }

    function obterDadosMunicipioPorNomeEUF($nome, $uf) {
        $query = "SELECT m.*, u.ufcod " .
                "FROM cadmun m " .
                "JOIN caduf u on m.ufcod = u.ufcod " .
                "WHERE munnome = '" . $nome . "' ";
        if (trim($uf) != "") {
            $query .= "AND ufsigla = '" . $uf . "'";
        }
        $this->result = executaSql($query);
    }

    function obterCapitais() {
        $query = "SELECT * " .
                "FROM cadmun " .
                "WHERE capital = 'S'";
        $this->result = executaSql($query);
    }

}

?>