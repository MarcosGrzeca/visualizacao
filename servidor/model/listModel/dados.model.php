<?php

require_once("list.model.php");

class DadosModel extends ResultListModel {

	function montarMapa($cid) {
		$query = "SELECT  s.codmunres, count(s.*) as ocorrencias, s.anobase, mun.munnome " .
		"FROM sim_rs s ". 
		"JOIN cadmun mun on mun.muncoddv = s.codmunres " .
		"WHERE s.codmunres <> '4300000' ". 
		"GROUP BY s.codmunres, s.anobase, mun.munnome ";
		//"LIMIT 100";
		$this->result = executaSql($query);  
	}

	function obterScatterParaMunicipio($idMunicipio) {
		$query = "SELECT dtobito, sexo, causabas, anobase, idade " .
			 "FROM sim_rs ". 
			 "WHERE codmunres = '" . $idMunicipio . "' ";
			 //"LIMIT 10";
		debug($query);
		$this->result = executaSql($query);  
	}
}
?>