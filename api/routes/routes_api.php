<?php

require_once __DIR__ . '/../app/Controllers/DataBaseControllers/dataBaseContollers.php';
require_once __DIR__ . "/../app/Services/IXCSoft/Service.php";
require_once __DIR__ . "/../app/Helpers/DataBaseHelpers/CaptureInput.php";

$controller = new DataBaseControllers();
$ixcController = new ApiIXC();
$getInput = new GetFileContens();

$method = $_SERVER['REQUEST_METHOD'];

$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace(['/ranking_api/api/public', '/ranking_api/api'], '', $uri);
$uri = trim(parse_url($uri, PHP_URL_PATH), "/");

if ($uri == "User/listAll" && $method == "GET") {
    $controller->getAllUser();
} elseif ($uri == "Account/login") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->loginUser($method, $data['email'], $data['password']);
} elseif ($uri == "Account/logout") {
    $controller->logout();
} elseif ($uri == "Colaborador/GetAll") {
    $controller->getAllColaborador();
} //elseif ($uri == "Ranking/RankingDiarioGeral" && $method == "POST") {

//     $data = json_decode(file_get_contents("php://input"), true);

//     if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
//         echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
//         exit;
//     }

//     $controller->getRankingDiarioGeral($data['data_request']);} 

elseif ($uri == "Ranking/RankingDiario" && $method == "POST") {
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->RankingSucessoTec($data['id'], $data['data_request']);


} elseif ($uri == "Departamento/getAll" && $method == "GET") {
    $controller->getAllDepartament();
}

elseif ($uri == "Ranking/RankingMensal" && $method == "POST") {

    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->getMediaMensal($data['data_request'], $data['id_tecnico']);
}

/// teste

elseif ($uri == "Ranking/SucessoDiario" && $method == "POST") {
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->verificarSucesso($data['id_atendimento']);
}





//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////








//Rotas Api IXCSoft


// ROTA LISTAGEM DE O.S FINALIZADA DO TECNICO DO DIA ESPECIFICADO
// elseif ($uri == "IXCSoft/listOSFinTec" && $method == "POST") {

//     $data = json_decode(file_get_contents("php://input"), true);
//     if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
//         echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
//         exit;
//     }

//     echo json_encode($ixcController->listarOsClienteTecnico($data['query'], $data['data_fechamento']));
// }

elseif ($uri == "IXCSoft/listOSFinTec" && $method == "POST") {

    $data = json_decode(file_get_contents("php://input"), true);
    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    echo json_encode($ixcController->obterChamadosCompletos($data['query'], $data['data_fechamento']));
}


// ROTA DO QUE RETORNA O CLIENTE
elseif ($uri == "IXCSoft/Cliente" && $method == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    echo json_encode($ixcController->cliente($data['query']));
}


// elseif ( $uri == "IXCSoft/listOsFinTec" && $method == "POST" ){
//     $data = json_decode(file_get_contents("php://input"), true);
//     $ixcController->listarOsClienteTecnico($data['query']);
// }

else {
    echo json_encode([
        "erro" => "Rota inexistente ou Requisição inválida"
    ]);
}
