<?php

require_once("../inc.php");
require_once(SYSTEM_DIR . "servidor/services/dados.php");
$dados = new Dados();
echo $dados->obterParalelaCategoricaParaMunicipio($_REQUEST["codMun"], $_REQUEST["nivel1"], $_REQUEST["nivel2"], $_REQUEST["nivel3"], $_REQUEST["nivel4"], $_REQUEST["nivel5"]) ?>