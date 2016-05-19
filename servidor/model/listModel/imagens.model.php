<?php

require_once("list.model.php");

class ImagensModel extends ResultListModel {
    
    function listar() {
        $query =    "SELECT * " .
                    "FROM imagem ";
        $this->result = executaSql($query);  
    }
}
?>