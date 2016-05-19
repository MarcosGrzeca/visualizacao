<?php

require_once("list.model.php");

class MunicipiosModel extends ResultListModel {

    function obterPopulacaoTotalMunicipio($codigoMunicipio) {
        $query = "SELECT municipio, sexo, total " .
                "FROM ibge WHERE municipio = '" . $codigoMunicipio . "' 
                AND anobase = (
                    SELECT max(anobase) 
                    FROM ibge 
                    WHERE municipio = '" . $codigoMunicipio . "') 
                GROUP by municipio, sexo, total";
        $this->result = executaSql($query);
    }
}

?>