<?php

require_once("list.model.php");

class DadosModel extends ResultListModel {

	function montarMapa($cidInicial, $cidFinal,$sexo) {
		$query = "SELECT  s.codmunres, count(s.*) as ocorrencias, s.anobase, mun.munnome " .
		"FROM sim_rs s ". 
		"JOIN cadmun mun on mun.muncoddv = s.codmunres " .
		"WHERE s.codmunres <> '4300000' ";

		if (trim($sexo) != "") {
			$query .= "AND sexo = '" . $sexo . "' ";
		}

		if (trim($cidInicial) != "" && trim($cidFinal) != "") {
			$query .= "AND (causabas >= '" . $cidInicial . "' AND causabas <= '" . $cidFinal . "') ";
		}
		$query .= "GROUP BY s.codmunres, s.anobase, mun.munnome ";
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

	function obterDescricaoCid($cid) {
		$query = "SELECT descr " .
				"FROM cid10 " .
				"WHERE cid10 = '" . $cid . "' ";
		$this->result = executaSql($query);
	}
}
?>