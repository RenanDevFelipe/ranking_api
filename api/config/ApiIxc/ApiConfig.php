<?php

// require_once __DIR__ . "/config.php";

// class ApiIxcRequest
// {

//     private $baseURL;
//     private $username;
//     private $password;

//     public function __construct()
//     {
//         $this->baseURL = constant("URL");
//         $this->username = constant("USERNAME");
//         $this->password = constant("PASSWORD");
//     }

//     public function request($endpoint, $method = "GET", $data = [], $methodHeader)
//     {
//         $url = $this->baseURL . $endpoint;

//         $ch = curl_init($url);

//         // Configurar Basic Auth
//         curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);

//         // Configurar Cabeçalhos
//         $headers = [
//             "Accept: application/json",
//             "Content-Type: application/json",
//             $methodHeader
//         ];

//         if ($method !== "GET") {
//             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
//             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Envia o JSON

//             // Log para depuração
//             error_log("Enviando JSON: " . json_encode($data));
//         }

//         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//         // Executa requisição
//         $response = curl_exec($ch);
//         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//         curl_close($ch);

//         // Log da resposta da API
//         error_log("Código HTTP: " . $httpCode);
//         error_log("Resposta da API: " . $response);

//         if ($httpCode !== 200) {
//             return json_encode([
//                 "erro" => "Erro ao conectar com a API do IXCSoft.",
//                 "status" => $httpCode
//             ]);
//         }

//         return json_decode($response, true);
//     }
// }

?>