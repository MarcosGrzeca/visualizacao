<?php

require_once("list.model.php");
require_once(SYSTEM_DIR . "/model/classes/faixa.etaria.class.php");

class VisualizacoesModel extends ResultListModel {

    function getDadosVisualizacoes($ufs, $municipios, $sexo, $idadeInicial, $idadeFinal, $ano, $cidInicial, $cidFinal, $linha) {
        set_time_limit(0);

        if ($linha == "grupo_causas") {
            $primeiroTodos = true;
            
            $grupos = array();
            $grupos["A00-B99"] = "Doenças infecciosas e parasitárias";
            $grupos["C00-D48"] = "Neoplasias";
            $grupos["I00-I99"] = "Doenças do aparelho circulatório";
            $grupos["J00-J99"] = "Doenças do aparelho respiratório";
            $grupos["P00-P96"] = "Afecções originadas no período perinatal";
            $grupos["V01-Y98"] = "Causas externas";
            
            $faixas = new FaixaEtaria();
            foreach ($grupos as $key => $grupo) {
                $cids = explode("-", $key);
                $cidInicial = $cids[0];
                $cidFinal = $cids[1];
                
                $primeiro = true;
                $queryTmp = "SELECT count(*) as total, '" . $grupo . "' as valor " .
                        "FROM sim s " .
                        "LEFT JOIN caduf u on s.uf = u.ufcod ";
                if ($ufs != "") {
                    if ($primeiro) {
                        $primeiro = false;
                        $operacao = "WHERE ";
                    } else {
                        $operacao = "AND ";
                    }
                    $queryTmp .= "$operacao uf IN(" . $ufs . ") ";
                }
                if ($municipios != "") {
                    if ($primeiro) {
                        $primeiro = false;
                        $operacao = "WHERE ";
                    } else {
                        $operacao = "AND ";
                    }
                    $queryTmp .= "$operacao codmunres IN(" . $municipios . ") ";
                }
                if ($sexo != "") {
                    if ($primeiro) {
                        $primeiro = false;
                        $operacao = "WHERE ";
                    } else {
                        $operacao = "AND ";
                    }
                    $queryTmp .= "$operacao sexo = '" . $sexo . "' ";
                }
                if ($idadeInicial != "") {
                    if ($primeiro) {
                        $primeiro = false;
                        $operacao = "WHERE ";
                    } else {
                        $operacao = "AND ";
                    }
                    $queryTmp .= "$operacao idade >= '" . $idadeInicial . "' AND idade <= '" . $idadeFinal . "' ";
                }
                if ($ano != "") {
                    if ($primeiro) {
                        $primeiro = false;
                        $operacao = "WHERE ";
                    } else {
                        $operacao = "AND ";
                    }
                    $queryTmp .= "$operacao anobase = '" . $ano . "' ";
                }

                if ($cidInicial != "" && $cidFinal != "") {
                    if ($primeiro) {
                        $primeiro = false;
                        $operacao = "WHERE ";
                    } else {
                        $operacao = "AND ";
                    }
                    $queryTmp .= "$operacao (causabas >= '" . $cidInicial . "' AND causabas <= '" . $cidFinal . "') ";
                }


                if ($primeiroTodos) {
                    $primeiroTodos = false;
                    $query = $queryTmp;
                } else {
                    $query .= " UNION " . $queryTmp;
                }
            }

            $query .= " GROUP by 2";
            $query .= " ORDER by 2";
        } else if ($linha == "faixa") {
            $primeiroTodos = true;
            $faixas = new FaixaEtaria();
            foreach ($faixas->obterFaixas() as $key => $faixaA) {
                if ($faixaA["codigo"] != "") {
                    $faixa = explode("-", $faixaA["codigo"]);
                    $idadeInicial = $faixa[0];
                    $idadeFinal = $faixa[1];
                    if ($idadeInicial == 0) {
                        $idadeInicial = "001";
                    } else if ($idadeInicial > 0) {
                        if ($idadeInicial < 10) {
                            $idadeInicial = 40 . $idadeInicial;
                        } else {
                            $idadeInicial = 4 . $idadeInicial;
                        }
                    }

                    if ($idadeFinal == 0) {
                        $idadeFinal = 400;
                    } else if ($idadeFinal > 80) {
                        $idadeFinal = 599;
                    } else if ($idadeFinal > 0) {
                        if ($idadeFinal < 10) {
                            $idadeFinal = 40 . $idadeFinal;
                        } else {
                            $idadeFinal = 4 . $idadeFinal;
                        }
                    }

                    debug($idadeInicial . " -- " . $idadeFinal);


                    $primeiro = true;
                    $queryTmp = "SELECT count(*) as total, '" . $faixaA["descricao"] . "' as valor, " . $faixaA["codigo"] . " " .
                            "FROM sim s " .
                            "LEFT JOIN caduf u on s.uf = u.ufcod ";
                    if ($ufs != "") {
                        if ($primeiro) {
                            $primeiro = false;
                            $operacao = "WHERE ";
                        } else {
                            $operacao = "AND ";
                        }
                        $queryTmp .= "$operacao uf IN(" . $ufs . ") ";
                    }
                    if ($municipios != "") {
                        if ($primeiro) {
                            $primeiro = false;
                            $operacao = "WHERE ";
                        } else {
                            $operacao = "AND ";
                        }
                        $queryTmp .= "$operacao codmunres IN(" . $municipios . ") ";
                    }
                    if ($sexo != "") {
                        if ($primeiro) {
                            $primeiro = false;
                            $operacao = "WHERE ";
                        } else {
                            $operacao = "AND ";
                        }
                        $queryTmp .= "$operacao sexo = '" . $sexo . "' ";
                    }
                    if ($idadeInicial != "") {
                        if ($primeiro) {
                            $primeiro = false;
                            $operacao = "WHERE ";
                        } else {
                            $operacao = "AND ";
                        }
                        $queryTmp .= "$operacao idade >= '" . $idadeInicial . "' AND idade <= '" . $idadeFinal . "' ";
                    }
                    if ($ano != "") {
                        if ($primeiro) {
                            $primeiro = false;
                            $operacao = "WHERE ";
                        } else {
                            $operacao = "AND ";
                        }
                        $queryTmp .= "$operacao anobase = '" . $ano . "' ";
                    }

                    if ($cidInicial != "" && $cidFinal != "") {
                        if ($primeiro) {
                            $primeiro = false;
                            $operacao = "WHERE ";
                        } else {
                            $operacao = "AND ";
                        }
                        $queryTmp .= "$operacao (causabas >= '" . $cidInicial . "' AND causabas <= '" . $cidFinal . "') ";
                    }


                    if ($primeiroTodos) {
                        $primeiroTodos = false;
                        $query = $queryTmp;
                    } else {
                        $query .= " UNION " . $queryTmp;
                    }
                }
            }

            $query .= " GROUP by 2";
            $query .= " ORDER by 3 DESC";
        } else {

            $primeiro = true;
            $query = "SELECT count(*) as total, " . $linha . " as valor " .
                    "FROM sim s " .
                    "LEFT JOIN caduf u on s.uf = u.ufcod ";
            if ($ufs != "") {
                if ($primeiro) {
                    $primeiro = false;
                    $operacao = "WHERE ";
                } else {
                    $operacao = "AND ";
                }
                $query .= "$operacao uf IN(" . $ufs . ") ";
            }
            if ($municipios != "") {
                if ($primeiro) {
                    $primeiro = false;
                    $operacao = "WHERE ";
                } else {
                    $operacao = "AND ";
                }
                $query .= "$operacao codmunres IN(" . $municipios . ") ";
            }
            if ($sexo != "") {
                if ($primeiro) {
                    $primeiro = false;
                    $operacao = "WHERE ";
                } else {
                    $operacao = "AND ";
                }
                $query .= "$operacao sexo = '" . $sexo . "' ";
            }
            if ($idadeInicial != "") {
                if ($primeiro) {
                    $primeiro = false;
                    $operacao = "WHERE ";
                } else {
                    $operacao = "AND ";
                }
                $query .= "$operacao idade >= '" . $idadeInicial . "' AND idade <= '" . $idadeFinal . "' ";
            }
            if ($ano != "") {
                if ($primeiro) {
                    $primeiro = false;
                    $operacao = "WHERE ";
                } else {
                    $operacao = "AND ";
                }
                $query .= "$operacao anobase = '" . $ano . "' ";
            }

            if ($cidInicial != "" && $cidFinal != "") {
                if ($primeiro) {
                    $primeiro = false;
                    $operacao = "WHERE ";
                } else {
                    $operacao = "AND ";
                }
                $query .= "$operacao (causabas >= '" . $cidInicial . "' AND causabas <= '" . $cidFinal . "') ";
            }

            $query .= " GROUP by " . $linha;
            $query .= " ORDER by " . $linha;
        }
        debug($query);
        $this->result = executaSql($query);
    }

}

?>