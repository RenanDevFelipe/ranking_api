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
}

elseif ( $uri == "User/getOne" )
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->getOneUser($data['id'], $method);
}

elseif ( $uri == "User/Delete" )
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->deleteUser($data['id'], $method);
}

elseif ($uri == "User/Post")
{
    $controller->postUser($method);
}

elseif ($uri == "Account/login") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->loginUser($method, $data['email'], $data['password']);
} 
elseif ($uri == "Account/logout") {
    $controller->logout();
} 
elseif ($uri == "Colaborador/GetAll" && $method == "GET") {
    $controller->getAllColaborador();
}

elseif ( $uri == "Colaborador/getOne" && $method == "POST" ){
    $data = $getInput->FileContets();


    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->getOneColaborador($data['id']);

}

elseif ( $uri == "Colaborador/Post" ){
    $controller->postColaborador($method);
}

elseif ($uri == "Colaborador/Delete" && $method = "DELETE") {
    $data = $getInput->FileContets();

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->deleteColaborador($data['id']);
    
} elseif ($uri == "Ranking/mediaDiaria" && $method == "POST") {
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->RankingSucessoTec($data['id'], $data['data_request']);
}

elseif ( $uri == "Ranking/RankingDiario" && $method == "POST" )
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->getRankingDiario($data['data_request']);
}

elseif ($uri == "Departamento/getAll" && $method == "GET") 
{
    $controller->getAllDepartament();
}

elseif ( $uri == "Departamento/Post" )
{
    $controller->postDepartament($method);
}

elseif ( $uri == "Departamento/getOne" )
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->getOneDepartament($data['id_setor']);
}

elseif ( $uri == "Departamento/Delete" )
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->deleteDepartament($method, $data['id_setor']);
}
 
elseif ($uri == "Ranking/MediaMensal" && $method == "POST") 
{

    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->getMediaMensal($data['data_request'], $data['id_tecnico']);
}


////////////////////////////// ############## Tutoriais ################ /////////////////////////////////////////////////////

elseif ($uri == "Tutorial/getAll" && $method == "GET") 
{
    $controller->getAllTutorias();
} 
elseif ($uri == "Tutorial/Post" && $method == "POST") 
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->postTutorial($data['title'], $data['descricao'], $data['url_view'], $data['url_download'], $data['criado_por'], $data['name_icon']);
} 
elseif ($uri == "Tutorial/Delete" && $method == "DELETE") 
{

    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->deleteTutorial($data['id']);
} 
elseif ($uri == "Tutorial/getOne" && $method == "POST") 
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->getOneTutorial($data['id']);
} 

elseif ($uri == "Tutorial/Update" && $method == "PATCH") 
{
    $data = $getInput->FileContets();
    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo (["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->updateTutorial($data['id'], $data['title'], $data['descricao'], $data['url_view'], $data['url_download'], $data['criado_por'], $data['name_icon']);
}


////////////////////////////// ############## Tutoriais ################ /////////////////////////////////////////////////////



elseif ($uri == "Ranking/RankingMensal" && $method == "POST") 
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->getRankingMensal($data['data_request']);


} 

elseif ( $uri == "Ranking/relatorio" && $method == "POST")
{
    $data = $getInput->FileContets();
    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->gerarPlanilhaRankingMensal($data['data_request']);
}

elseif ($uri == "Meta/MetaMensal") 
{
    $data = $getInput->FileContets();

    $controller->getMentaMensal($data['id'], $data['data_request']);
}


elseif ( $uri == "Assunto/Post" )
{
    $controller->postAssuntoOS($method);
}

elseif ( $uri == "Assunto/getAll" && $method == "GET")
{
    $controller->getAllAssuntoOs();
}

elseif ( $uri == "Assunto/getOne" && $method == "POST")
{
    $data = $getInput->FileContets();
    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->getOneAssuntoOs($data['id']);
}

elseif ( $uri == "Assunto/Delete" )
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->deleteAssuntoOs($data['id']);
}


elseif ( $uri == "Checklist/Post" )
{
    $controller->postChecklistField($method);
}

elseif ( $uri == "Checklist/getFiltered" && $method == "POST")
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->checklistFieldGetFiltred($data['id']);

}

elseif ( $uri == "Checklist/Delete" && $method == "DELETE" )
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->checklistFieldDelete($data['id']);
}



elseif ( $uri == "Avaliacao/N2" )
{
    $controller->avaliacao_n2($method);
}

elseif ( $uri == "Historico/N2" && $method == "POST"){
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->logHistoricoN2($method, $data['id_colaborador'], $data['data_requisicao']);

    // echo json_encode($method);
}

elseif ( $uri == "Avaliacao/Estoque" )
{
    $controller->avaliacaoEstoque($method);
}

elseif ( $uri == "Historico/Estoque"  && $method == "POST")
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->logHistoricoEstoque($data['id_colaborador'], $data['data_requisicao']);
}


elseif ($uri == "Avaliacao/Rh")
{
    $controller->avaliacao_rh($method);
}

elseif ( $uri == "Historico/Rh" )
{
    $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    $controller->logHistoricoRh($method, $data['id_colaborador'], $data['data_requisicao']);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////








//Rotas Api IXCSoft


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


elseif ( $uri == "IXCSoft/Arquivo" && $method == "POST" ){
    $data = $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    echo json_encode($ixcController->arquivosOS($data['id_chamado']));
}


// elseif ( $uri == "IXCSoft/listOsFinTec" && $method == "POST" ){
//     $data = json_decode(file_get_contents("php://input"), true);
//     $ixcController->listarOsClienteTecnico($data['query']);
// }

elseif ($uri == "IXCSoft/Teste" && $method == "POST"){
    $data = $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    echo json_encode($ixcController->getIPraduser($data['id_login']));
}

elseif ($uri == "IXCSoft/Teste2" && $method == "POST"){
    $data = $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    echo json_encode($ixcController->clienteFibraOnu($data['id_login']));
}

elseif ($uri == "IXCSoft/Teste3" && $method == "POST"){
    $data = $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    echo json_encode($ixcController->ClienteContrato($data['id_contrato']));
}

elseif ($uri == "Avaliacao/N3")
{
    echo json_encode($ixcController->avalicaoN3($method));
}

else if ($uri == "IXCSoft/ListIpAux")
{
    $data = $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }
    
    echo json_encode($ixcController->ipaux($data['ip_aux']));
}

else if($uri == "IXCSoft/ListSoDepartament"){
    $data = $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    echo json_encode($ixcController->connectBiListSo($data['id_setor']));
}

else if($uri == "IXCSoft/ListSoAssunto"){
    $data = $data = $getInput->FileContets();

    if ($data == null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["erro" => "Erro ao processar JSON: " . json_last_error_msg()]);
        exit;
    }

    echo json_encode($ixcController->listSoAssunto($data['id_assunto']));
}

else {
    echo json_encode([
        "erro" => "Rota inexistente ou Requisição inválida"
    ]);
}
