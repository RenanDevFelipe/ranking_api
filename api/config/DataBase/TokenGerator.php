<?php

require_once __DIR__ . '../../../utils/jwt/JWT.php';
require_once __DIR__ . '../../../utils/jwt/Key.php';
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
        // $expirationTime = $issuedAt + 3600;
        $payload = [
            "iat" => $issuedAt,
            // "exp" => $expirationTime,
            "data" => $userData
        ];

        //Gerando o Token JWT
        return JWT::encode($payload, $this->secret_key, "HS256");
    }



    public function verificarToken()
    {
        $headers = getallheaders();

        if (!isset($headers["Authorization"])) {
            http_response_code(401);
            echo json_encode(["erro" => "Token não fornecido"]);
            exit;
        }

        $token = str_replace("Bearer ", "", $headers["Authorization"]);

        session_start();
        if (isset($_SESSION['blacklist'][$token])) {
            http_response_code(401);
            echo json_encode(["erro" => "Token inválido ou expirado"]);
            exit;
        }

        try {
            return JWT::decode($token, new Key($this->secret_key, "HS256"));
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["erro" => "Token inválido"]);
            exit;
        }
    }

    public function logoutUser()
    {
        $headers = getallheaders();

        if (!isset($headers["Authorization"])) {
            http_response_code(401);
            echo json_encode(["erro" => "Token não fornecido"]);
            exit;
        }

        $token = str_replace("Bearer ", "", $headers["Authorization"]);

        // Adiciona o token à sessão (blacklist temporária)
        session_start();
        $_SESSION['blacklist'][$token] = true;

        echo json_encode(["mensagem" => "Logout realizado com sucesso"]);
        http_response_code(200);
        exit;
    }
}
