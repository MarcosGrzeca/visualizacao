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

function gerarCvs($array){
    $csv = "";
    $csv .= chr(0xEF).chr(0xBB).chr(0xBF);
    foreach($array as $linha){
        $linhaCsv = "";
        foreach($linha as $value){
            if($linhaCsv != ""){
                $linhaCsv .= ";";
            }
            $linhaCsv .= '"'.$value.'"';
        }
        $csv .= $linhaCsv.chr(13).chr(10);
    }
    return $csv;
}  
function gerarCvsLinha($linha){
    $csv = "";
    //$csv .= chr(0xEF).chr(0xBB).chr(0xBF);
    $linhaCsv ="";
    foreach($linha as $value){
        if($linhaCsv != ""){
            $linhaCsv .= ",";
        }
        $linhaCsv .= '"'.$value.'"';
    }
    
    $csv .= $linhaCsv.chr(13).chr(10);
    return $csv;
}
?>