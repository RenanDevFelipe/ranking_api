<?php

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Trata requisição OPTIONS imediatamente
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


require_once __DIR__ . "/../../Helpers/IXCSoft/dataIxc.php";
require_once __DIR__ . "/../../Helpers/IXCSoft/method.php";
require_once __DIR__ . "/../../Helpers/IXCSoft/qtype.php";
require_once __DIR__ . "/../../../config/ApiIxc/config.php";
require_once __DIR__ . '/../../../config/DataBase/TokenGerator.php';





class ApiIXC
{
    private $baseURL;
    private $username;
    private $password;
    private $body;
    private $methodIXC;
    private $queryIXC;
    private $token;
    private $db;
    private $serviceDB;



    public function __construct()
    {
        $this->body = new DataApiIXC();
        $this->methodIXC = new ListMethod();
        $this->queryIXC = new QtypeRiquisicoesIXC();
        $this->baseURL = constant("URL");
        $this->username = constant("USERNAME");
        $this->password = constant("PASSWORD");
        $this->token = new Token();
        $db = new Database();
        $this->db = $db->getConnection();
        $this->serviceDB = new getDataBase;
    }

    public function listarOsClienteTecnico($query, $data)
    {
        $this->token->verificarToken();
        $body = $this->body->ListAllOsTecnicoFin($query, $data);
        $methodH = $this->methodIXC->listarIXC();

        return $this->request(
            $this->queryIXC->su_chamado_os(),
            "POST",
            $body,
            $methodH
        );

        // return $body;
    }

    public function cliente($query)
    {
        $this->token->verificarToken();
        $body = $this->body->Cliente($query);
        $methodH = $this->methodIXC->listarIXC();

        return $this->request(
            $this->queryIXC->cliente(),
            "POST",
            $body,
            $methodH
        );
    }

    public function arquivosOS($id_chamado)
    {
        $body = $this->body->arquivos($id_chamado);
        $method = $this->methodIXC->listarIXC();

        return $this->request(
            $this->queryIXC->arquivo(),
            "POST",
            $body,
            $method
        );
    }

    public function getIPraduser($id_login)
    {
        $body = $this->body->login($id_login);
        $method = $this->methodIXC->listarIXC();

        return $this->request(
            $this->queryIXC->raduser(),
            "POST",
            $body,
            $method
        );
    }

    public function clienteFibraOnu($id_login)
    {
        $body = $this->body->clienteFibraOnu($id_login);
        $method = $this->methodIXC->listarIXC();

        return $this->request(
            $this->queryIXC->clienteFibraOnu(),
            "POST",
            $body,
            $method
        );
    }

    public function clienteRadio($id_login)
    {
        $body = $this->body->clienteRadio($id_login);
        $method = $this->methodIXC->listarIXC();

        return $this->request(
            $this->queryIXC->clienteRadio(),
            "POST",
            $body,
            $method
        );
    }


    public function getColaboradordb($id)
    {
        $select = $this->db->prepare("SELECT * FROM colaborador WHERE id_ixc = :id");
        $select->execute([":id" => $id]);
        $colaborador = $select->fetch(PDO::FETCH_ASSOC);

        return $colaborador;
    }

