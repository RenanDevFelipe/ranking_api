<?php

class ResponseHelper
{
    public static function jsonResponse($data)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }
}
