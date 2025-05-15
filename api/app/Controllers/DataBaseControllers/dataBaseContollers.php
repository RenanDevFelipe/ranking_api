<?php

require_once __DIR__ . '/../../Services/DataBaseService/Service.php';
require_once __DIR__ . '../../../Helpers/DataBaseHelpers/ResponseHelper.php';


class DataBaseControllers
{
    private $service;

    public function __construct()
    {
        $this->service = new getDataBase();
    }

    public function loginUser($method, $email, $password)
    {
        if ($method == "POST") {
            if (!isset($email) || !isset($password)) {
                $message = [
                    "erro" => "E-mail e senha obrigatórios"
                ];
                ResponseHelper::jsonResponse($message);
                exit;
            }

            $data = $this->service->loginUser($email, $password);
            ResponseHelper::jsonResponse($data);
        } else {

            $message = "Requisição inválida";
            ResponseHelper::jsonResponse($message);
        }
    }

    public function getAllUser()
    {
        $data = $this->service->listAllUser();
        ResponseHelper::jsonResponse($data);
    }

    // public function getRankingDiarioGeral($data_request){
    //     if (!isset($data_request) || empty($data_request)){
    //         $message = ["erro" => "campo data_request obrigatório na requisição"];
    //         ResponseHelper::jsonResponse($message);
    //     }

    //     $data = $this->service->RankingDiarioGeral($data_request);
    //     ResponseHelper::jsonResponse($data);
    // }

    public function getAllColaborador()
    {
        $data = $this->service->AllColaborador();
        ResponseHelper::jsonResponse($data);
    }

    public function getOneColaborador($id)
    {
        $data = $this->service->getOneColaborador($id);
        ResponseHelper::jsonResponse($data);
    }

    public function postColaborador($method)
    {
        $data = $this->service->postColaborador($method);
        ResponseHelper::jsonResponse($data);
    }

    public function deleteColaborador($id)
    {
        $data = $this->service->deleteColaborador($id);
        ResponseHelper::jsonResponse($data);
    }

    public function logout()
    {
        return $this->service->logoutUser();
    }

    public function RankingSucessoTec($id, $data)
    {
        $data = $this->service->RankinDiarioCalc($id, $data);
        ResponseHelper::jsonResponse($data);
    }

    public function getAllDepartament()
    {
        $data = $this->service->getAllDepartament();
        ResponseHelper::jsonResponse($data);
    }

    public function verificarSucesso($id)
    {
        $data = $this->service->verificarSucesso($id);
        ResponseHelper::jsonResponse($data);
    }

    public function getMediaMensal($date, $id)
    {
        $data = $this->service->getMediaMensal($date, $id);
        ResponseHelper::jsonResponse($data);
    }

    public function getAllTutorias()
    {
        $data = $this->service->getAllTutoriais();
        ResponseHelper::jsonResponse($data);
    }

    public function getRankingMensal($date)
    {
        $data = $this->service->getRankingMensal($date);
        ResponseHelper::jsonResponse($data);
    }

    public function getRankingDiario($date)
    {
        $data = $this->service->getRankingDiario($date);
        ResponseHelper::jsonResponse($data);
    }

    public function getMentaMensal($id, $data)
    {
        $data = $this->service->metaMensal($id, $data);
        ResponseHelper::jsonResponse($data);
    }

    public function postTutorial($title, $description, $url_view, $url_download, $criador, $name_icon)
    {
        $data = $this->service->postTutoriais($title, $description, $url_view, $url_download, $criador, $name_icon);
        ResponseHelper::jsonResponse($data);
    }

    public function updateTutorial($id, $title, $description, $url_view, $url_download, $criador, $name_icon)
    {
        $data = $this->service->updateTutoriais($id, $title, $description, $url_view, $url_download, $criador, $name_icon);
        ResponseHelper::jsonResponse($data);
    }


    public function deleteTutorial($id)
    {
        $data = $this->service->deleteTutoriais($id);
        ResponseHelper::jsonResponse($data);
    }

    public function getOneTutorial($id)
    {
        $data = $this->service->getOneTutoriais($id);
        ResponseHelper::jsonResponse($data);
    }

    public function postAssuntoOS($method)
    {
        $data = $this->service->postAssuntoOs($method);
        ResponseHelper::jsonResponse($data);
    }

    public function getAllAssuntoOs(){
        $data = $this->service->getAllAssuntoOs();
        ResponseHelper::jsonResponse($data);
    }

    public function getOneAssuntoOs($id){
        $data = $this->service->getOneAssuntoOs($id);
        ResponseHelper::jsonResponse($data);
    }

    public function deleteAssuntoOs($id){
        $data = $this->service->deleteAssuntoOs($id);
        ResponseHelper::jsonResponse($data);
    }


    public function postChecklistField($method)
    {
        $data = $this->service->checklistFieldPost($method);
        ResponseHelper::jsonResponse($data);
    }

    public function checklistFieldGetFiltred($id)
    {
        $data = $this->service->checklistFieldGetFiltred($id);
        ResponseHelper::jsonResponse($data);
    }

    public function checklistFieldDelete($id)
    {
        $data = $this->service->checklistFieldDelete($id);
        ResponseHelper::jsonResponse($data);
    }

    public function gerarPlanilhaRankingMensal($date)
    {
        $data = $this->service->gerarPlanilhaRankingMensal($date);
        ResponseHelper::jsonResponse($data);
    }

    public function avaliacao_n2($method)
    {
        $data = $this->service->avaliacao_n2($method);
        ResponseHelper::jsonResponse($data);
    }
}
