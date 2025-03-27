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
                $message = "E-mail e senha obrigatórios";
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
}
