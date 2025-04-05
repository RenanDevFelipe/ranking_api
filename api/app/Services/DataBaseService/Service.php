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

        if ($user && password_verify($password, $user["senha_user"])) {
            $data = [
                "id" => $user["id"],
                "nome" => $user["nome_user"]
            ];

            $acess_token = $this->token->gerarToken($data);

            return ([
                "access_token" => $acess_token,
                "email" => $user['email_user'],
                "nome" => $user['nome_user'],
                "id_ixc" => $user["id_ixc_user"]
            ]);
        } else {
            return (["erro:" => "Credenciais inválidas"]);
        }
    }

    public function logoutUser()
    {
        $this->token->logoutUser();
    }

    public function listAllUser()
    {
        $this->token->verificarToken();
        $stmt = $this->db->prepare("SELECT * FROM users");
        $stmt->execute();
        $users =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = count($users);

        if ($total == 0) {
            return (["erro" => "nenhum usuário encontrado"]);
            exit;
        }

        $registros = [
            "total" => $total,
            "registros" => $users
        ];

        return $registros;
    }


    public function AllColaborador()
    {
        $this->token->verificarToken();
        $stmt = $this->db->prepare("SELECT * FROM colaborador");
        $stmt->execute();
        $colaboradores  = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = count($colaboradores);

        if ($total == 0) {
            return ([
                "error" => "Nenhum Colaborador encontrado"
            ]);
            exit;
        }

        $registros = [
            "total" => $total,
            "registros" => $colaboradores
        ];

        return $registros;
    }

    public function RankingDiarioGeral($data)

    {
        $this->token->verificarToken();
        $stmt = $this->db->prepare("SELECT * FROM ranking_diario_geral WHERE data = :data_request");
        $stmt->execute([
            ":data_request" => $data
        ]);

        $ranking_d_geral = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = count($ranking_d_geral);

        if ($total < 1) {
            return [
                "erro" => "Nenhum dado encontrado referente a Esta data"
            ];
            exit;
        }

        $registros = [
            "total" => $total,
            "registros" => $ranking_d_geral
        ];

        return $registros;
    }

    public function RankinDiarioCalc($id, $data)
    {
        $this->token->verificarToken();
        /// SUCESSO AO CLIENTE ///

        $stmt = $this->db->prepare("SELECT ponto_sucesso, id_setor FROM avaliacao_sucesso WHERE id_tecnico = :id AND data_avaliacao = :data_avaliacao");
        $stmt->execute([
            ":id" => $id,
            ":data_avaliacao" => $data
        ]);

        $sucesso_tecnico = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_sucesso = count($sucesso_tecnico);
        $sum_ponts = 0;
        $id_setor = $sucesso_tecnico[0]['id_setor']; // Pega o id_setor da primeira linha

        if ($total_sucesso < 1) {
            $media_sucesso = 0;

            $sucesso = [
                "id_setor" => 8,
                "total_registros" => $total_sucesso,
                "media_diaria" => number_format($media_sucesso, 2),
                "soma_pontuacao" => number_format($sum_ponts, 2),
            ];
        } else {

            foreach ($sucesso_tecnico as $registro) {
                $sum_ponts += $registro['ponto_sucesso'];
            }

            $media_sucesso = $sum_ponts / $total_sucesso;

            $sucesso = [
                "id_setor" => $id_setor,
                "total_registros" => $total_sucesso,
                "media_diaria" => number_format($media_sucesso, 2),
                "soma_pontuacao" => number_format($sum_ponts, 2),
            ];
        }





        /// SUCESSO AO CLIENTE ///


        /// SETOR NIVEL 2 ///
        $stmt = $this->db->prepare("SELECT * FROM avaliacao_n2 WHERE id_tecnico_n2 = :id AND data_finalizacao = :data_avaliacao");
        $stmt->execute([
            ":id" => $id,
            ":data_avaliacao" => $data
        ]);
        $n2_tecnico = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_n2 = $stmt->rowCount();

        if ($total_n2 < 1) {
            return ([
                "erro" => "Nenhum Resultado encontrado"
            ]);
            exit;
        }

        $media_n2 = $n2_tecnico['ponto_total'] / 4;

        $setor_n2 = [
            "id_setor" => $n2_tecnico['id_setor'],
            "total_registros" => $total_n2,
            "media_diaria" => number_format($media_n2, 2),
            "soma_pontuacao" => number_format($n2_tecnico['ponto_total'], 2)
        ];

        /// SETOR NIVEL 2 ///



        /// SETOR NIVEL 3 ///
        $stmt = $this->db->prepare("SELECT * FROM avaliacao_n3 WHERE id_tecnico = :id AND data_finalizacao_os = :data_avaliacao");
        $stmt->execute([
            ":id" => $id,
            ":data_avaliacao" => $data
        ]);

        $setor_n3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_n3 = $stmt->rowCount();
        $sum_ponts_n3 = 0;
        $id_setor_n3 = $setor_n3[0]['id_setor'];

        if ($total_n3 < 1) {
            $media_n3 = 0;

            $setor_nivel3 = [
                "id_setor" => 5,
                "tota_registros" => $total_n3,
                "media_diaria" => number_format($media_n3, 2),
                "soma_pontacao" => number_format($sum_ponts_n3, 2)
            ];
        } else {
            foreach ($setor_n3 as $registro) {
                $sum_ponts_n3 += $registro['nota_os'];
            }

            $media_n3 = $sum_ponts_n3 / $total_n3;

            $setor_nivel3 = [
                "id_setor" => $id_setor_n3,
                "tota_registros" => $total_n3,
                "media_diaria" => number_format($media_n3, 2),
                "soma_pontacao" => number_format($sum_ponts_n3, 2)
            ];
        }


        /// SETOR NIVEL 3 ///




        /// ESTOQUE ///

        $stmt = $this->db->prepare("SELECT * FROM avaliacao_estoque WHERE id_tecnico_estoque = :id AND data_finalizacao = :data_fin");
        $stmt->execute([
            ":id" => $id,
            ":data_fin" => $data
        ]);

        $avaliacoes_estoque = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_estoque = $stmt->rowCount();

        if ($total_estoque < 1) {
            return (["erro" => "Nenhum dado encontrado no Estoque"]);
            exit;
        }

        $media_estoque = $avaliacoes_estoque['pnt_total_estoque'] / 1;

        $setor_estoque = [
            "id_setor" => $avaliacoes_estoque['id_setor_avaliacao'],
            "total_registros" => $total_estoque,
            "media_diaria" => number_format($media_estoque, 2),
            "soma_pontuacao" => number_format($avaliacoes_estoque['pnt_total_estoque'], 2)
        ];

        /// ESTOQUE ///



        /// RECURSOS HUMANOS ///

        $stmt = $this->db->prepare("SELECT * FROM avaliacao_rh WHERE id_tecnico = :id AND data_avaliacao = :data_fin");
        $stmt->execute([
            ":id" => $id,
            ":data_fin" => $data
        ]);

        $avalizacoes_rh = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_rh = $stmt->rowCount();

        if ($total_rh < 1) {
            return (["erro" => "Nenhum dado encontrado no Estoque"]);
            exit;
        }

        $media_rh = $avalizacoes_rh['pnt_total'] / 3;
        $sum_rh = $avalizacoes_rh['pnt_total'];

        $setor_rh = [
            "id_setor" => $avalizacoes_rh['id_setor'],
            "total_registros" => $total_rh,
            "media_diaria" => number_format($media_rh, 2),
            "soma_pontuacao" => number_format($sum_rh, 2)
        ];



        /// RECURSOS HUMANOS ///



        /// media total diaria ///

        $media_total_diaria = ($media_sucesso + $media_n2 + $media_n3 + $media_estoque + $media_rh) / 5;

        /// media total diaria ///


        $registros = [
            "media_setor" => [
                $sucesso,
                $setor_n2,
                $setor_nivel3,
                $setor_estoque,
                $setor_rh
            ],

            "media_total" => number_format($media_total_diaria, 2)
        ];

        return $registros;
    }
}
