<?php
function executaSql($query) {
	$conexao = new Conexao();
	$result = pg_query($query) or die('Query failed: ' . pg_last_error());
	$result = $conexao->query($query);
	return $result;
}

function debug($msg, $tipo = ""){
    switch ($tipo) {
    	case "I":
    	FB::info($msg);
    	break;
    	case "W":
    	FB::warn($msg);
    	break;
    	case "E":
    	FB::error($msg);
    	break;
    	case "T":
    	FB::table("Table", $msg);
    	break;
    	default:
    	FB::log($msg);
    }   
}

?>