    public function obterChamadosCompletos($query, $data)
    {
        $this->token->verificarToken();

        // Passo 1: Buscar O.S finalizadas
        $body = $this->body->ListAllOsTecnicoFin($query, $data);
        $methodH = $this->methodIXC->listarIXC();
        $response = $this->request($this->queryIXC->su_chamado_os(), "POST", $body, $methodH);
        $colaboradorRequest = $this->getColaboradordb($query);
        $id_colaborador = $colaboradorRequest['id_colaborador'];
        $nome_colaborador = $colaboradorRequest['nome_colaborador'];

        $registros = $response['registros'] ?? [];
        $resultadoFinal = [];
        $total = 0;
        $total_registros = $response['total'];

        foreach ($registros as $os) {
            if ($os['id_assunto'] != 2 and $os['id_assunto'] != 380 and $os['id_assunto'] != 79) {
                $id = $os['id'];
                $id_cliente = $os['id_cliente'];
                $ticket = $os['id_ticket'];
                $id_login = $os['id_login'];

                // Passo 2: Usar a função cliente() que já existe no seu código
                $clienteResponse = $this->cliente(['id' => $id_cliente]); // 👈 aqui a função cliente() é usada
                $razao = $clienteResponse['registros'][0]['razao'] ?? 'Cliente não encontrado';

                $arquivoResponse = $this->arquivosOS($id);
                $arquivo = $arquivoResponse['registros'][0]['id'] ?? 'Arquivo não encontrado';

                $ipRequest = $this->getIPraduser($id_login);
                $ip_login = $ipRequest['registros'][0]['ip'];
                $contrato = $ipRequest['registros'][0]['id_contrato'];

                $plano = $this->ClienteContrato($contrato);

                $potenciaRequest = $this->clienteFibraOnu($id_login);
                $sinal_rx = $potenciaRequest['registros'][0]['sinal_rx'];
                $sinal_tx = $potenciaRequest['registros'][0]['sinal_tx'];
                $caixa_FTTH = $potenciaRequest['registros'][0]['id_caixa_ftth'];
                $porta_FTTH = $potenciaRequest['registros'][0]['porta_ftth'];

                $radioRequest = $this->clienteRadio($id_login);
                $radio_ccq = $radioRequest['registros'][0]['ccq'];
                $radio_sinal = $radioRequest['registros'][0]['sinal'];
                $radio_ip = $radioRequest['registros'][0]['lastip'];


                // Passo 3: Buscar checklist no banco de dados
                $stmt = $this->db->prepare("SELECT * FROM avaliacao_n3 WHERE id_os = ?");
                $stmt->execute([$id]);
                $checklistResult = $stmt->fetch(PDO::FETCH_ASSOC);
                $checklist = $checklistResult['check_list'] ?? 'Não preenchido';

                if ($checklistResult > 0) {
                    $status = 'Finalizada';
                    $avaliador = $checklistResult['avaliador'];
                    $nota_os = $checklistResult['nota_os'];
                    $total++;
                } else {
                    $status = 'Aberta';
                    $avaliador = '';
                    $nota_os = '';
                }

                $nota_sucesso = $this->serviceDB->verificarSucesso($ticket);

                // Junta tudo
                $resultadoFinal[] = [
                    'id' => $id,
                    'id_atendimento' => $ticket,
                    'id_arquivo' => $arquivo,
                    'id_cliente' => $id_cliente,
                    'id_assunto' => $os['id_assunto'],
                    'ip_login' => $ip_login,
                    'plano_cliente' => $plano,
                    'potencia' => [
                        'fibra' => [
                            "id_caixa_ftth" => $caixa_FTTH,
                            "porta_ftth" => $porta_FTTH,
                            "tx" => $sinal_tx,
                            "rx" => $sinal_rx
                        ],

                        'radio' => [
                            'ccq' => $radio_ccq,
                            'sinal' => $radio_sinal,
                            'ip' => $radio_ip
                        ]
                    ],
                    'cliente' => $razao,
                    'finalizacao' => $os['data_fechamento'] ?? '',
                    'mensagem' => $os['mensagem_resposta'] ?? '',
                    'checklist' => $checklist,
                    'status' => $status,
                    'avaliador' => $avaliador,
                    'nota_os' => $nota_os,
                    'registros_sucesso' => $nota_sucesso
                ];
            }
        }






        return ([
            "total_registros" => $total_registros,
            "total_os_finalizadas" => $total,
            "id_tecnico" => $id_colaborador,
            "nome_tecnico" => $nome_colaborador,
            "registros" => $resultadoFinal,
        ]);
    }

