<?

require_once("../inc.php");

/*
SELECT municipio, sexo, total from ibge WHERE municipio = '430210' AND anobase = (SELECT max(anobase) from ibge WHERE municipio = '430210') GROUP by municipio, sexo, total;*/

if (method_exists($login, $_REQUEST["func"])) {
	echo json_encode(call_user_func_array(array($login, $_REQUEST["func"]) , $_REQUEST["parameters"]));
}

class Municipio() {
	function obterPopulacaoTotalMunicipio($codigoMunicipio) {

	}
}
?>