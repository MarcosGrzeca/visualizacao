<?php

set_time_limit(0);	
require_once(SYSTEM_DIR . "servidor/model/listModel/dados.model.php");
require_once(SYSTEM_DIR . "servidor/services/municipio.php");

class Dados {

	function montarMapa($cid, $sexo, $conteudo) {
		$resultado = array();

		$cidInicial = "";
		$cidFinal = "";
		if (trim($cid) != "") {
			$cids = explode("-", $cid);
			$cidInicial = $cids[0];
			$cidFinal = $cids[1];
		}

		$dados = new DadosModel();
		$dados->montarMapa($cidInicial, $cidFinal, $sexo);
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
			$resultado[$obj["codmunres"]]["proporcao"] = ($resultado[$obj["codmunres"]]["ocorrencias"] * 100000) / ($resultado[$obj["codmunres"]]["populacao"] * count($resultado[$obj["codmunres"]]["anos"]));
		}

		$resultado["4314548"] = $resultado["4302105"];

		$maiorIndice = 0;
		$conteudo = trim($conteudo);
		foreach ($resultado as $key => $value) {
			if ($conteudo == "P") {
				if ($value["proporcao"] > $maiorIndice) {
					$maiorIndice = $value["proporcao"];
				}
			} else {
				if ($value["ocorrencias"] > $maiorIndice) {
					$maiorIndice = $value["ocorrencias"];
				}
			}
		}
		foreach ($resultado as $key => $value) {
			if ($conteudo == "P") {
				$resultado[$key]["opacity"] = $value["proporcao"] / $maiorIndice;
			} else {
				$resultado[$key]["opacity"] = $value["ocorrencias"] / $maiorIndice;
			}
		}
		//debug(date("H:i:s.u"), "W");
		return $resultado;
	}

	function obterScatterParaMunicipio($idMunicipio) {
		$dados = new DadosModel();
		$dados->obterScatterParaMunicipio($idMunicipio);

		$nomeArquivo = SYSTEM_DIR . "servidor/dados_scatter.csv";
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

	function obterSequenceParaMunicipio($idMunicipio, $nivel1, $nivel2, $nivel3, $nivel4, $nivel5, $nivel6) {
		$niveis = array($nivel1, $nivel2, $nivel3, $nivel4, $nivel5, $nivel6);
		foreach ($niveis as $key => $value) {
			if (empty($value)) {
				unset($niveis[$key]);
			}
		}
		
		$dados = new DadosModel();
		$dados->obterSequenceParaMunicipio($idMunicipio, $niveis);
		$nomeArquivo = SYSTEM_DIR . "servidor/sequence.csv";
		try {
			@unlink($nomeArquivo);	
		} catch (Exception $e) {
			
		}
		
		$doencas = array();
		while ($obj = $dados->getRegistro()) {
			if (isset($obj["sexo"])) {
				$obj["sexo"] = $this->_getDescricaoSexo($obj["sexo"]);	
			}
			if (isset($obj["idade"])) {
				$obj["idade"] = $this->_getIdade($obj["idade"]);
			}

			if (isset($obj["esc"])) {
				$obj["esc"] = $this->_getEscolariedade($obj["esc"]);
			}
			

			if (isset($obj["causabas"])) {
				if (in_array($obj["causabas"], $doencas)) {
					$obj["causabas"] = $doencas[$obj["causabas"]];
				} else {
					$desc = $this->_getDescricaoCid($obj["causabas"]);
					$doencas[$obj["causabas"]] = $desc;
					$obj["causabas"] = $desc;
				}
			}

			$copy = $obj;
			unset($copy["total"]);
			file_put_contents($nomeArquivo, gerarCvsLinha(array(implode("-", $copy), $obj["total"])), FILE_APPEND);
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

	function _getEscolariedade($esc) {
		switch ($esc) {
			case "1":
				$esc = "Nenhum";
			break;
			case "2": 
				$esc = "1 a 3 anos";
				break;
			case "3": 
				$esc = "4 a 7 anos";
				break;
			case "4": 
				$esc = "5 a 11 anos";
				break;
			case "5": 
				$esc = "12 e mais";
				break;
			default: 
				$esc = "Ignorado";
		}
		return $esc;
	}

	function _getDescricaoCid($cid) {
		$descCid = $cid;
		$dadosCid = new DadosModel();
		$dadosCid->obterDescricaoCid($cid);
		while ($obj = $dadosCid->getRegistro()) {
			$descCid = str_replace("-", " ", trim($obj["descr"]));
		}

		return $descCid;
	}
}
?>