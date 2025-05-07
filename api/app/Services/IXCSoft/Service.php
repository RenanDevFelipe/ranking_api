<?php

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


    public function obterChamadosCompletos($query, $data)
    {
        $this->token->verificarToken();

        // Passo 1: Buscar O.S finalizadas
        $body = $this->body->ListAllOsTecnicoFin($query, $data);
        $methodH = $this->methodIXC->listarIXC();
        $response = $this->request($this->queryIXC->su_chamado_os(), "POST", $body, $methodH);

        $registros = $response['registros'] ?? [];
        $resultadoFinal = [];
        $total = 0;
        $total_registros = $response['total'];

        foreach ($registros as $os) {
            if ($os['id_assunto'] != 2) {
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

                $potenciaRequest = $this->clienteFibraOnu($id_login);
                $sinal_rx = $potenciaRequest['registros'][0]['sinal_rx'];
                $sinal_tx = $potenciaRequest['registros'][0]['sinal_tx'];

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
                    'potencia' => [
                        'fibra' => [
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
            "registros" => $resultadoFinal,
        ]);
    }



    private function request($endpoint, $method = "GET", $data = [], $methodHeader)
    {
        $url = $this->baseURL . $endpoint;

        $ch = curl_init($url);

        //configurar Basic Auth
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);

        //configurar Cabeçalhos
        $headers = [
            "Accept: application/json",
            "Content-Type: application/json",
            $methodHeader
        ];

        if ($method !== "GET") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //envia o json no corpo
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Executa requisiçao
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ([
                "erro" => "Erro ao conectar com a API do IXCSoft.",
                "status" => $httpCode
            ]);
        }

        return json_decode($response, true);

        // return $data;
    }
}
