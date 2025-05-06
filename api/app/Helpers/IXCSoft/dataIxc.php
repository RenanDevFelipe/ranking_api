<?php
require_once __DIR__ . "/BodyRequest.php";
require_once __DIR__ . "/../../Helpers/DataBaseHelpers/ResponseHelper.php";

class DataApiIxc{
    private $bodyRequest;

    public function __construct()
    {
        $this->bodyRequest = new ModelBodyRequest();
    }

    public function ListAllOsTecnicoFin($query, $data){
        return $this->bodyRequest->BodyRequestModelFinalTecnico($query, $data);
    }

    // public function testeAlmox($query){
    //     return $this->bodyRequest->BodyAlmoxTecnico($query);
    // }

    public function Cliente($query){
        return $this->bodyRequest->bodyRequestCliente($query);
    }

    public function arquivos($id){
        return $this->bodyRequest->BodyRequestArquivo($id);
    }
}

?>