    public function finalizarOSVerificar($id_atendimento, $text_verificar, $id_troca, $id_ixc_user, $check_list) // Etapa normal sem precisar ser modificado
    {
        $body = $this->body->FinalizarOS($id_atendimento);
        $method = $this->methodIXC->listarIXC();

        $verificacoes = $this->request(
            $this->queryIXC->su_chamado_os(),
            "POST",
            $body,
            $method
        );

        if ($verificacoes['total'] < 1) {
            return ([
                "status" => "error",
                "message" => "Erro ao finalizar no ixc, atendiemento: " . $id_atendimento . " não tem o.s de verificação"
            ]);
        }

        $id_verificar = $verificacoes['registros'][0]['id'];

        // return $id_verificar;

        $bodyFin = $this->body->FinalizarVerificar($id_verificar, $text_verificar, $id_troca, $id_ixc_user);

        $this->request(
            $this->queryIXC->os_finalizar(),
            'POST',
            $bodyFin,
            ''
        );

        // dps de finalizar o.s de verificar ele vem pra etapa de finalizar a de conferencia
        $bodyRequestConferencia = $this->body->BodyRequestConferencia($id_atendimento);

        $conferencias = $this->request(
            $this->queryIXC->su_chamado_os(),
            "POST",
            $bodyRequestConferencia,
            $method
        );

        if ($conferencias['total'] < 1) {
            return ([
                "status" => "error",
                "message" => "Erro ao finalizar no ixc, atendiemento: " . $id_atendimento . " não tem o.s de conferencia"
            ]);
        }

        $id_conferencia = $conferencias['registros'][0]['id'];

        $bodyFinalizarConferencia = $this->body->FinalizarConferencia($id_conferencia, $check_list, $id_ixc_user);

        return $this->request(
            $this->queryIXC->os_finalizar(),
            'POST',
            $bodyFinalizarConferencia,
            ''
        );
    }

    public function mudancaDeEndereco($id_atendimento, $evaluationText, $id_ixc_user, $text_verificar, $id_troca)
    {

        $body = $this->body->FinalizarOS($id_atendimento);
        $method = $this->methodIXC->listarIXC();

        $verificacoes = $this->request(
            $this->queryIXC->su_chamado_os(),
            "POST",
            $body,
            $method
        );

        if ($verificacoes['total'] < 1) {
            return ([
                "status" => "error",
                "message" => "Erro ao finalizar no ixc, atendiemento: " . $id_atendimento . " não tem o.s de verificação"
            ]);
        }

        $id_verificar = $verificacoes['registros'][0]['id'];

        $bodyFin = $this->body->FinalizarVerificar($id_verificar, $text_verificar, $id_troca, $id_ixc_user);

        $this->request(
            $this->queryIXC->os_finalizar(),
            'POST',
            $bodyFin,
            ''
        );



        $body = $this->body->BodyRequestMudancaDeEndereco($id_atendimento);
        $method = $this->methodIXC->listarIXC();

        $verificacoes = $this->request(
            $this->queryIXC->su_chamado_os(),
            'POST',
            $body,
            $method
        );

        if ($verificacoes['total'] < 1) {
            return ([
                'status' => 'error',
                'message' => 'Erro ao finalizar, não tem o.s de verificação para o id: ' . $id_atendimento
            ]);

            exit;
        }

        $id_verificar = $verificacoes['registros'][0]['id'];

        $bodyFinVer = $this->body->BodyRequestVerificarMudanca($id_verificar, $evaluationText, $id_ixc_user);

        return $this->request(
            $this->queryIXC->os_finalizar(),
            'POST',
            $bodyFinVer,
            ''
        );
    }

    public function instalacao($id_atendimento, $evaluationText, $id_ixc_user)
    {
        $body = $this->body->BodyRequestConferencia($id_atendimento);
        $method = $this->methodIXC->listarIXC();

        $conferencias = $this->request(
            $this->queryIXC->su_chamado_os(),
            'POST',
            $body,
            $method
        );

        if ($conferencias['total'] < 1) {
            return ([
                'status' => 'error',
                'message' => 'Erro ao finalizar, não tem o.s de verificação para o id: ' . $id_atendimento
            ]);

            exit;
        }

        $id_conferencia = $conferencias['registros'][0]['id'];

        $body = $this->body->BodyRequestConferenciaInstalacao($id_conferencia, $evaluationText, $id_ixc_user);

        return $this->request(
            $this->queryIXC->os_finalizar(),
            'POST',
            $body,
            ''
        );
    }

