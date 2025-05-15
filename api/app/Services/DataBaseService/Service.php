<?php

require_once __DIR__ . '/../../../config/DataBase/dataBase.php';
require_once __DIR__ . '/../../../config/DataBase/TokenGerator.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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

            $setor = $this->getOneDepartament($user['setor_user']);

            $acess_token = $this->token->gerarToken($data);

            return ([
                "access_token" => $acess_token,
                "email" => $user['email_user'],
                "nome" => $user['nome_user'],
                "setor" => $setor,
                "id_ixc" => $user["id_ixc_user"],
                "role" => $user["role"]
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

    public function listOneUser()
    {
        $select = $this->db->prepare("SELECT * FROM users WHERE ");
    }

    public function postUser($method)
    {
        try {

            if ($method !== "POST") {
                return [
                    "status" => "error",
                    "message" => "Riquisição inválida"
                ];
            }

            $action = $_POST['action'] ?? null;
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ];
        }
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

    public function getOneColaborador($id)
    {
        $this->token->verificarToken();
        $stmt = $this->db->prepare("SELECT * FROM colaborador WHERE id_colaborador = :id");
        $stmt->execute(
            [":id" => $id]
        );
        $colaborador = $stmt->fetch(PDO::FETCH_ASSOC);

        return $colaborador;
    }

    public function postColaborador($method)
    {
        try {
            if ($method !== "POST") {
                return [
                    "status" => "error",
                    "message" => "Método inválido"
                ];
            }

            $this->token->verificarToken();

            $action = $_POST['action'] ?? null; // create ou update
            $id_colaborador = $_POST['id_colaborador'] ?? null;
            $id_ixc = $_POST['id_ixc'] ?? null;
            $nome   = $_POST['nome_colaborador'] ?? null;
            $setor  = $_POST['setor_colaborador'] ?? null;

            if (!$action || !$id_ixc || !$nome || !$setor) {
                return [
                    "status" => "error",
                    "message" => "Parâmetros obrigatórios ausentes"
                ];
            }

            // Upload de imagem
            $urlImagem = null;
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                $nomeImagem = uniqid('colaborador_') . '.' . $extensao;

                // Caminho absoluto no servidor
                $caminho = __DIR__ . '/../../../uploads/' . $nomeImagem;
                // Caminho salvo no banco/retornado
                $urlImagem = 'ticonnecte.com.br/ranking_api/api/uploads/' . $nomeImagem;

                if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
                    return [
                        "status" => "error",
                        "message" => "Erro ao salvar a imagem"
                    ];
                }
            }

            if ($action === 'create') {
                // Verifica se já existe
                $stmt = $this->db->prepare("SELECT * FROM colaborador WHERE id_ixc = :id_ixc");
                $stmt->execute([":id_ixc" => $id_ixc]);

                if ($stmt->rowCount() > 0) {
                    return [
                        "status" => "error",
                        "message" => "Colaborador já está cadastrado!"
                    ];
                }

                // Insere
                $stmt = $this->db->prepare("INSERT INTO colaborador (id_ixc, nome_colaborador, setor_colaborador, url_image) VALUES (:id, :nome, :setor, :url)");
                $stmt->execute([
                    ":id" => $id_ixc,
                    ":nome" => $nome,
                    ":setor" => $setor,
                    ":url" => $urlImagem
                ]);

                return [
                    "status" => "success",
                    "message" => "Colaborador cadastrado com sucesso",
                    "url_imagem" => $urlImagem
                ];
            }

            if ($action === 'update') {
                $stmt = $this->db->prepare("SELECT * FROM colaborador WHERE id_colaborador = :id");
                $stmt->execute([":id" => $id_colaborador]);

                if ($stmt->rowCount() === 0) {
                    return [
                        "status" => "error",
                        "message" => "Colaborador não encontrado"
                    ];
                }

                $sql = "UPDATE colaborador SET nome_colaborador = :nome, setor_colaborador = :setor";
                if ($urlImagem) {
                    $sql .= ", url_image = :url";
                }
                $sql .= " WHERE id_colaborador = :id";

                $params = [
                    ":nome" => $nome,
                    ":setor" => $setor,
                    ":id" => $id_colaborador
                ];
                if ($urlImagem) {
                    $params[":url"] = $urlImagem;
                }

                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);

                return [
                    "status" => "success",
                    "message" => "Colaborador atualizado com sucesso",
                    "url_imagem" => $urlImagem
                ];
            }

            return [
                "status" => "error",
                "message" => "Ação inválida"
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => "Erro: " . $e->getMessage()
            ];
        }
    }

    public function deleteColaborador($id)
    {
        $this->token->verificarToken();
        try {
            $stmt = $this->db->prepare("SELECT * FROM colaborador WHERE id_colaborador = :id");
            $stmt->execute([":id" => $id]);
            $exists = $stmt->rowCount();

            if ($exists < 1) {
                return ([
                    "status" => "success",
                    "message" => "Colaborador não encontrado"
                ]);

                exit;
            }

            $stmt = $this->db->prepare("DELETE FROM colaborador WHERE id_colaborador = :id");
            $success = $stmt->execute([":id" => $id]);

            if ($success) {
                return ([
                    "status" => "success",
                    "message" => "Colaborador deletado!"
                ]);
            } else {
                return ([
                    "status" => "erro",
                    "message" => "Erro ao deletar colaborador"
                ]);
            }
        } catch (PDOException $e) {
            return ([
                "status" => "erro",
                "message" => "Erro no banco de datos: " . $e->getMessage()
            ]);
        }
    }

    public function getColaboradorSetor($setor)
    {
        $stmt = $this->db->prepare("SELECT * FROM colaborador WHERE setor_colaborador = :setor");
        $stmt->execute(
            [":setor" => $setor]
        );
        $colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();

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


    public function getAllDepartament()
    {

        $this->token->verificarToken();

        $stmt = $this->db->prepare("SELECT * FROM setor");
        $stmt->execute();
        $setores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_setor = $stmt->rowCount();

        if ($total_setor < 1) {
            return ([
                "erro" => "Nenhum setor encontrado!"
            ]);
            exit;
        }

        $registro = [
            "total" => $total_setor,
            "registros" => $setores
        ];

        return $registro;
    }

    public function getOneDepartament($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM setor WHERE id_setor = :id");
        $stmt->execute([
            ":id" => $id
        ]);
        $setor = $stmt->fetch(PDO::FETCH_ASSOC);

        return $setor;
    }


    public function getMediaMensal($date, $id)
    {

        $this->token->verificarToken();

        $name_tecnico = $this->getOneColaborador($id);

        $stmt = $this->db->prepare("SELECT * FROM avaliacao_n3 WHERE DATE_FORMAT(data_finalizacao_os, '%Y-%m') = :data_finalizacao AND id_tecnico = :id");
        $stmt->execute([
            ":data_finalizacao" => $date,
            ":id" => $id
        ]);
        $setor_n3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $id_n3 = $setor_n3[0]['id_setor'];
        $total_n3 = $stmt->rowCount();
        $name_setor = $this->getOneDepartament($id_n3);

        $sum_n3 = 0;

        if ($total_n3 < 1) {
            return (["erro" => "nenhum registro encontrado"]);
        }

        foreach ($setor_n3 as $n3) {
            $sum_n3 += $n3['nota_os'];
        }

        $media_n3 = $sum_n3 / $total_n3;

        $registros_n3 = [
            "id_setor" => $id_n3,
            "setor" => $name_setor['nome_setor'],
            "total_registros" => $total_n3,
            "media_mensal" => number_format($media_n3, 2),
            "soma_pontuacao" => number_format($sum_n3, 2)
        ];

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $stmt = $this->db->prepare("SELECT * FROM avaliacao_sucesso WHERE DATE_FORMAT(data_avaliacao, '%Y-%m') = :data_avaliacao AND id_tecnico = :id");
        $stmt->execute([
            ":id" => $id,
            ":data_avaliacao" => $date
        ]);
        $setor_sucesso = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_sucesso = $stmt->rowCount();
        $id_sucesso = $setor_sucesso[0]['id_setor'];
        $name_setor = $this->getOneDepartament($id_sucesso);

        if ($total_sucesso < 1) {
            return ([
                "erro" => "Nenhum resultado encontrado",
                "id_setor" => $id_sucesso,
                "setor" => $name_setor
            ]);
        }

        $sum_sucesso = 0;

        foreach ($setor_sucesso as $sucesso) {
            $sum_sucesso += $sucesso['ponto_sucesso'];
        }

        $media_sucesso = $sum_sucesso / $total_sucesso;

        $registros_sucesso = [
            "id_setor" => $id_sucesso,
            "setor" => $name_setor['nome_setor'],
            "total_registros" => $total_sucesso,
            "media_mensal" => number_format($media_sucesso, 2),
            "soma_pontuacao" => number_format($sum_sucesso, 2),
        ];

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////


        $stmt = $this->db->prepare("SELECT * FROM avaliacao_n2 WHERE DATE_FORMAT(data_finalizacao, '%Y-%m') = :data_finalizacao AND id_tecnico_n2 = :id");
        $stmt->execute([
            ":data_finalizacao" => $date,
            ":id" => $id
        ]);
        $setor_n2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_n2 = $stmt->rowCount();
        $id_n2 = $setor_n2[0]['id_setor'];
        $name_setor = $this->getOneDepartament($id_n2);

        if ($total_n2 < 1) {
            return ([
                "erro" => "Nenhum registro econtrado",
                "id_setor" => $id_n2,
                "setor" => $name_setor
            ]);
        }

        $sum_n2 = 0;

        foreach ($setor_n2 as $n2) {
            $sum_n2 += $n2['ponto_total'];
        }

        $media_n2 = $sum_n2 / ($total_n2 * 4);

        $registros_n2 = [
            "id_setor" => $id_n2,
            "setor" => $name_setor['nome_setor'],
            "total_registros" => $total_n2,
            "media_mensal" => number_format($media_n2, 2),
            "soma_pontuacao" => number_format($sum_n2, 2)
        ];


        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $stmt = $this->db->prepare("SELECT * FROM avaliacao_rh WHERE DATE_FORMAT(data_avaliacao, '%Y-%m') = :data_avaliacao AND id_tecnico = :id");
        $stmt->execute([
            ":data_avaliacao" => $date,
            ":id" => $id
        ]);
        $setor_rh = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_rh = $stmt->rowCount();
        $id_rh = $setor_rh[0]['id_setor'];
        $name_setor = $this->getOneDepartament($id_rh);

        if ($total_rh < 1) {
            return ([
                "erro" => "Nenhum Registro econtrado",
                "id_setor" => $id_rh,
                "setor" => $name_setor['nome_setor']
            ]);
        }

        $sum_rh = 0;

        foreach ($setor_rh as $rh) {
            $sum_rh += $rh['pnt_total'];
        }

        $media_rh = $sum_rh / ($total_rh * 3);

        $registros_rh = [
            "id_setor" => $id_rh,
            "setor" => $name_setor['nome_setor'],
            "total_registros" => $total_rh,
            "media_mensal" => number_format($media_rh, 2),
            "soma_pontuacao" => number_format($sum_rh, 2)
        ];

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $stmt = $this->db->prepare("SELECT * FROM avaliacao_estoque WHERE DATE_FORMAT(data_finalizacao, '%Y-%m') = :data_finalizacao AND id_tecnico_estoque = :id");
        $stmt->execute([
            ":data_finalizacao" => $date,
            ":id" => $id
        ]);
        $setor_estoque = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_estoque = $stmt->rowCount();
        $id_estoque = $setor_estoque[0]['id_setor_avaliacao'];
        $name_setor = $this->getOneDepartament($id_estoque);

        if ($total_estoque < 1) {
            return ([
                "erro" => "Nenhum registro enocntrado",
                "id_setor" => $id_estoque,
                "setor" => $name_setor['nome_setor']
            ]);
        }

        $sum_estoque = 0;

        foreach ($setor_estoque as $estoque) {
            $sum_estoque += $estoque['pnt_total_estoque'];
        }

        $media_estoque = $sum_estoque / $total_estoque;

        $registros_estoque = [
            "id_setor" => $id_estoque,
            "setor" => $name_setor['nome_setor'],
            "total_registros" => $total_estoque,
            "media_mensal" => number_format($media_estoque, 2),
            "soma_pontuacao" => number_format($sum_estoque, 2),
        ];

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $media_mensal = ($media_n3 + $media_n2 + $media_rh + $media_sucesso + $media_estoque) / 5;

        $total_registros = $total_sucesso + $total_n3 + $total_n2 + $total_rh + $total_estoque;

        $media_mensal_setor = [
            $registros_n3,
            $registros_sucesso,
            $registros_n2,
            $registros_rh,
            $registros_estoque,
        ];

        $metaMensal = $this->metaMensal($id, $date);

        return ([
            "tecnico" => $name_tecnico['nome_colaborador'],
            "total_registros" => $total_registros,
            "media_mensal" => number_format($media_mensal, 2),
            "meta_mensal" => $metaMensal,
            "media_setor" => $media_mensal_setor
        ]);
    }

    public function getRankingDiario($date)
    {
        try {

            $colaboradores = $this->getColaboradorSetor(22);

            $ranking_Diario = [];

            foreach ($colaboradores['registros'] as $colaborador) {
                $id = $colaborador['id_colaborador'];

                $ranking_Diario[] = $this->RankinDiarioCalc($id, $date);
            }

            usort($ranking_Diario, function ($a, $b) {
                return $b['media_total'] <=> $a['media_total'];
            });

            foreach ($ranking_Diario as $i => &$item) {
                $nova_ordem = [];

                foreach ($item as $key => $value) {
                    $nova_ordem[$key] = $value;

                    if ($key === 'colaborador') {
                        $nova_ordem['colocacao'] = $i + 1;
                    }
                }

                $item = $nova_ordem;
            }

            return [
                "status" => "success",
                "ranking_diario" => $ranking_Diario
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ];
        }
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

        $colaborador = $this->getOneColaborador($id);

        $sucesso_tecnico = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_sucesso = count($sucesso_tecnico);
        $sum_ponts = 0;
        $id_setor = $sucesso_tecnico[0]['id_setor']; // Pega o id_setor da primeira linha
        $setor_sucesso = $this->getOneDepartament($id_setor);

        if ($total_sucesso < 1) {
            $media_sucesso = 0;

            $sucesso = [
                "id_setor" => 8,
                "setor" => 'Sucesso ao cliente',
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
                "setor" => $setor_sucesso['nome_setor'],
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
        $id_n2 = $n2_tecnico['id_setor'];
        $setor_n2 = $this->getOneDepartament($id_n2);
        $total_n2 = $stmt->rowCount();

        if ($total_n2 < 1) {
            return ([
                "erro" => "Nenhum Resultado encontrado"
            ]);
            exit;
        }

        $media_n2 = $n2_tecnico['ponto_total'] / 4;

        $setor_n2 = [
            "id_setor" => $id_n2,
            "setor" => $setor_n2['nome_setor'],
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
        $setor_Ni3 = $this->getOneDepartament($id_setor_n3);

        if ($total_n3 < 1) {
            $media_n3 = 0;

            $setor_nivel3 = [
                "id_setor" => 5,
                "setor" => 'Suporte Nível 3',
                "total_registros" => $total_n3,
                "media_diaria" => number_format($media_n3, 2),
                "soma_pontuacao" => number_format($sum_ponts_n3, 2)
            ];
        } else {
            foreach ($setor_n3 as $registro) {
                $sum_ponts_n3 += $registro['nota_os'];
            }

            $media_n3 = $sum_ponts_n3 / $total_n3;

            $setor_nivel3 = [
                "id_setor" => $id_setor_n3,
                "setor" => $setor_Ni3['nome_setor'],
                "total_registros" => $total_n3,
                "media_diaria" => number_format($media_n3, 2),
                "soma_pontuacao" => number_format($sum_ponts_n3, 2)
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
        $id_estoque = $avaliacoes_estoque['id_setor_avaliacao'];
        $setor_est = $this->getOneDepartament($id_estoque);

        if ($total_estoque < 1) {
            return (["erro" => "Nenhum dado encontrado no Estoque"]);
            exit;
        }

        $media_estoque = $avaliacoes_estoque['pnt_total_estoque'] / 1;

        $setor_estoque = [
            "id_setor" => $id_estoque,
            "setor" => $setor_est['nome_setor'],
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
        $setor_rh = $this->getOneDepartament($avalizacoes_rh['id_setor']);

        if ($total_rh < 1) {
            return (["erro" => "Nenhum dado encontrado no Estoque"]);
            exit;
        }

        $media_rh = $avalizacoes_rh['pnt_total'] / 3;
        $sum_rh = $avalizacoes_rh['pnt_total'];

        $setor_rh = [
            "id_setor" => $avalizacoes_rh['id_setor'],
            "setor" => $setor_rh['nome_setor'],
            "total_registros" => $total_rh,
            "media_diaria" => number_format($media_rh, 2),
            "soma_pontuacao" => number_format($sum_rh, 2)
        ];



        /// RECURSOS HUMANOS ///



        /// media total diaria ///

        $media_total_diaria = ($media_sucesso + $media_n2 + $media_n3 + $media_estoque + $media_rh) / 5;
        $total_avaliacoes = ($total_estoque + $total_n2 + $total_n3 + $total_rh + $total_sucesso);

        /// media total diaria ///


        $registros = [
            "colaborador" => $colaborador['nome_colaborador'],
            "total_registros" => $total_avaliacoes,
            "media_total" => number_format($media_total_diaria, 2),
            "media_setor" => [
                $sucesso,
                $setor_n2,
                $setor_nivel3,
                $setor_estoque,
                $setor_rh
            ]


        ];

        return $registros;
    }

    public function getRankingMensal($date)
    {
        $this->token->verificarToken();
        $colaboradores = $this->getColaboradorSetor(22);

        $ranking_mensal = [];

        foreach ($colaboradores['registros'] as $colaborador) {
            $id = $colaborador['id_colaborador'];

            $ranking_mensal[] = $this->getMediaMensal($date, $id);
        }

        usort($ranking_mensal, function ($a, $b) {
            return $b['media_mensal'] <=> $a['media_mensal'];
        });

        foreach ($ranking_mensal as $i => &$item) {
            $nova_ordem = [];

            foreach ($item as $key => $value) {
                $nova_ordem[$key] = $value;

                if ($key === 'tecnico') {
                    $nova_ordem['colocacao'] = $i + 1;
                }
            }

            $item = $nova_ordem;
        }

        $ranking_mensal = [
            "total" => $colaboradores['total'],
            "ranking_mensal" => $ranking_mensal
        ];

        return $ranking_mensal;
    }

    public function verificarSucesso($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM avaliacao_sucesso WHERE id_atendimento = :id_atendimento");
        $stmt->execute([":id_atendimento" => $id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $result;
    }



    public function metaMensal($id, $data)
    {
        list($ano, $mes) = explode('-', $data);

        $qntDias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
        $count_d_batidos = 0;

        foreach (range(1, $qntDias) as $dia) {
            $diaFormatado = str_pad($dia, 2, '0', STR_PAD_LEFT);
            $dataCompleta = "$ano-$mes-$diaFormatado";

            $request_qnt_d_batidos = $this->RankinDiarioCalc($id, $dataCompleta);

            if ($request_qnt_d_batidos['media_total'] === "10.00") {
                $count_d_batidos += 1;
            }
        }

        $diasTrabalhados = 26;

        $metaMensal = ($count_d_batidos * 100) / $diasTrabalhados;

        return ([
            "total_dias_batidos" => $count_d_batidos,
            "meta_do_mes" => number_format($metaMensal, 2) . "%"
        ]);
    }


    public function getAllTutoriais()
    {
        $this->token->verificarToken();
        $stmt = $this->db->prepare("SELECT * FROM tutoriais");
        $stmt->execute();
        $tutoriais = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $stmt->rowCount();

        $registros = [
            "total" => $total,
            "registros" => $tutoriais
        ];

        return $registros;
    }

    public function getOneTutoriais($id)
    {
        try {
            $this->token->verificarToken();
            $stmt = $this->db->prepare("SELECT * FROM tutoriais WHERE id = :id");
            $success = $stmt->execute([":id" => $id]);
            $tutorial = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($success) {
                return ([
                    "status" => "success",
                    "registro" => $tutorial
                ]);
            } else {
                return ([
                    "status" => "erro",
                    "message" => "Erro ao buscar Tutorial"
                ]);
            }
        } catch (PDOException $e) {
            return ([
                "status" => "erro",
                "message" => "Erro ao executar no banco de dados"
            ]);
        }
    }

    public function postTutoriais($title, $description, $url_view, $url_download, $criador, $name_icon)
    {
        try {
            $this->token->verificarToken();
            $stmt = $this->db->prepare("INSERT INTO tutoriais VALUES (:id,:title,:descricao,:url_view,:url_download,:criador,:data_descricao,:nome_icon)");

            // Sanitização
            $title = htmlspecialchars(trim($title));
            $description = htmlspecialchars(trim($description));
            $url_view = trim($url_view);
            $url_download = trim($url_download);
            $criador = htmlspecialchars(trim($criador));
            $name_icon = htmlspecialchars(trim($name_icon));

            // Validação
            if (
                empty($title) || empty($description) ||
                !filter_var($url_view, FILTER_VALIDATE_URL) ||
                !filter_var($url_download, FILTER_VALIDATE_URL) ||
                empty($criador) || empty($name_icon)
            ) {
                return (['status' => 'error', 'message' => 'Preencha todos os campos corretamente.']);
            }

            $success = $stmt->execute([
                ":id" => null,
                ":title" => $title,
                ":descricao" => $description,
                ":url_view" => $url_view,
                ":url_download" => $url_download,
                ":criador" => $criador,
                ":data_descricao" => date('Y-m-d'),
                ":nome_icon" => $name_icon
            ]);

            if ($success) {
                return ([
                    "status" => "success",
                    "message" => "Tutorial inserido com sucesso"
                ]);
            } else {
                return ([
                    "status" => "erro",
                    "message" => "Erro ao inserir Tutorial"
                ]);
            }
        } catch (PDOException $e) {
            return ([
                "status" => "erro",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ]);
        }
    }

    public function deleteTutoriais($id)
    {
        try {
            $this->token->verificarToken();
            $stmt = $this->db->prepare("DELETE FROM tutoriais WHERE id = :id");
            $success = $stmt->execute([
                ":id" => $id
            ]);

            if ($success) {
                return ([
                    "status" => "success",
                    "message" => "Tutorial deletado com sucesso!"
                ]);
            } else {
                return ([
                    "status" => "erro",
                    "message" => "Erro ao deletar tutorial"
                ]);
            }
        } catch (PDOException $e) {
            return ([
                "status" => "erro",
                "message" => "Erro ao banco de dados " . $e->getMessage()
            ]);
        }
    }

    public function updateTutoriais($id, $title, $description, $url_view, $url_download, $criador, $name_icon)
    {
        try {
            $this->token->verificarToken();
            // Verifica se o tutorial existe
            $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM tutoriais WHERE id = :id");
            $checkStmt->execute([":id" => $id]);
            $exists = $checkStmt->fetchColumn();

            if (!$exists) {
                return ([
                    "status" => "erro",
                    "message" => "Tutorial com ID $id não encontrado."
                ]);
            }

            // Preparar update
            $stmt = $this->db->prepare("UPDATE tutoriais
            SET title = :title,
                descricao = :descricao,
                url_view = :url_view,
                url_download = :url_download,
                name_icon = :nome_icon
            WHERE id = :id");

            // Sanitização dos dados
            $title = htmlspecialchars(trim($title));
            $description = htmlspecialchars(trim($description));
            $url_view = trim($url_view);
            $url_download = trim($url_download);
            $criador = htmlspecialchars(trim($criador));
            $name_icon = htmlspecialchars(trim($name_icon));
            $id = (int) $id;

            $success = $stmt->execute([
                ":id" => $id,
                ":title" => $title,
                ":descricao" => $description,
                ":url_view" => $url_view,
                ":url_download" => $url_download,
                ":nome_icon" => $name_icon
            ]);

            if ($success) {


                $insert = $this->db->prepare("INSERT INTO historico_tutorial (data_edicao, editado_por, id_tutorial) VALUES (:data_edicao, :editado_por, :id_tutorial)");
                $success = $insert->execute([
                    ":data_edicao" => date("Y-m-d H:i:s"),
                    ":editado_por" => $criador,
                    ":id_tutorial" => $id
                ]);

                return ([
                    "status" => "success",
                    "message" => "Tutorial atualizado com sucesso!"
                ]);
            } else {
                return ([
                    "status" => "erro",
                    "message" => "Erro ao atualizar o tutorial."
                ]);
            }
        } catch (PDOException $e) {
            return ([
                "status" => "erro",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ]);
        }
    }


    public function getOneAssuntoOs($id)
    {
        try {

            $this->token->verificarToken();

            $stmt = $this->db->prepare("SELECT * FROM assunto_os WHERE id = :id");
            $stmt->execute([":id" => $id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            $total = $stmt->rowCount();

            if ($total > 0) {
                return [
                    "total" => $total,
                    "registro" => $registro
                ];
            } else {
                return [
                    "error" => "Nenhum Registro encontrado"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ];
        }
    }

    public function getAllAssuntoOs()
    {
        try {

            $this->token->verificarToken();

            $stmt = $this->db->prepare("SELECT * FROM assunto_os");
            $stmt->execute();
            $total = $stmt->rowCount();
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($total < 1) {
                return [
                    "status" => "error",
                    "message" => "Nenhum resultado encontrado"
                ];
                exit;
            }

            return [
                "total" => $total,
                "registros" => $registros
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ];
        }
    }

    public function postAssuntoOs($method)
    {
        try {


            if ($method !== "POST") {
                return [
                    "status" => "error",
                    "message" => "Requisição inválida!"
                ];
            }

            $this->token->verificarToken();

            $id = $_POST['id'];
            $name = $_POST['name'];
            $action = $_POST['action'];

            if (!empty($id) || !empty($name) || !empty($action)) {

                if ($action === "create") {

                    $verification = $this->getOneAssuntoOs($id);

                    if ($verification['total'] > 0) {
                        return [
                            "status" => "error",
                            "message" => "Assunto já está cadastrado"
                        ];
                    }

                    $stmt = $this->db->prepare("INSERT INTO assunto_os (id, name) VALUES (:id, :name)");
                    $success = $stmt->execute([
                        ":id" => $id,
                        ":name" => $name
                    ]);

                    if ($success) {
                        return [
                            "status" => "success",
                            "message" => "Assunto cadastrado!"
                        ];
                    } else {
                        return [
                            "status" => "error",
                            "message" => "Erro ao cadastrar assunto"
                        ];
                    }
                } elseif ($action === "update") {

                    $verification = $this->getOneAssuntoOs($id);

                    if ($verification['total'] < 1) {
                        return [
                            "status" => "error",
                            "message" => "Assunto não encontrado"
                        ];
                    }

                    $update = $this->db->prepare("UPDATE assunto_os SET id = :id, name = :name WHERE id = :id_up");
                    $success = $update->execute([
                        ":id" => $id,
                        ":name" => $name,
                        ":id_up" => $id
                    ]);

                    if ($success) {
                        return [
                            "status" => "success",
                            "message" => "Assunto atualizado!"
                        ];
                    } else {
                        return [
                            "status" => "error",
                            "message" => "Erro ao atualizar assunto!"
                        ];
                    }
                }
            } else {
                return [
                    "status" => "error",
                    "message" => "Preencha todos os campos!"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ];
        }
    }

    public function deleteAssuntoOs($id)
    {
        try {

            $this->token->verificarToken();

            $verification = $this->getOneAssuntoOs($id);

            if ($verification['total'] < 1) {
                return [
                    "status" => "error",
                    "message" => "Registro não encontrado"
                ];
            }

            $delete = $this->db->prepare("DELETE FROM assunto_os WHERE id = :id");
            $success = $delete->execute([":id" => $id]);

            if ($success) {
                return [
                    "status" => "success",
                    "message" => "Assunto deletado!"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Erro ao deletar assunto!"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ];
        }
    }


    public function checklistFieldPost($method)
    {
        try {

            if ($method == "POST") {

                $this->token->verificarToken();

                $id = $_POST['id'];
                $checklist_id = $_POST['checklist_id'] ?? null;
                $label = $_POST['label'] ?? null;
                $type = $_POST['type'] ?? null;
                $max_score = $_POST['max_score'] ?? null;
                $action = $_POST['action'];

                if ($checklist_id == null || $label == null ||  $type == null) {
                    return [
                        "status" => "error",
                        "message" => "Preencha todos os campos"
                    ];
                    exit;
                } else {

                    if ($action === "create") {
                        $add = $this->db->prepare("INSERT INTO checklist_fields (checklist_id, label, type, max_score) VALUES (:id, :label, :type, :max_score)");
                        $success = $add->execute([
                            ":id" => $checklist_id,
                            ":label" => $label,
                            ":type" => $type,
                            ":max_score" => $max_score
                        ]);

                        if ($success) {
                            return [
                                "status" => "success",
                                "message" => "Field adicionado com sucesso"
                            ];
                        } else {
                            return [
                                "status" => "error",
                                "message" => "Erro ao adicionar field"
                            ];
                        }
                    } elseif ($action === "update") {
                        $update = $this->db->prepare("UPDATE checklist_fields  SET checklist_id = :id, label = :label, type = :type, max_score = :max_score WHERE id = :id_field");
                        $success = $update->execute([
                            ":id" => $checklist_id,
                            ":label" => $label,
                            ":type" => $type,
                            ":max_score" => $max_score,
                            ":id_field" => $id
                        ]);

                        if ($success) {
                            return [
                                "status" => "success",
                                "message" => "Field atualizado"
                            ];
                        } else {
                            return [
                                "status" => "error",
                                "message" => "Erro ao editar Field"
                            ];
                        }
                    }
                }
            } else {
                return [
                    "status" => "error",
                    "message" => "Requisição inválida"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ];
        }
    }

    public function checklistFieldGetFiltred($id)
    {
        try {

            $this->token->verificarToken();

            $getAll = $this->db->prepare("SELECT * FROM checklist_fields WHERE checklist_id = :id");
            $getAll->execute([":id" => $id]);
            $count = $getAll->rowCount();
            $checklist = $getAll->fetchAll(PDO::FETCH_ASSOC);

            if ($count < 1) {
                return [
                    "total" => $count,
                    "message" => "Nenhuma Field vinculada a esse assunto!"
                ];
                exit;
            }

            return [
                "checklist" => $checklist
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no banco de Dados: " . $e->getMessage()
            ];
        }
    }

    public function checklistFieldDelete($id)
    {
        try {

            $verify = $this->db->prepare("SELECT * FROM checklist_fields");
            $verify->execute();
            $count = $verify->rowCount();

            if ($count < 1) {
                return [
                    "status" => "error",
                    "message" => "Field não encontrado"
                ];
                exit;
            }

            $delete = $this->db->prepare("DELETE FROM checklist_fields WHERE id = :id");
            $success = $delete->execute([
                ":id" => $id
            ]);

            if ($success) {
                return [
                    "status" => "success",
                    "message" => "Field deletado!"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Erro ao deletar Field"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ];
        }
    }


    function gerarPlanilhaRankingMensal($date)
    {
        $dados = $this->getRankingMensal($date);

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:x="urn:schemas-microsoft-com:office:excel"
        xmlns="http://www.w3.org/TR/REC-html40">
        <head>
        <meta charset="UTF-8">
        <style>
            .title {
                background-color: #4F81BD;
                color: #FFFFFF;
                font-weight: bold;
                font-size: 14pt;
                text-align: center;
                padding: 5px;
            }
            .header {
                background-color: #D6E3BC;
                font-weight: bold;
                border: 1px solid #000000;
            }
            .highlight {
                font-weight: bold;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            td {
                padding: 5px;
                border: 1px solid #CCCCCC;
            }
            .nested-table {
                width: 100%;
                border: none;
                margin-top: 10px;
            }
        </style>
        <!--[if gte mso 9]>
        <xml>
            <x:ExcelWorkbook>
                <x:ExcelWorksheets>
                    <x:ExcelWorksheet>
                        <x:Name>Relatório Consolidado</x:Name>
                        <x:WorksheetOptions>
                            <x:DisplayGridlines/>
                        </x:WorksheetOptions>
                    </x:ExcelWorksheet>
                </x:ExcelWorksheets>
            </x:ExcelWorkbook>
        </xml>
        <![endif]-->
        </head>
        <body>';

        // Cabeçalho principal
        $html .= '<div style="text-align:center;margin-bottom:20px;">
            <h1 style="color:#4F81BD;">Relatório Mensal Consolidado</h1>
            <h3>' . date('F Y', strtotime($date)) . '</h3>
          </div>';

        // Quantidade de técnicos
        $tecnicos = $dados['ranking_mensal'];
        $colunas = count($tecnicos) * 2 - 1; // espaço entre técnicos

        // Início da tabela
        $html .= '<table style="width: 100%;">';

        // Linha de nomes
        $html .= '<tr>';
        foreach ($tecnicos as $index => $item) {
            $html .= '<td class="title" style="text-align:center; width:45%;">' . htmlspecialchars($item['tecnico']) . '</td>';
            if ($index < count($tecnicos) - 1) {
                $html .= '<td style="width:10%;"></td>'; // Espaço entre colunas
            }
        }
        $html .= '</tr>';

        // Linha de tabelas mensais
        $html .= '<tr>';
        foreach ($tecnicos as $index => $item) {
            $html .= '<td style="vertical-align:top;">';
            $html .= '<table class="nested-table">';
            $html .= '
            <tr>
                <td class="header">Total Registros</td>
                <td>' . $item['total_registros'] . '</td>
            </tr>
            <tr>
                <td class="header">Média Mensal</td>
                <td>' . number_format($item['media_mensal'], 2, ',', '.') . '</td>
            </tr>
            <tr>
                <td class="header">Dias Nota 10</td>
                <td>' . $item['meta_mensal']['total_dias_batidos'] . '</td>
            </tr>
            <tr>
                <td class="header">Meta do Mês</td>
                <td>' . $item['meta_mensal']['meta_do_mes'] . '</td>
            </tr>
            <tr>
                <td class="header">Valor Receber</td>
                <td class="highlight">R$ ' . number_format(($item['meta_mensal']['total_dias_batidos'] * 20), 2, ',', '.') . '</td>
            </tr>';
            $html .= '</table>';
            $html .= '</td>';

            if ($index < count($tecnicos) - 1) {
                $html .= '<td></td>';
            }
        }
        $html .= '</tr>';

        // Linha de tabelas por setor
        $html .= '<tr>';
        foreach ($tecnicos as $index => $item) {
            $html .= '<td style="vertical-align:top;">';
            $html .= '<table class="nested-table">';
            $html .= '
            <tr>
                <td colspan="3" class="header" style="text-align:center;">Desempenho por Setor</td>
            </tr>
            <tr>
                <td class="header">Setor</td>
                <td class="header">Total</td>
                <td class="header">Média</td>
            </tr>';
            foreach ($item['media_setor'] as $setor) {
                $html .= '
            <tr>
                <td>' . htmlspecialchars($setor['setor']) . '</td>
                <td>' . $setor['total_registros'] . '</td>
                <td>' . number_format($setor['media_mensal'], 2, ',', '.') . '</td>
            </tr>';
            }
            $html .= '</table>';
            $html .= '</td>';

            if ($index < count($tecnicos) - 1) {
                $html .= '<td></td>';
            }
        }
        $html .= '</tr>';

        $html .= '</table>';
        $html .= '</body></html>';

        // Forçar download
        $filename = 'ranking_consolidado_' . date('Y-m-d') . '.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo $html;
        exit;
    }


    /// SERVICE PONTO N2 ///

    // public function avaliacao_n2($method)
    // {
    //     try {
    //         if ($method != 'POST') {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'Requisição inválida'
    //             ];
    //         }

    //         $this->token->verificarToken();

    //         $data = $_POST['data_requisicao'] ?? date('Y-m-d');
    //         $id_tecnico = $_POST['id_colaborador'];

    //         // Buscar a avaliação existente
    //         $stmt = $this->db->prepare("SELECT * FROM avaliacao_n2 WHERE id_tecnico_n2 = ? AND data_finalizacao = ?");
    //         $stmt->execute([$id_tecnico, $data]);
    //         $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

    //         if (!$avaliacao) {
    //             return [
    //                 'status' => 'error',
    //                 'message' => 'Avaliação não encontrada.'
    //             ];
    //         }

    //         // Função para ajustar ponto individualmente
    //         function ajustarPonto($valorAtual, $add, $sub)
    //         {
    //             if ($add) return min(10, $valorAtual + 10);
    //             if ($sub) return max(0, $valorAtual - 10);
    //             return $valorAtual;
    //         }

    //         // Ajusta os pontos com base nos checkboxes recebidos
    //         $ponto_finalizacao_os = ajustarPonto(
    //             (int)$avaliacao['ponto_finalizacao_os'],
    //             isset($_POST['finalizacao_os_add']),
    //             isset($_POST['finalizacao_os_sub'])
    //         );

    //         $ponto_lavagem_carro = ajustarPonto(
    //             (int)$avaliacao['ponto_lavagem_carro'],
    //             isset($_POST['lavagem_carro_add']),
    //             isset($_POST['lavagem_carro_sub'])
    //         );

    //         $organizacao_material = ajustarPonto(
    //             (int)$avaliacao['organizacao_material'],
    //             isset($_POST['organizacao_material_add']),
    //             isset($_POST['organizacao_material_sub'])
    //         );

    //         $ponto_fardamento = ajustarPonto(
    //             (int)$avaliacao['ponto_fardamento'],
    //             isset($_POST['fardamento_add']),
    //             isset($_POST['fardamento_sub'])
    //         );

    //         // Atualiza somente os campos individuais
    //         $update = $this->db->prepare("UPDATE avaliacao_n2 SET 
    //         ponto_finalizacao_os = ?, 
    //         ponto_lavagem_carro = ?, 
    //         organizacao_material = ?, 
    //         ponto_fardamento = ?
    //         WHERE id_avaliacao_n2 = ?");

    //         $update->execute([
    //             $ponto_finalizacao_os,
    //             $ponto_lavagem_carro,
    //             $organizacao_material,
    //             $ponto_fardamento,
    //             $avaliacao['id_avaliacao_n2']
    //         ]);

    //         $inserirHistorico = $this->db->prepare("
    //             INSERT INTO historico_n2 (
    //                 nome_avaliador,
    //                 data_avaliacao,
    //                 data_infracao,
    //                 pontuacao_anterior,
    //                 pontuacao_atual,
    //                 observacao,
    //                 nome_tecnico,
    //                 id_tecnico
    //             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    //         ");

    //         // Você pode pegar esses dados do contexto
    //         $nome_avaliador = $_POST['nome_avaliador'] ?? 'Sistema'; // ou pegue de sessão/token
    //         $data_avaliacao = date('Y-m-d H:i:s');
    //         $data_infracao = $data; // mesmo valor usado na avaliação
    //         $observacao = $_POST['observacao'] ?? null;
    //         $nome_tecnico = $_POST['nome_tecnico'] ?? 'Desconhecido';
    //         $id_tecnico = $id_tecnico;

    //         // Calcular a soma antes e depois
    //         $pontuacao_anterior =
    //             (int)$avaliacao['ponto_finalizacao_os'] +
    //             (int)$avaliacao['ponto_lavagem_carro'] +
    //             (int)$avaliacao['organizacao_material'] +
    //             (int)$avaliacao['ponto_fardamento'];

    //         $pontuacao_atual =
    //             $ponto_finalizacao_os +
    //             $ponto_lavagem_carro +
    //             $organizacao_material +
    //             $ponto_fardamento;

    //         $inserirHistorico->execute([
    //             $nome_avaliador,
    //             $data_avaliacao,
    //             $data_infracao,
    //             $pontuacao_anterior,
    //             $pontuacao_atual,
    //             $observacao,
    //             $nome_tecnico,
    //             $id_tecnico
    //         ]);

    //         return [
    //             'status' => 'success',
    //             'message' => 'Pontos atualizados com sucesso.',
    //             'dados' => [
    //                 'ponto_finalizacao_os' => $ponto_finalizacao_os,
    //                 'ponto_lavagem_carro' => $ponto_lavagem_carro,
    //                 'organizacao_material' => $organizacao_material,
    //                 'ponto_fardamento' => $ponto_fardamento
    //                 // ponto_total é omitido porque é automático no banco
    //             ]
    //         ];
    //     } catch (PDOException $e) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'Erro no banco de dados: ' . $e->getMessage()
    //         ];
    //     }
    // }


    public function avaliacao_n2($method)
{
    try {
        if ($method != 'POST') {
            return [
                'status' => 'error',
                'message' => 'Requisição inválida'
            ];
        }

        $this->token->verificarToken();

        $data = $_POST['data_requisicao'] ?? date('Y-m-d');
        $id_tecnico = $_POST['id_colaborador'];

        // Buscar a avaliação existente
        $stmt = $this->db->prepare("SELECT * FROM avaliacao_n2 WHERE id_tecnico_n2 = ? AND data_finalizacao = ?");
        $stmt->execute([$id_tecnico, $data]);
        $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$avaliacao) {
            return [
                'status' => 'error',
                'message' => 'Avaliação não encontrada.'
            ];
        }

        $pontuacao_anterior =
            (int)$avaliacao['ponto_finalizacao_os'] +
            (int)$avaliacao['ponto_lavagem_carro'] +
            (int)$avaliacao['organizacao_material'] +
            (int)$avaliacao['ponto_fardamento'];

        $campos = [
            'ponto_finalizacao_os' => ['add' => 'finalizacao_os_add', 'sub' => 'finalizacao_os_sub'],
            'ponto_lavagem_carro'  => ['add' => 'lavagem_carro_add', 'sub' => 'lavagem_carro_sub'],
            'organizacao_material' => ['add' => 'organizacao_material_add', 'sub' => 'organizacao_material_sub'],
            'ponto_fardamento'     => ['add' => 'fardamento_add', 'sub' => 'fardamento_sub'],
        ];

        $camposAtualizados = [];
        foreach ($campos as $campo => $acoes) {
            $valorAtual = (int)$avaliacao[$campo];
            if (isset($_POST[$acoes['add']])) {
                $valorNovo = min(10, $valorAtual + 10);
                $camposAtualizados[$campo] = $valorNovo;
            } elseif (isset($_POST[$acoes['sub']])) {
                $valorNovo = max(0, $valorAtual - 10);
                $camposAtualizados[$campo] = $valorNovo;
            }
        }

        if (empty($camposAtualizados)) {
            return [
                'status' => 'error',
                'message' => 'Nenhuma alteração foi enviada.'
            ];
        }

        // Monta dinamicamente o SQL
        $setSQL = '';
        $params = [];
        foreach ($camposAtualizados as $campo => $valor) {
            $setSQL .= "$campo = :$campo, ";
            $params[":$campo"] = $valor;
        }
        $setSQL = rtrim($setSQL, ', ');

        $params[':id'] = $avaliacao['id_avaliacao_n2'];

        // Atualiza somente os campos alterados
        $update = $this->db->prepare("UPDATE avaliacao_n2 SET $setSQL WHERE id_avaliacao_n2 = :id");
        $update->execute($params);

        // Buscar os dados atualizados
        $stmtNew = $this->db->prepare("SELECT * FROM avaliacao_n2 WHERE id_avaliacao_n2 = ?");
        $stmtNew->execute([$avaliacao['id_avaliacao_n2']]);
        $avaliacaoAtualizada = $stmtNew->fetch(PDO::FETCH_ASSOC);

        $pontuacao_atual =
            (int)$avaliacaoAtualizada['ponto_finalizacao_os'] +
            (int)$avaliacaoAtualizada['ponto_lavagem_carro'] +
            (int)$avaliacaoAtualizada['organizacao_material'] +
            (int)$avaliacaoAtualizada['ponto_fardamento'];

        // Inserir no histórico
        $inserirHistorico = $this->db->prepare("
            INSERT INTO historico_n2 (
                nome_avaliador,
                data_avaliacao,
                data_infracao,
                pontuacao_anterior,
                pontuacao_atual,
                observacao,
                nome_tecnico,
                id_tecnico
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $inserirHistorico->execute([
            $_POST['nome_avaliador'] ?? 'Sistema',
            date('Y-m-d H:i:s'),
            $data,
            $pontuacao_anterior,
            $pontuacao_atual,
            $_POST['observacao'] ?? null,
            $_POST['nome_tecnico'] ?? 'Desconhecido',
            $id_tecnico
        ]);

        return [
            'status' => 'success',
            'message' => 'Pontos atualizados com sucesso.',
            'dados' => [
                'ponto_finalizacao_os' => $avaliacaoAtualizada['ponto_finalizacao_os'],
                'ponto_lavagem_carro' => $avaliacaoAtualizada['ponto_lavagem_carro'],
                'organizacao_material' => $avaliacaoAtualizada['organizacao_material'],
                'ponto_fardamento' => $avaliacaoAtualizada['ponto_fardamento']
            ]
        ];
    } catch (PDOException $e) {
        return [
            'status' => 'error',
            'message' => 'Erro no banco de dados: ' . $e->getMessage()
        ];
    }
}



    /// SERVICE PONTO N2 ///
}
