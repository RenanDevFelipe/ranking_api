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

    public function BodyRequestLoginIpAux($query)
    {
        $data = [
            "qtype" => "radusuarios.ip_aux",
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

    public function BodyRequestosConferencia($atendimento)
    {
        $data = [
            'qtype' => 'su_oss_chamado.id_ticket',
            'query' => $atendimento,
            'oper' => '=',
            'page' => '1',
            'rp' => '20',
            'sortname' => 'su_oss_chamado.id',
            'sortorder' => 'desc',
            'grid_param' => json_encode(array(
                array('TB' => 'su_oss_chamado.setor', 'OP' => '=', 'P' => '7'),
                array('TB' => 'su_oss_chamado.id_assunto', 'OP' => 'IN', 'P' => '95,85,149,310,454,359,255,411,422')
            ))
        ];

        return $data;
    }

    public function finalizarConferencia($id_os_conferencia, $evaluationText, $id_ixc_user)
    {
        $currentDateTime = date('Y-m-d H:i:s');

        $data = [
            'id_chamado' => $id_os_conferencia, // ID da O.S
            'data_inicio' => $currentDateTime, // Data e Hora de Início da finalização
            'data_final' => $currentDateTime, // Data e Hora de Final da finalização
            'mensagem' => $evaluationText, // Mensagem
            'gera_comissao' => '', // "N" para Não "S" para Sim
            'id_su_diagnostico' => '', // ID do diagnóstico (não obrigatório)
            'finaliza_processo' => 'S', // "S" para finalizar o processo
            'status' => 'F', // Status "F" para finalizar
            'id_tecnico' => $id_ixc_user // ID do técnico responsável
        ];

        return $data;
    }

    public function BodyRequestMudancaDeEndereco($atendimento)
    {
        $data = [
            'qtype' => 'su_oss_chamado.id_ticket',
            'query' => $atendimento,
            'oper' => '=',
            'page' => '1',
            'rp' => '20',
            'sortname' => 'su_oss_chamado.id',
            'sortorder' => 'desc',
            'grid_param' => json_encode(array(
                array('TB' => 'su_oss_chamado.setor', 'OP' => '=', 'P' => '7'),
                array('TB' => 'su_oss_chamado.id_assunto', 'OP' => '=', 'P' => '149')
            ))
        ];

        return $data;
    }

    public function BodyRequestVerificarMudanca($id_os_conferencia, $evaluationText, $id_ixc_user)
    {
        $currentDateTime = date('Y-m-d H:i:s');

        $data = [
            'id_chamado' => $id_os_conferencia, // ID da O.S
            'data_inicio' => $currentDateTime, // Data e Hora de Início da finalização
            'data_final' => $currentDateTime, // Data e Hora de Final da finalização
            'mensagem' => $evaluationText, // Mensagem
            'gera_comissao' => '', // "N" para Não "S" para Sim
            'id_su_diagnostico' => '', // ID do diagnóstico (não obrigatório)
            'id_proxima_tarefa' => '50',
            'proxima_sequencia_forcada' => '7',
            'finaliza_processo' => 'N', // "S" para finalizar o processo
            'status' => 'F', // Status "F" para finalizar
            'id_tecnico' => $id_ixc_user // ID do técnico responsável
        ];

        return $data;
    }

    public function BodyRequestConferenciaInstalacao($id_os_conferencia, $evaluationText, $id_ixc_user)
    {
        $currentDateTime = date('Y-m-d H:i:s');

        $data = [
            'id_chamado' => $id_os_conferencia, // ID da O.S
            'data_inicio' => $currentDateTime, // Data e Hora de Início da finalização
            'data_final' => $currentDateTime, // Data e Hora de Final da finalização
            'mensagem' => $evaluationText, // Mensagem
            'gera_comissao' => '', // "N" para Não "S" para Sim
            'id_su_diagnostico' => '', // ID do diagnóstico (não obrigatório)
            'id_proxima_tarefa' => '478',
            'finaliza_processo' => 'N', // "S" para finalizar o processo
            'status' => 'F', // Status "F" para finalizar
            'id_tecnico' => $id_ixc_user // ID do técnico responsável
        ];

        return $data;
    }

    public function BodyRequestConferenciaCamera($id_os_conferencia, $evaluationText, $id_ixc_user)
    {
        $currentDateTime = date('Y-m-d H:i:s');
        $data = [
            'id_chamado' => $id_os_conferencia, // ID da O.S
            'data_inicio' => $currentDateTime, // Data e Hora de Início da finalização
            'data_final' => $currentDateTime, // Data e Hora de Final da finalização
            'mensagem' => $evaluationText, // Mensagem
            'gera_comissao' => '', // "N" para Não "S" para Sim
            'id_su_diagnostico' => '', // ID do diagnóstico (não obrigatório)
            'id_proxima_tarefa' => '222',
            'finaliza_processo' => 'N', // "S" para finalizar o processo
            'status' => 'F', // Status "F" para finalizar
            'id_tecnico' => $id_ixc_user // ID do técnico responsável
        ];

        return $data;
    }

    public function BodyRequestContrato($id_contrato)
    {
        $data = [
            'qtype' => 'cliente_contrato.id', //campo de filtro
            'query' => $id_contrato, //valor para consultar
            'oper' => '=', //operador da consulta
            'page' => '1', //página a ser mostrada
            'rp' => '20', //quantidade de registros por página
            'sortname' => 'cliente_contrato.id', //campo para ordenar a consulta
            'sortorder' => 'desc' //ordenação (asc= crescente | desc=decrescente)
        ];

        return $data;
    }

    public function listSoDepartament($query)
    {
        $params = array(
            'qtype' => 'su_oss_chamado.setor', //campo de filtro
            'query' => $query, //valor para consultar
            'oper' => '=', //operador da consulta
            'page' => '1', //página a ser mostrada
            'rp' => '1000', //quantidade de registros por página
            'sortname' => 'su_oss_chamado.id', //campo para ordenar a consulta
            'sortorder' => 'desc', //ordenação (asc= crescente | desc=decrescente)
            'grid_param' => json_encode(array(
                array('TB' => 'su_oss_chamado.status', 'OP' => '!=', 'P' => 'F')
            ))
        );

        return $params;
    }

    public function listAssunto($query)
    {
        $params = array(
            'qtype' => 'su_oss_assunto.id', //campo de filtro
            'query' => $query, //valor para consultar
            'oper' => '=', //operador da consulta
            'page' => '1', //página a ser mostrada
            'rp' => '20', //quantidade de registros por página
            'sortname' => 'su_oss_assunto.id', //campo para ordenar a consulta
            'sortorder' => 'desc' //ordenação (asc= crescente | desc=decrescente)
        );

        return $params;
    }
}