    public function camera($id_atendimento, $text_verificar, $id_troca, $id_ixc_user, $evaluationText)
    {
        $body = $this->body->FinalizarOS($id_atendimento);
        $method = $this->methodIXC->listarIXC();

        $verificacoes = $this->request(
            $this->queryIXC->su_chamado_os(),
            "POST",
            $body,
            $method
        );

        if ($verificacoes['total'] < 1) {
            return ([
                "status" => "error",
                "message" => "Erro ao finalizar no ixc, atendiemento: " . $id_atendimento . " não tem o.s de verificação"
            ]);
        }

        $id_verificar = $verificacoes['registros'][0]['id'];

        $bodyFin = $this->body->FinalizarVerificar($id_verificar, $text_verificar, $id_troca, $id_ixc_user);

        $this->request(
            $this->queryIXC->os_finalizar(),
            'POST',
            $bodyFin,
            ''
        );


        $body = $this->body->BodyRequestConferencia($id_atendimento);
        $method = $this->methodIXC->listarIXC();

        $conferencias = $this->request(
            $this->queryIXC->su_chamado_os(),
            'POST',
            $body,
            $method
        );

        if ($conferencias['total'] < 1) {
            return ([
                'status' => 'error',
                'message' => 'Erro ao finalizar, não tem o.s de verificação para o id: ' . $id_atendimento
            ]);

            exit;
        }

        $id_conferencia = $conferencias['registros'][0]['id'];

        $body = $this->body->BodyRequestConferenciaCamera($id_conferencia, $evaluationText, $id_ixc_user);

        return $this->request(
            $this->queryIXC->os_finalizar(),
            'POST',
            $body,
            ''
        );
    }

    // DATA BASE REQUEST AVALIACAO N3 //

