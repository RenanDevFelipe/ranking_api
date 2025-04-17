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

    public function deleteColaborador($id){
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

    public function getRankingMensal($date){
        $data = $this->service->getRankingMensal($date);
        ResponseHelper::jsonResponse($data);
    }

    public function getMentaMensal($id, $data){
        $data = $this->service->metaMensal($id, $data);
        ResponseHelper::jsonResponse($data);
    }

    public function postTutorial($title, $description, $url_view, $url_download, $criador, $name_icon){
        $data = $this->service->postTutoriais($title, $description, $url_view, $url_download, $criador,$name_icon);
        ResponseHelper::jsonResponse($data);
    }

    public function updateTutorial($id, $title, $description, $url_view, $url_download, $criador, $name_icon){
        $data = $this->service->updateTutoriais($id, $title, $description, $url_view, $url_download, $criador, $name_icon);
        ResponseHelper::jsonResponse($data);
    }


    public function deleteTutorial($id){
        $data = $this->service->deleteTutoriais($id);
        ResponseHelper::jsonResponse($data);
    }

    public function getOneTutorial($id){
        $data = $this->service->getOneTutoriais($id);
        ResponseHelper::jsonResponse($data);
    }
}
