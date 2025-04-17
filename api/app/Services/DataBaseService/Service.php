<?php

require_once __DIR__ . '/../../../config/DataBase/dataBase.php';
require_once __DIR__ . '/../../../config/DataBase/TokenGerator.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
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
            if ($method == "POST") {

                $this->token->verificarToken();

                // $id_colaborador = $_POST['id_colaborador'] ?? null;
                $id_ixc = $_POST['id_ixc'] ?? null;
                $nome   = $_POST['nome_colaborador'] ?? null;
                $setor  = $_POST['setor_colaborador'] ?? null;

                $stmt = $this->db->prepare("SELECT * FROM colaborador WHERE id_ixc = :id_ixc");
                $stmt->execute([
                    ":id_ixc" => $id_ixc
                ]);

                if ($stmt->rowCount() > 0) {
                    return ([
                        "status" => "error",
                        "message" => "Colaborador já está cadastrado!"
                    ]);
                    exit;
                }

                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                    $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                    $nomeImagem = uniqid('colaborador_') . '.' . $extensao;
                    $caminho = 'ticonnecte.com.br/ranking_api/api/uploads/' . $nomeImagem;
                
                    if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
                        return [
                            "status" => "error",
                            "message" => "Erro ao salvar a imagem"
                        ];
                    }
                
                    // CORRIGIDO: Removido o else que dava erro sempre
                    $stmt = $this->db->prepare("INSERT INTO colaborador (id_ixc, nome_colaborador, setor_colaborador, url_image) VALUES (:id, :nome, :setor, :url_image)");
                    $success = $stmt->execute([
                        ":id" => $id_ixc,
                        ":nome" => $nome,
                        ":setor" => $setor,
                        ":url_image" => $caminho
                    ]);
                
                    if ($success) {
                        return [
                            "status" => "success",
                            "message" => "Colaborador cadastrado com sucesso",
                            "url_imagem" => $caminho
                        ];
                    } else {
                        return [
                            "status" => "error",
                            "message" => "Erro ao cadastrar Colaborador"
                        ];
                    }
                }



            } elseif ($method == "PATCH") {
                $stmt = $this->db->prepare("SELECT * FROM colaborador WHERE id_colaborador = :id");

                return ([
                    "teste" => "Method PATCH"
                ]);
            } else {
                return ([
                    "status" => "error",
                    "message" => "Riquisição inválida"
                ]);
            }
        } catch (PDOException $e) {
            return ([
                "status" => "error",
                "message" => "Erro no banco de dados: " . $e->getMessage()
            ]);
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
}
