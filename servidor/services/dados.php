<?php

require_once(SYSTEM_DIR . "servidor/model/listModel/dados.model.php");
require_once(SYSTEM_DIR . "servidor/services/municipio.php");

class Dados {

	function montarMapa($cid) {
		$resultado = array();
		$dados = new DadosModel();
		$dados->montarMapa($cid);

		$municipio = new Municipio();
		
		while ($obj = $dados->getRegistro()) {
			if (!isset($resultado[$obj["codmunres"]])) {
				$resultado[$obj["codmunres"]] = array("ocorrencias" => 0, "anos" => array());
				$resultado[$obj["codmunres"]]["nome"] = trim($obj["munnome"]);
				$resultado[$obj["codmunres"]]["populacao"] = $municipio->obterPopulacaoTotalMunicipio($obj["codmunres"]);
			}
			$resultado[$obj["codmunres"]]["ocorrencias"] += $obj["ocorrencias"];
			$resultado[$obj["codmunres"]]["anos"][$obj["anobase"]] = $obj["ocorrencias"];
			$resultado[$obj["codmunres"]]["opacity"] = 0;
			//$resultado[$obj["codmunres"]]["proporcao"] = $resultado[$obj["codmunres"]]["ocorrencias"] / $resultado[$obj["codmunres"]]["populacao"];
			$resultado[$obj["codmunres"]]["proporcao"] = ($resultado[$obj["codmunres"]]["ocorrencias"] * 100000) / $resultado[$obj["codmunres"]]["populacao"];
		}
														



		debug($resultado);
		return $resultado;
	}
}

?>