    public function avalicaoN3($method)
    {
        try {

            if ($method !== "POST") {
                return ([
                    'status' => 'error',
                    'message' => 'Riquisição inválida'
                ]);
            }

            $this->token->verificarToken();

            $id_os = $_POST['id_os'];
            $desc_os = $_POST['desc_os'];
            $pontuacao_os = $_POST['pontuacao_os'];
            $nota_os = $_POST['nota_os'];
            $data_finalizacao_os = $_POST['data_finalizacao_os'];
            $data_finalizacao = $_POST['data_finalizacao'];
            $id_tecnico = $_POST['id_tecnico'];
            $id_setor = $_POST['id_setor'];
            $avaliador = $_POST['avaliador'];
            $check_list = $_POST['check_list'];
            $id_assunto = $_POST['id_assunto'];
            $id_atendimento = $_POST['id_atendimento'];
            $observacao_troca = $_POST['observacao_troca'];
            $troca = $_POST['troca'];
            $id_ixc_user = $_POST['id_ixc'];


            $verificar = $this->db->prepare("SELECT * FROM avaliacao_n3 WHERE id_os = :id");
            $verificar->execute([':id' => $id_os]);
            $count = $verificar->rowCount();

            if ($count > 0) {

                $update = $this->db->prepare("UPDATE avaliacao_n3 SET desc_os = :desc_os, pontuacao_os = :pontuacao_os, nota_os = :nota_os, data_finalizacao_os = :data_finalizacao_os, data_finalizacao = :data_finalizacao, id_tecnico = :id_tecnico, id_setor = :id_setor, avaliador = :avaliador, check_list = :check_list WHERE id_os = :id");
                $success = $update->execute([
                    ':id' => $id_os,
                    ':desc_os' => $desc_os,
                    ':pontuacao_os' => $pontuacao_os,
                    ':nota_os' => $nota_os,
                    ':data_finalizacao_os' => $data_finalizacao_os,
                    ':data_finalizacao' => $data_finalizacao,
                    ':id_tecnico' => $id_tecnico,
                    ':id_setor' => $id_setor,
                    ':avaliador' => $avaliador,
                    ':check_list' => $check_list
                ]);

                if ($success) {
                    return ([
                        'status' => 'success',
                        'message' => 'Avaliação atualizada com sucesso'
                    ]);
                } else {
                    return ([
                        'status' => 'error',
                        'message' => 'Erro ao atualizar avaliação'
                    ]);
                }
            } else {
                $insert = $this->db->prepare("INSERT INTO avaliacao_n3 (id_os, desc_os, pontuacao_os, nota_os, data_finalizacao_os, data_finalizacao, id_tecnico, id_setor, avaliador, check_list) VALUES (:id_os, :desc_os, :pontuacao_os, :nota_os, :data_finalizacao_os, :data_finalizacao, :id_tecnico, :id_setor, :avaliador, :check_list)");
                $success = $insert->execute([
                    ':id_os' => $id_os,
                    ':desc_os' => $desc_os,
                    ':pontuacao_os' => $pontuacao_os,
                    ':nota_os' => $nota_os,
                    ':data_finalizacao_os' => $data_finalizacao_os,
                    ':data_finalizacao' => $data_finalizacao,
                    ':id_tecnico' => $id_tecnico,
                    ':id_setor' => $id_setor,
                    ':avaliador' => $avaliador,
                    ':check_list' => $check_list
                ]);

                if ($success) {

                    if ($id_assunto === '10' || $id_assunto === '187' || $id_assunto === '425' || $id_assunto === '308' || $id_assunto === '420' || $id_assunto === '314' || $id_assunto === '419' || $id_assunto === '189' || $id_assunto === '503') {
                        $this->instalacao($id_atendimento, $check_list, $id_ixc_user);
                    } elseif ($id_assunto === '503' || $id_assunto === '421') {
                        $this->camera($id_atendimento, $observacao_troca, $troca, $id_ixc_user, $check_list);
                    } elseif ($id_assunto === '5' || $id_assunto === '70') {
                        $this->mudancaDeEndereco($id_atendimento, $check_list, $id_ixc_user, $observacao_troca, $troca);
                    } else {
                        return $this->finalizarOSVerificar($id_atendimento, $observacao_troca, $troca, $id_ixc_user, $check_list);
                    }

                    return ([
                        'status' => 'success',
                        'message' => 'Avaliação inserida com sucesso'
                    ]);
                } else {
                    return ([
                        'status' => 'error',
                        'message' => 'Erro ao inserir avaliação'
                    ]);
                }
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no banco de dados: " . $e
            ];
        }
    }

    // DATA BASE REQUEST AVALIACAO N3 //


    public function ipaux($query)
    {
        $body = $this->body->BodyRequestLoginIpAux($query);
        $method = $this->methodIXC->listarIXC();

        return $this->request(
            $this->queryIXC->raduser(),
            'POST',
            $body,
            $method
        );
    }

    public function ClienteContrato($id_contrato)
    {
        $body = $this->body->BodyRequestContrato($id_contrato);
        $method = $this->methodIXC->listarIXC();
        $request = $this->request(
            $this->queryIXC->cliente_contrato(),
            "POST",
            $body,
            $method
        );

        $contrato = $request['registros'][0]['contrato'];

        return $this->extrairCidadeVelocidade($contrato);
    }

    function extrairCidadeVelocidade($contrato)
    {
        $partes = explode('_', $contrato);

        // Cidade: primeiras duas partes separadas por espaço
        $cidade = $partes[0] . ' ' . $partes[1];

        // Velocidade: parte que contém 'MEGA' ou 'MB'
        $velocidade = null;
        foreach ($partes as $parte) {
            if (stripos($parte, 'MEGA') !== false || stripos($parte, 'MB') !== false) {
                $velocidade = $parte;
                break;
            }
        }

        return [
            'cidade' => $cidade,
            'velocidade' => $velocidade
        ];
    }

    private function request($endpoint, $method = "GET", $data = [], $methodHeader)
    {
        $url = $this->baseURL . $endpoint;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);

        $headers = [
            "Accept: application/json",
            "Content-Type: application/json",
            "Accept-Encoding: gzip", // gzip
            $methodHeader
        ];

