<?php
require_once __DIR__ . "/BodyRequest.php";
require_once __DIR__ . "/../../Helpers/DataBaseHelpers/ResponseHelper.php";

class DataApiIxc
{
    private $bodyRequest;

    public function __construct()
    {
        $this->bodyRequest = new ModelBodyRequest();
    }

    public function ListAllOsTecnicoFin($query, $data)
    {
        return $this->bodyRequest->BodyRequestModelFinalTecnico($query, $data);
    }

    // public function testeAlmox($query){
    //     return $this->bodyRequest->BodyAlmoxTecnico($query);
    // }

    public function Cliente($query)
    {
        return $this->bodyRequest->bodyRequestCliente($query);
    }

    public function arquivos($id)
    {
        return $this->bodyRequest->BodyRequestArquivo($id);
    }

    public function login($query)
    {
        return $this->bodyRequest->BodyRequestLogin($query);
    }

    public function clienteFibraOnu($query) 
    {
        return $this->bodyRequest->BodyRequestONU($query);
    }

    public function clienteRadio($query)
    {
        return $this->bodyRequest->BodyRequestClienteRadio($query);
    }

    public function FinalizarOS($id)
    {
        return $this->bodyRequest->FinalizarOS($id);
    }

    public function FinalizarVerificar($id_os, $text_verificar, $id_troca, $id_ixc_user)
    {
        return $this->bodyRequest->FinalizarVerificar($id_os, $text_verificar, $id_troca, $id_ixc_user);
    }

    public function BodyRequestConferencia($atendimento)
    {
        return $this->bodyRequest->BodyRequestosConferencia($atendimento);
    }

    public function FinalizarConferencia($id_os_conferencia, $evaluationText, $id_ixc_user)
    {
        return $this->bodyRequest->finalizarConferencia($id_os_conferencia, $evaluationText, $id_ixc_user);
    }

    public function BodyRequestMudancaDeEndereco($atendimento)
    {
        return $this->bodyRequest->BodyRequestMudancaDeEndereco($atendimento);
    }

    public function BodyRequestVerificarMudanca($id_os_conferencia, $evaluationText, $id_ixc_user)
    {
        return $this->bodyRequest->BodyRequestVerificarMudanca($id_os_conferencia, $evaluationText, $id_ixc_user);
    }

    public function BodyRequestConferenciaInstalacao($id_os_conferencia, $evaluationText, $id_ixc_user)
    {
        return $this->bodyRequest->BodyRequestConferenciaInstalacao($id_os_conferencia, $evaluationText, $id_ixc_user);
    }

    public function BodyRequestConferenciaCamera($id_os_conferencia, $evaluationText, $id_ixc_user)
    {
        return $this->bodyRequest->BodyRequestConferenciaCamera($id_os_conferencia, $evaluationText, $id_ixc_user);
    }

    public function BodyRequestLoginIpAux($query)
    {
        return $this->bodyRequest->BodyRequestLoginIpAux($query);
    }

    public function BodyRequestContrato($id_contrato)
    {
        return $this->bodyRequest->BodyRequestContrato($id_contrato);
    }

    public function listSoDepartament($query){
        return $this->bodyRequest->listSoDepartament($query);
    }

    public function listAssunto($query)
    {
        return $this->bodyRequest->listAssunto($query);
    }

    public function searchColaboratorApi($query)
    {
        return $this->bodyRequest->searchColaboratorApi($query);
    }

    public function listAllSoDepartament()
    {
        return $this->bodyRequest->listAllSoDepartament();
    }

    public function listAllAssunto()
    {
        return $this->bodyRequest->listAllAssunto();
    }

    public function searchAllColaboratorApi()
    {
        return $this->bodyRequest->searchAllColaboratorApi();
    }
}
