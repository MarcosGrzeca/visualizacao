<?php

require_once("list.model.php");

class DadosModel extends ResultListModel {

	function montarMapa($cidInicial, $cidFinal,$sexo, $anoBase = "") {
		$query = "SELECT  s.codmunres, count(s.*) as ocorrencias, s.anobase, mun.munnome " .
		"FROM sim_rs s ". 
		"JOIN cadmun mun on mun.muncoddv = s.codmunres " .
		"WHERE s.codmunres <> '4300000' ";

		//$query .= "AND s.codmunres  IN ('4302105', '4320230', '4302303', '4300034','4300059','4300109','4300208','4300406','4300471', '4300802') ";

		if (trim($sexo) != "") {
			$query .= "AND sexo = '" . $sexo . "' ";
		}

		if (trim($anoBase) != "") {
			$query .= "AND anoBase = '" . $anoBase . "' ";
		}

		if (trim($cidInicial) != "" && trim($cidFinal) != "") {
			$query .= "AND (causabas >= '" . $cidInicial . "' AND causabas <= '" . $cidFinal . "') ";
		}
		$query .= "GROUP BY s.codmunres, s.anobase, mun.munnome ";
		$query .= "ORDER BY s.codmunres, s.anobase";
		$this->result = executaSql($query);  
	}

	function obterScatterParaMunicipio($idMunicipio) {
		$query = "SELECT dtobito, sexo, causabas, anobase, idade " .
		"FROM sim_rs ". 
		"WHERE codmunres = '" . $idMunicipio . "' ";
		$this->result = executaSql($query);  
	}

	function obterSequenceParaMunicipio($idMunicipio, $niveis) {

		$query = "SELECT " . implode(", ", $niveis) . ", count(*) as total " . 
		"FROM sim_rs ". 
		"WHERE codmunres = '" . $idMunicipio . "' " .
		"GROUP by " . implode(", ", $niveis);
		$this->result = executaSql($query);
	}

	function obterParallelParaMunicipio($idMunicipio) {
		$query = "SELECT anobase, esc, idade, sexo " . 
		"FROM sim_rs ". 
		"WHERE codmunres = '" . $idMunicipio . "' ";
		$this->result = executaSql($query);	
	} 

	function obterParalelaCategoricaParaMunicipio($idMunicipio, $niveis) {
		$query = "SELECT " . implode(", ", $niveis) .  " " .
				"FROM sim_rs ". 
				"WHERE codmunres = '" . $idMunicipio . "' ";
		$this->result = executaSql($query);
	}

	function obterDescricaoCid($cid) {
		$query = "SELECT descr " .
				"FROM cid10 " .
				"WHERE cid10 = '" . $cid . "' ";
		$this->result = executaSql($query);
	}
}
?>