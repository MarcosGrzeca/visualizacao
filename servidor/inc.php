<?php

ini_set("default_charset", "utf-8");
define("BASE_DIR", "b=visualizacao&amp;");
define("SYSTEM_DIR", dirname(__FILE__) . "/");
define("SERVER_DIR", 'localhost/visualizacao/');

define("HTTP_DIR", "http://" . SERVER_DIR);

error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('America/Sao_Paulo');

require_once(SYSTEM_DIR . "servidor/utils/FirePHPCore/fb.php");
require_once(SYSTEM_DIR . "servidor/utils/functions.php");
require_once(SYSTEM_DIR . "model/persistencia/conexao.php");

$conexao = new Conexao();
?>