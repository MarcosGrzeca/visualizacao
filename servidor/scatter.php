<?php

require_once("../inc.php");
require_once(SYSTEM_DIR . "servidor/services/dados.php");
$dados = new Dados();
echo $dados->obterScatterParaMunicipio($_REQUEST["codMun"]);
?>