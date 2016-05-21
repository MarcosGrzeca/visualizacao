<?php

set_time_limit(0);	
require_once(SYSTEM_DIR . "servidor/model/listModel/dados.model.php");
require_once(SYSTEM_DIR . "servidor/services/municipio.php");

class Dados {

	function montarMapa($cid) {
		$resultado = array();
		debug(date("H:i:s.u"));
		$dados = new DadosModel();
		$dados->montarMapa($cid);
		debug(date("H:i:s.u"), "I");
		$municipio = new Municipio();
		$maximaProporcao = 0;		
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
			$resultado[$obj["codmunres"]]["proporcao"] = ($resultado[$obj["codmunres"]]["ocorrencias"] * 100000) / ($resultado[$obj["codmunres"]]["populacao"] * count($resultado[$obj["codmunres"]]["anos"]));
		}

		$resultado["4314548"] = $resultado["4302105"];
		


		$maiorIndice = 0;
		foreach ($resultado as $key => $value) {
			if ($value["proporcao"] > $maiorIndice) {
				$maiorIndice = $value["proporcao"];
			}
		}
		foreach ($resultado as $key => $value) {
			$resultado[$key]["opacity"] = $value["proporcao"] / $maiorIndice;
		}

		debug(date("H:i:s.u"), "W");
		debug($resultado);
		debug(count($resultado));
		return $resultado;
	}
}

?>