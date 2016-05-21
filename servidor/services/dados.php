<?php

set_time_limit(0);	
require_once(SYSTEM_DIR . "servidor/model/listModel/dados.model.php");
require_once(SYSTEM_DIR . "servidor/services/municipio.php");

class Dados {

	function montarMapa($cid) {
		$resultado = array();
		$dados = new DadosModel();
		$dados->montarMapa($cid);
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
		/*
		debug(date("H:i:s.u"), "W");
		debug($resultado);
		debug(count($resultado));*/
		return $resultado;
	}

	function obterScatterParaMunicipio($idMunicipio) {
		$dados = new DadosModel();
		$dados->obterScatterParaMunicipio($idMunicipio);

		$nomeArquivo = SYSTEM_DIR . "servidor/dados_scatter.csv";
		debug($nomeArquivo);
		try {
			@unlink($nomeArquivo);	
		} catch (Exception $e) {
			
		}
		$obj = array("Data Óbito", "Sexo", "CID", "Ano", "Idade");
		file_put_contents($nomeArquivo, gerarCvsLinha($obj), FILE_APPEND);
		while ($obj = $dados->getRegistro()) {
			$obj["sexo"] = $this->_getDescricaoSexo($obj["sexo"]);
			$obj["idade"] = $this->_getIdade($obj["idade"]);
			file_put_contents($nomeArquivo, gerarCvsLinha($obj), FILE_APPEND);
		}
	}

	function _getDescricaoSexo($sexo) {
		switch ($sexo) {
			case '1':
				$sexoDesc = "Masculino";
				break;
			case '2' :
				$sexoDesc = "Feminino";
				break;
			default:
				$sexoDesc = "Não informado";
				break;
		}
		return $sexoDesc;
	}

	function _getIdade($idade) {
		$idadeCalculada = 0;
		$digito = substr($idade, 0, 1);
		if ($idade > 400) {
			$idadeCalculada = $idade - 400;
		} else if ($idade > 300) {
			$idadeCalculada = ($idade - 300) / 100;
		}
		return $idadeCalculada;
	}
}
?>