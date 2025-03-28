<?php 

require_once __DIR__ . "/../../Helpers/IXCSoft/dataIxc.php";
require_once __DIR__ . "/../../Helpers/IXCSoft/method.php";
require_once __DIR__ . "/../../Helpers/IXCSoft/qtype.php";
require_once __DIR__ . "/../../../config/ApiIxc/ApiConfig.php";


class ApiIXC {

    private $body;
    private $methodIXC;
    private $queryIXC;
    private $ixcRequest;

    public function __construct()
    {
        $this->body = new DataApiIXC();
        $this->methodIXC = new ListMethod();
        $this->queryIXC = new QtypeRiquisicoesIXC();
        $this->ixcRequest = new ApiIxcRequest();
    }

    public function listarOsClienteTecnico($query){
        $body = $this->body->ListAllOsTecnicoFin($query);
        $methodH = $this->methodIXC->listarIXC();

        return $this->ixcRequest->request(
            $this->queryIXC->su_chamado_os(),
            "GET",
            $body,
            $methodH
        );

        // return $body;
    }
}

?>