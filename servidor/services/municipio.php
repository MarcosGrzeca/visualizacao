<?php

require_once(SYSTEM_DIR . "servidor/model/listModel/municipios.model.php");


/*
*/

/*if (method_exists($login, $_REQUEST["func"])) {
	echo json_encode(call_user_func_array(array($login, $_REQUEST["func"]) , $_REQUEST["parameters"]));
}*/

class Municipio {
	function obterPopulacaoTotalMunicipio($codigoMunicipio) {
		if (strlen($codigoMunicipio) >6) {
			$codigoMunicipio = substr($codigoMunicipio, 0, 6);
		}

		$total = 0;
		$municipios = new MunicipiosModel();
		$municipios->obterPopulacaoTotalMunicipio($codigoMunicipio);
		while ($obj = $municipios->getRegistro()) {
			$total += $obj["total"];
		}
		return $total;
	}
}
?>