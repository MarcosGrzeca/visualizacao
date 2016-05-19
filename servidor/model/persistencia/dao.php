<?php

class Persistence {

    public $result;
    public $temId = false;

    function salvar($id, $dados) {
        if ($id > 0) {
            $this->alterar($id, $dados);
        } else {
            $this->criar($dados);
        }
    }

    function alterar($id, $dados) {
        $campos = array();
        $valores = array();
        $updates = "";
        foreach ($this->atributos as $key => $value) {
            if (isset($dados[$value])) {
                $campos[] = $value;
                $valores[] = "'" . pg_escape_string($dados[$value]) . "'";
                if ($updates == "") {
                    $updates = " $value = '" . pg_escape_string($dados[$value]) . "'";
                } else {
                    $updates .= ", $value = '" . pg_escape_string($dados[$value]) . "'";
                }
            }
        }

        $campos = implode(", ", $campos);
        $valores = implode(", ", $valores);
        $sql = "UPDATE " . $this->tabela . " SET $updates WHERE id = '" . $id . "'";
        debug($sql);
        $this->query($sql);
        return $id;
    }

    function criar($dados) {
        $campos = array();
        $valores = array();

        foreach ($this->atributos as $key => $value) {
            if (isset($dados[$value])) {
                $campos[] = $value;
                $valores[] = "'" . pg_escape_string($dados[$value]) . "'";
            }
        }

        $campos = implode(", ", $campos);
        $valores = implode(", ", $valores);
        debug($this->atributos);
        if ($this->temId == true) {
            $sql = "INSERT INTO " . $this->tabela . "(" . $campos . ") VALUES (" . $valores . ") RETURNING id;";
            debug($sql);
            $id = $this->query($sql);
            return $id;
        } else {
            $sql = "INSERT INTO " . $this->tabela . "(" . $campos . ") VALUES (" . $valores . ")";
            try {
                $this->query($sql);
            } catch (Exception $ex) {
                die("Ocorreu um erro com SQL " . $sql);
            }
            return -1;
        }
    }

    function obter($id) {
        $sql = "SELECT * " .
                "FROM " . $this->tabela . " " .
                "WHERE id = '" . $id . "'";
        $this->query($sql);
        return pg_fetch_array($this->result, null, PGSQL_ASSOC);
    }

    function obterSim($numero, $uf, $ano) {
        $sql = "SELECT * " .
                "FROM " . $this->tabela . " " .
                "WHERE numerodo = '" . $numero . "' " .
                "AND uf = '" . $uf . "' " .
                "AND anobase = '" . $ano ."' ";
        $this->query($sql);
        return pg_fetch_array($this->result, null, PGSQL_ASSOC);
    }

    function excluir($id) {
        $sql = "DELETE FROM " . $this->tabela . " WHERE id = '" . $id . "';";
        $this->query($sql);
    }

    function query($query) {
        $query = trim($query);
        $this->result = pg_query($query) or die('Query failed: ' . pg_last_error());
        if (substr($query, 0, 11) == "INSERT INTO") {
            $retorno = pg_fetch_array($this->result, null, PGSQL_ASSOC);
            return $retorno["id"];
        }
    }

}
