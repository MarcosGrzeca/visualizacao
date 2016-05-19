<?php

class ResultListModel {
    
    public $result;
    
    function getRegistro() {
        $registro = pg_fetch_array($this->result, null, PGSQL_ASSOC);
        return $registro;
    }
    
    function getObjetoRegistro() {
        $registro = pg_fetch_object($this->result, null);
        return $registro;
    }
    
    function getNumeroRegistros() {
        $registro = pg_num_rows($this->result);
        return $registro;
    }
}
?>