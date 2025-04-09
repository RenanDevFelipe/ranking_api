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
}