        if ($method !== "GET") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, ""); // ativa gzip
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $start = microtime(true);
        $response = curl_exec($ch);
        $duration = microtime(true) - $start;
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                "erro" => "Erro cURL: $error",
                "status" => 0
            ];
        }

        curl_close($ch);

        error_log("IXC API [$method $url] - Tempo: " . round($duration, 2) . "s - Status: $httpCode");

        if ($httpCode !== 200) {
            return [
                "erro" => "Erro HTTP $httpCode ao acessar a API do IXC.",
                "status" => $httpCode,
                "resposta" => $response
            ];
        }

        return json_decode($response, true);
    }

    // private function getCachedData($key, callable $callback, $ttl = 600)
    // {
    //     $cacheDir = __DIR__. "/../../../cache/ixc";
    //     if (!is_dir($cacheDir)) {
    //         mkdir($cacheDir, 0777, true);
    //     }

    //     $file = $cacheDir . '/' . md5($key) . '.json';

    //     if (file_exists($file) && (time() - filemtime($file)) < $ttl) {
    //         return json_decode(file_get_contents($file), true);
    //     }

    //     $data = $callback();
    //     file_put_contents($file, json_encode($data));
    //     return $data;
    // }

    // private function getAssuntoCached($id)
    // {
    //     return $this->getCachedData("assunto_{$id}", function () use ($id) {
    //         return $this->listSoAssunto($id);
    //     });
    // }

    // private function getTecnicoCached($id)
    // {
    //     return $this->getCachedData("tecnico_{$id}", function () use ($id) {
    //         return $this->colaboratorApi($id);
    //     });
    // }


    // TI CONNECT BI //
    public function listSoAssunto($query)
    {
        $body = $this->body->listAssunto($query);
        $method = $this->methodIXC->listarIXC();
        $return = $this->request(
            $this->queryIXC->su_oss_assunto(),
            "POST",
            $body,
            $method
        );

        return $return['registros'][0]['assunto'];
    }

    public function colaboratorApi($query)
    {
        $body = $this->body->searchColaboratorApi($query);
        $method = $this->methodIXC->listarIXC();
        $return = $this->request(
            $this->queryIXC->funcionarios(),
            "POST",
            $body,
            $method
        );

        return $return['registros'][0]['funcionario'];
    }

    public function connectBiListSo($query)
    {
        $this->token->verificarToken();

        $body = $this->body->listSoDepartament($query);
        $method = $this->methodIXC->listarIXC();
        $return = $this->request(
            $this->queryIXC->su_chamado_os(),
            "POST",
            $body,
            $method
        );

        $serviceOrdens = $return['registros'] ?? [];
        $total = $return['total'];

        // Cache para evitar chamadas duplicadas
        $cacheAssuntos = [];
        $cacheTecnicos = [];

        // Contadores
        $contagem_id_assunto = [];
        $statusCounts = [
            'A' => ['total' => 0, 'dados' => []],
            'AN' => ['total' => 0, 'dados' => []],
            'EN' => ['total' => 0, 'dados' => []],
            'AS' => ['total' => 0, 'dados' => []],
            'AG' => ['total' => 0, 'dados' => []],
            'DS' => ['total' => 0, 'dados' => []],
            'EX' => ['total' => 0, 'dados' => []],
            'RAG' => ['total' => 0, 'dados' => []]
        ];

        foreach ($serviceOrdens as $SO) {
            $id_assunto = $SO['id_assunto'];
            $id_tecnico = $SO['id_tecnico'];
            $status = $SO['status'];

            // Cache de assunto
            if (!isset($cacheAssuntos[$id_assunto])) {
                $assunto_id = $this->dbIXCSubject($id_assunto);
                $cacheAssuntos[$id_assunto] = $assunto_id['assunto'];
            }
            $assunto = $cacheAssuntos[$id_assunto];

            // Cache de técnico
            if (!isset($cacheTecnicos[$id_tecnico])) {
                $tecnico_id = $this->dbIXCFuncionarios($id_tecnico);
                $cacheTecnicos[$id_tecnico] = $tecnico_id['colaborador'];
            }
            $tecnico = $cacheTecnicos[$id_tecnico];

            // Contagem por assunto
            if (!isset($contagem_id_assunto[$assunto])) {
                $contagem_id_assunto[$assunto] = 0;
            }
            $contagem_id_assunto[$assunto]++;

            // Adiciona aos dados conforme status
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]['dados'][] = [
                    'id' => $SO['id'],
                    'mensagem' => $SO['mensagem'],
                    'id_tecnico' => $tecnico,
                    'status' => $status,
                    'prioridade' => $SO['prioridade'],
                    'assunto' => $assunto
                ];
                $statusCounts[$status]['total']++;
            }
        }

        return [
            'total' => $total,
            'id_assunto_count' => $contagem_id_assunto,
            'registros' => [
                'aberta' => [
                    'total' => $statusCounts['A']['total'],
                    'services_ordem' => $statusCounts['A']['dados']
                ],
                'analise' => [
                    'total' => $statusCounts['AN']['total'],
                    'services_ordem' => $statusCounts['AN']['dados']
                ],
                'encaminhada' => [
                    'total' => $statusCounts['EN']['total'],
                    'services_ordem' => $statusCounts['EN']['dados']
                ],
                'assumida' => [
                    'total' => $statusCounts['AS']['total'],
                    'services_ordem' => $statusCounts['AS']['dados']
                ],
                'agendada' => [
                    'total' => $statusCounts['AG']['total'],
                    'services_ordem' => $statusCounts['AG']['dados']
                ],
                'deslocamento' => [
                    'total' => $statusCounts['DS']['total'],
                    'services_ordem' => $statusCounts['DS']['dados']
                ],
                'execucao' => [
                    'total' => $statusCounts['EX']['total'],
                    'services_ordem' => $statusCounts['EX']['dados']
                ],
                'reagendamento' => [
                    'total' => $statusCounts['RAG']['total'],
                    'services_ordem' => $statusCounts['RAG']['dados']
                ]
            ]
        ];
    }

    public function getSoAllDepartament()
    {
        $body = $this->body->listAllSoDepartament();
        $method = $this->methodIXC->listarIXC();
        $return = $this->request(
            $this->queryIXC->su_chamado_os(),
            "POST",
            $body,
            $method
        );

        return $return;
    }

    public function getSoGroupedByStatus()
    {
        $this->token->verificarToken();
        $original = $this->getSoAllDepartament();
        $registros = $original['registros'] ?? [];

        $resultado = [
            'total' => count($registros),
            'registros' => []
        ];

        foreach ($registros as $os) {
            $assunto_id = $this->dbIXCSubject($os['id_assunto']);
            $tecnico_id = $this->dbIXCFuncionarios($os['id_tecnico']);

            $setor = $os['setor'] ?? 'indefinido';
            $status = $os['status'] ?? 'indefinido';
            $assunto = $assunto_id['assunto'] ?? 'indefinido';
            $descrition = $os['mensagem'];
            $data_abertura = $os['data_abertura'];
            $status = $os['status'];

            // Inicializa o setor
            if (!isset($resultado['registros'][$setor])) {
                $resultado['registros'][$setor] = [
                    'total' => 0,
                    'status' => []
                ];
            }

            // Inicializa o status dentro do setor
            if (!isset($resultado['registros'][$setor]['status'][$status])) {
                $resultado['registros'][$setor]['status'][$status] = [
                    'total' => 0,
                    'assuntos' => []
                ];
            }

            // Inicializa o assunto dentro do status
            if (!isset($resultado['registros'][$setor]['status'][$status]['assuntos'][$assunto])) {
                $resultado['registros'][$setor]['status'][$status]['assuntos'][$assunto] = [
                    'total' => 0,
                    'services_ordem' => []
                ];
            }

            // Adiciona a ordem de serviço
            $resultado['registros'][$setor]['status'][$status]['assuntos'][$assunto]['services_ordem'][] = [
                'id' => $os['id'],
                'setor' => $setor,
                'id_tecnico' => $tecnico_id['colaborador'],
                'assunto' => $assunto,
                'descricao' => $descrition,
                'data_abertura' => $data_abertura,
                'status' => $status
            ];

            // Incrementa os totais
            $resultado['registros'][$setor]['total']++;
            $resultado['registros'][$setor]['status'][$status]['total']++;
            $resultado['registros'][$setor]['status'][$status]['assuntos'][$assunto]['total']++;
        }

        return $resultado;
    }

    public function listAllAssunto($page = 1, $limit = 100)
    {
        $body = $this->body->listAllAssunto();

        $method = $this->methodIXC->listarIXC(); // Autenticação/token se necessário

        $return = $this->request(
            $this->queryIXC->su_oss_assunto(), // Sem query string na URL
            "POST", // Troca de GET para POST
            $body,
            $method
        );

        return $return;
    }

    public function insertSubjectIXC()
    {
        $return = $this->listAllAssunto();
        $subjects = $return['registros'];

        $successCount = 0;
        $failures = [];

        foreach ($subjects as $sub) {
            try {
                $stmt = $this->db->prepare("INSERT IGNORE INTO assuntos_ixc (name, id_ixc) VALUES (:name, :id_ixc)");
                $stmt->execute([
                    ':name' => $sub['assunto'],
                    ':id_ixc' => $sub['id']
                ]);

                if ($stmt->rowCount() > 0) {
                    $successCount++;
                }
            } catch (PDOException $e) {
                $failures[] = [
                    'id' => $sub['id'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'status' => empty($failures) ? 'success' : 'partial',
            'message' => "Foram inseridos $successCount assuntos.",
            'failures' => $failures
        ];
    }

    public function searchAllColaboratorApi()
    {
        $body = $this->body->searchAllColaboratorApi();
        $method = $this->methodIXC->listarIXC();
        return $this->request(
            $this->queryIXC->funcionarios(),
            "POST",
            $body,
            $method
        );
    }

    public function insertColaboratorIxc()
    {
        $return = $this->searchAllColaboratorApi();
        $subjects = $return['registros'];

        $successCount = 0;
        $failures = [];

        foreach ($subjects as $sub) {
            try {
                $stmt = $this->db->prepare("INSERT IGNORE INTO funcionarios_ixc (name, id_ixc, email_ixc) VALUES (:name, :id_ixc, :email_ixc)");
                $stmt->execute([
                    ':name' => $sub['funcionario'],
                    ':id_ixc' => $sub['id'],
                    ':email_ixc' => $sub['email']
                ]);

                if ($stmt->rowCount() > 0) {
                    $successCount++;
                }
            } catch (PDOException $e) {
                $failures[] = [
                    'id' => $sub['id'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'status' => empty($failures) ? 'success' : 'partial',
            'message' => "Foram inseridos $successCount assuntos.",
            'failures' => $failures
        ];
    }

    public function dbIXCFuncionarios($id)
    {
        try {

            $stmt = $this->db->prepare("SELECT * FROM funcionarios_ixc WHERE id_ixc = :id_ixc");
            $stmt->execute([
                ':id_ixc' => $id
            ]);

            $colaborator = $stmt->fetch(PDO::FETCH_ASSOC);

            if($stmt->rowCount() > 0){
                return [
                    'colaborador' => $colaborator['name']
                ];
            } else {
                return [
                    'colaborador' => 'Não atribuido'
                ];
            }


        } catch (PDOException $e){
            return [
                'status' => 'error',
                'message' => 'Erro no banco de dados: ' . $e->getMessage()
            ];
        }
    }

    public function dbIXCSubject($id)
    {
        try {

            $stmt = $this->db->prepare("SELECT * FROM assuntos_ixc WHERE id_ixc = :id_ixc");
            $stmt->execute([
                ':id_ixc' => $id
            ]);

            $subject = $stmt->fetch(PDO::FETCH_ASSOC);

            if($stmt->rowCount() > 0){
                return [
                    'assunto' => $subject['name']
                ];
            } else {
                return [
                    'assunto' => 'Não atribuido'
                ];
            }


        } catch (PDOException $e){
            return [
                'status' => 'error',
                'message' => 'Erro no banco de dados: ' . $e->getMessage()
            ];
        }
    }
}
    // TI CONNECT BI //
