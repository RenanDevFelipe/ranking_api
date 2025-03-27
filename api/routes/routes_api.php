<?php 

require_once __DIR__ . '/../app/Controllers/DataBaseControllers/dataBaseContollers.php';

$controller = new DataBaseControllers();
$method = $_SERVER['REQUEST_METHOD'];

$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace(['/ranking_api/api/public', '/ranking_api/api'], '', $uri);
$uri = trim(parse_url($uri, PHP_URL_PATH), "/");

if ( $uri == "User/listAll" && $method == "GET" ){
    $controller->getAllUser();
}

elseif ( $uri == "Account/login" ){
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->loginUser($method, $data['email'], $data['password']);
}

else {
    echo json_encode([
        "erro" => "Rota inexistente ou Requisição inválida"
    ]);
}

?>