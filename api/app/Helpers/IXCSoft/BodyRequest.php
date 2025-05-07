<?php

require_once __DIR__ . "/qtype.php";

class ModelBodyRequest
{
    private $qtypeIXC;


    public function __construct()
    {
        $this->qtypeIXC = new QtypeRiquisicoesIXC();
    }

    public function BodyRequest()
    {
        $data = [
            "qtype" => "su_oss_chamado.tipo",
            "query" => "C",
            "oper" => "=",
            "page" => "1",
            "rp" => "1000",
            "sortname" => "su_oss_chamado.id",
            "sortorder" => "desc"

        ];

        return $data;
    }

    public function BodyRequestModelFinalTecnico($query, $data)
    {
        $data = [
            "qtype" => $this->qtypeIXC->su_chamado_os() . ".id_tecnico",
            "query" => $query,
            "oper" => "=",
            "page" => "1",
            "rp" => "1000",
            "sortname" => $this->qtypeIXC->su_chamado_os() . ".id",
            "sortorder" => "desc",
            "grid_param" => json_encode(array(
                array("TB" => "su_oss_chamado.data_fechamento", "OP" => ">=", "P" => $data . " 00:00:00"),
                array("TB" => "su_oss_chamado.data_fechamento", "OP" => "<=", "P" => $data . " 23:59:59"),
                array("TB" => "su_oss_chamado.tipo", "OP" => "=", "P" => "C"),
                array("TB" => "su_oss_chamado.status", "OP" => "=", "P" => "F")
            ))


        ];

        return $data;
    }

    public function bodyRequestCliente($query)
    {
        $data = [
            "qtype" => $this->qtypeIXC->cliente() . ".id",
            "query" => $query,
            "oper" => "=",
            "page" => "1",
            "rp" => "1000",
            "sortname" => $this->qtypeIXC->cliente() . ".id",
            "sortorder" => "desc"

        ];

        return $data;
    }

    public function BodyRequestArquivo($id_chamado)
    {
        $data = [
            "qtype" => "su_oss_chamado_arquivos.id_oss_chamado",
            "query" => $id_chamado,
            "oper" => "=",
            "page" => "1",
            "rp" => "1000",
            "sortname" => "su_oss_chamado_arquivos.id",
            "sortorder" => "desc"
        ];

        return $data;
    }

    public function BodyRequestLogin($query)
    {
        $data = [
            "qtype" => "radusuarios.id",
            "query" => $query,
            "oper" => "=",
            "page" => "1",
            "rp" => "20",
            "sortname" => "radusuarios.id",
            "sortorder" => "desc"
        ];

        return $data;
    }

    public function BodyRequestONU($query)
    {
        $data = [
            "qtype" => "radpop_radio_cliente_fibra.id_login",
            "query" => $query,
            "oper" => "=",
            "page" => "1",
            "rp" => "1000",
            "sortname" => "radpop_radio_cliente_fibra.id",
            "sortorder" => "desc"
        ];

        return $data;
    }

    public function BodyRequestClienteRadio($query)
    {
        $data = [
            "qtype" => "radpop_radio_cliente.id_radusuarios",
            "query" => $query,
            "oper" => "=",
            "page" => "1",
            "rp" => "1000",
            "sortname" => "radpop_radio_cliente.id",
            "sortorder" => "desc"
        ];

        return $data;
    }
}
