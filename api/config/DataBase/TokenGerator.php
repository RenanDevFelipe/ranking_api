<?php

require_once __DIR__ . '../../../utils/jwt/JWT.php';
// require_once __DIR__ . '../../../utils/jwt/Key.php';
require_once __DIR__ . '/../../config/DataBase/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class Token
{

    private $secret_key;

    public function __construct()
    {
        $this->secret_key = SECRET_KEY;
    }

    public function gerarToken($userData)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;
        $payload = [
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "data" => $userData
        ];

        //Gerando o Token JWT
        return JWT::encode($payload, $this->secret_key, "HS256");
    }



    // public function verificarToken()
    // {
    //     $headers = getallheaders();

    //     if (!isset($headers["Authorization"])) {
    //         http_response_code(401);
    //         echo json_encode(["erro" => "Token não fornecido"]);
    //         exit;
    //     }

    //     $token = str_replace("Bearer ", "", $headers["Authorization"]);

    //     try {
    //         return JWT::decode($token, new Key($this->secret_key, "HS256"));
    //     } catch (Exception $e) {
    //         http_response_code(401);
    //         echo json_encode(["erro" => "Token inválido"]);
    //         exit;
    //     }
    // }
}
