<?php
require_once __DIR__ . "/BodyRequest.php";
require_once __DIR__ . "/../../Helpers/DataBaseHelpers/ResponseHelper.php";

class DataApiIxc{
    private $bodyRequest;

    public function __construct()
    {
        $this->bodyRequest = new ModelBodyRequest();
    }

    public function ListAllOsTecnicoFin($query){
        return $this->bodyRequest->BodyRequestModelFinalTecnico($query);
    }
}

?>