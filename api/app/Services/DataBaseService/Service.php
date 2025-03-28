<?php

require_once __DIR__ . '/../../../config/DataBase/dataBase.php';
require_once __DIR__ . '/../../../config/DataBase/TokenGerator.php';

class getDataBase
{

    private $db;
    private $token;

    public function __construct()
    {
        $db = new Database();
        $this->db = $db->getConnection();
        $this->token = new Token();
    }

    public function loginUser($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email_user = :email");
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["senha_user"])){
            $data = [
                "id" => $user["id"],
                "nome" => $user["nome_user"]
            ];

            $acess_token = $this->token->gerarToken($data);

            return ([
                "acess_token" => $acess_token,
                "email" => $user['email_user'],
                "nome" => $user['nome_user'],
                "id_ixc" => $user["id_ixc_user"]
            ]);
        } else {
            return (["erro:" => "Credenciais inválidas"]);
        }

    }

    public function listAllUser()
    {
        $this->token->verificarToken();
        $stmt = $this->db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
}
