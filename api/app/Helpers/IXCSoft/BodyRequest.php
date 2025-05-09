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

    public function FinalizarOS($id)
    {
        $data = [
            'qtype' => 'su_oss_chamado.id_ticket',
            'query' => $id,
            'oper' => '=',
            'page' => '1',
            'rp' => '20',
            'sortname' => 'su_oss_chamado.id',
            'sortorder' => 'desc',
            'grid_param' => json_encode(array(
                array('TB' => 'su_oss_chamado.setor', 'OP' => '=', 'P' => '7'),
                array('TB' => 'su_oss_chamado.id_assunto', 'OP' => 'IN', 'P' => '264,453,358')
            ))
        ];

        return $data;
    }

    public function FinalizarVerificar($id_os, $text_verificar, $id_troca, $id_ixc_user)
    {
        $currentDateTime = date('Y-m-d H:i:s');

        $data = [
            'id_chamado' => $id_os, // ID da O.S
            'data_inicio' => $currentDateTime, // Data e Hora de Início da finalização
            'data_final' => $currentDateTime, // Data e Hora de Final da finalização
            'mensagem' => $text_verificar, // Mensagem
            'gera_comissao' => '', // "N" para Não "S" para Sim
            'id_su_diagnostico' => '', // ID do diagnóstico (não obrigatório)
            'id_proxima_tarefa' => $id_troca,
            'finaliza_processo' => 'N', // "S" para finalizar o processo
            'status' => 'F', // Status "F" para finalizar
            'id_tecnico' => $id_ixc_user // ID do técnico responsável
        ];

        return $data;
    }
}
