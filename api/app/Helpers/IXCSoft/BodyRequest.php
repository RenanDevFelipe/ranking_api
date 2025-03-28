<?php

require_once __DIR__ . "/qtype.php";

class ModelBodyRequest
{
    private $qtypeIXC;


    public function __construct()
    {
        $this->qtypeIXC = new QtypeRiquisicoesIXC();
    }

    public function BodyRequest(){
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

    // public function BodyRequestModelDinamic($qtype, $query, $oper, $sortname, $sortorder){
    //     $data = [

    //         "qtype" => $qtype,
    //         "query" => $query,
    //         "oper" => $oper,
    //         "page" => "1",
    //         "rp" => "10000",
    //         "sortname" => $sortname,
    //         "sortorder" => $sortorder

    //     ];

    //     return $data;
    // }

    public function BodyRequestModelFinalTecnico($query, $data)
    {
        $data = [
                "qtype" => "su_oss_chamado.id_tecnico",
                "query" => $query,
                "oper" => "=",
                "page" => "1",
                "rp" => "1000",
                "sortname" => "su_oss_chamado.id",
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

    public function BodyAlmoxTecnico($query){
        $data = [
            "qtype" => $this->qtypeIXC->estoque_produtos_almox_filial().".id_almox",
            "query" => $query,
            "oper" => "=",
            "page" => "1",
            "rp" => "1000",
            "sortname" => $this->qtypeIXC->estoque_produtos_almox_filial().".id",
            "sortorder" => "desc",
            "status" => "S",
        ];

        return $data;
    }
}
