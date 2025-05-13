<?php
require_once '../../../core/core.php'; // Verifique o caminho para o arquivo de conexão
require_once '../../../api/api.php';

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');
$missing_fields = array();

$idOS = $_GET['idOS'];
$currentDateTime = date('Y-m-d H:i:s');

$id_troca = $_POST['select_option'];
$text_verificar = $_POST['extra_input'];

$atendimento = htmlspecialchars(trim($_POST['idAtendimento_' . $idOS]));
$id_ixc_user = htmlspecialchars(trim($_POST['idIxc_' . $idOS]));
$id_assunto = htmlspecialchars(trim($_POST['idForm_' . $idOS]));
$assunto_instalacao = htmlspecialchars(trim($_POST['formId_' . $idOS]));

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se todos os campos necessários estão presentes e não estão vazios
    $required_fields = array(
        'descOS_' . $idOS,
        'fechamentoOS_' . $idOS,
        'formId_' . $idOS,
        'notaOS_' . $idOS,
        'pontuacaoOS_' . $idOS,
        'setor_' . $idOS,
        'idTecnico_' . $idOS
    );

    $evaluationText = $_POST['evaluationText'];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (empty($missing_fields)) {
        // Escapa a entrada do usuário para evitar injeção de SQL
        $obs = htmlspecialchars(trim($_POST['obs_' . $idOS]));
        $desc = htmlspecialchars(trim($_POST['descOS_' . $idOS]));
        $fechamento = htmlspecialchars(trim($_POST['fechamentoOS_' . $idOS]));
        $formId = htmlspecialchars(trim($_POST['formId_' . $idOS]));
        $notaOS = htmlspecialchars(trim($_POST['notaOS_' . $idOS]));
        $pontuacao = htmlspecialchars(trim($_POST['pontuacaoOS_' . $idOS]));
        $setor = htmlspecialchars(trim($_POST['setor_' . $idOS]));
        $dataAvaliacao = date('Y-m-d');
        $idTecnico = htmlspecialchars(trim($_POST['idTecnico_' . $idOS]));
        $avaliador = htmlspecialchars(trim($_POST['avaliador_' . $idOS]));

        try {
            // Consulta para verificar se o avaliacao já existe
            $sql = $pdo->prepare('SELECT * FROM avaliacao_n3 WHERE id_os = ?');
            $sql->execute([$idOS]);

            if ($sql->rowCount() > 0) {
                $response['message'] = 'Avaliação ja existe!';
            } else {
                // Insere o novo colaborador no banco de dados
                $insert = $pdo->prepare('INSERT INTO avaliacao_n3 (id_os, desc_os, pontuacao_os, nota_os, data_finalizacao_os, data_finalizacao, id_tecnico, id_setor, avaliador , check_list) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?,?)');
                if ($insert->execute([$idOS, $desc, $pontuacao, $notaOS, $fechamento, $dataAvaliacao, $idTecnico, $setor, $avaliador, $evaluationText])) {
                    $response['success'] = true;
                    $response['message'] = 'Avaliação feita com sucesso!';

                    // Parâmetros para consulta na API
                    $params = array(
                        'qtype' => 'su_oss_chamado.id_ticket',
                        'query' => $atendimento,
                        'oper' => '=',
                        'page' => '1',
                        'rp' => '20',
                        'sortname' => 'su_oss_chamado.id',
                        'sortorder' => 'desc',
                        'grid_param' => json_encode(array(
                            array('TB' => 'su_oss_chamado.setor', 'OP' => '=', 'P' => '7'),
                            array('TB' => 'su_oss_chamado.id_assunto', 'OP' => 'IN', 'P' => '264,453,358')
                        ))
                    );

                    // Faz a requisição GET na API
                    $api->get('su_oss_chamado', $params);
                    $retorno = $api->getRespostaConteudo(false);
                    $teste_2 = json_decode($retorno);

                    if ($teste_2 && $teste_2->total > 0) {
                        $id_os_verificar = $teste_2->registros[0]->id;

                        // Dados para fechar o chamado
                        $dados = array(
                            'id_chamado' => $id_os_verificar, // ID da O.S
                            'data_inicio' => $currentDateTime, // Data e Hora de Início da finalização
                            'data_final' => $currentDateTime, // Data e Hora de Final da finalização
                            'mensagem' => $text_verificar, // Mensagem
                            'gera_comissao' => '', // "N" para Não "S" para Sim
                            'id_su_diagnostico' => '', // ID do diagnóstico (não obrigatório)
                            'id_proxima_tarefa' => $id_troca,
                            'finaliza_processo' => 'N', // "S" para finalizar o processo
                            'status' => 'F', // Status "F" para finalizar
                            'id_tecnico' => $id_ixc_user // ID do técnico responsável

                        );

                        // Execução do método POST na API
                        $api->post('su_oss_chamado_fechar', $dados);

                        // Obtém a resposta da API
                        $retorno = $api->getRespostaConteudo(false);
                    }

                    if ($id_assunto == 70 || $id_assunto == 5 || $assunto_instalacao == 5) {
                        // Parâmetros para consulta na API
                        $params = array(
                            'qtype' => 'su_oss_chamado.id_ticket',
                            'query' => $atendimento,
                            'oper' => '=',
                            'page' => '1',
                            'rp' => '20',
                            'sortname' => 'su_oss_chamado.id',
                            'sortorder' => 'desc',
                            'grid_param' => json_encode(array(
                                array('TB' => 'su_oss_chamado.setor', 'OP' => '=', 'P' => '7'),
                                array('TB' => 'su_oss_chamado.id_assunto', 'OP' => '=', 'P' => '149')
                            ))
                        );

                        // Faz a requisição GET na API
                        $api->get('su_oss_chamado', $params);
                        $retorno = $api->getRespostaConteudo(false);
                        $teste_1 = json_decode($retorno);

                        if ($teste_1 && $teste_1->total > 0) {
                            $id_os_conferencia = $teste_1->registros[0]->id;

                            // Dados para fechar o chamado
                            $dados = array(
                                'id_chamado' => $id_os_conferencia, // ID da O.S
                                'data_inicio' => $currentDateTime, // Data e Hora de Início da finalização
                                'data_final' => $currentDateTime, // Data e Hora de Final da finalização
                                'mensagem' => $evaluationText, // Mensagem
                                'gera_comissao' => '', // "N" para Não "S" para Sim
                                'id_su_diagnostico' => '', // ID do diagnóstico (não obrigatório)
                                'id_proxima_tarefa' => '50',
                                'proxima_sequencia_forcada' => '7',
                                'finaliza_processo' => 'N', // "S" para finalizar o processo
                                'status' => 'F', // Status "F" para finalizar
                                'id_tecnico' => $id_ixc_user // ID do técnico responsável
                            );

                            // Execução do método POST na API
                            $api->post('su_oss_chamado_fechar', $dados);

                            // Obtém a resposta da API
                            $retorno = $api->getRespostaConteudo(false);
                        }
                    } else {

                        $params = array(
                            'qtype' => 'su_oss_chamado.id_ticket',
                            'query' => $atendimento,
                            'oper' => '=',
                            'page' => '1',
                            'rp' => '20',
                            'sortname' => 'su_oss_chamado.id',
                            'sortorder' => 'desc',
                            'grid_param' => json_encode(array(
                                array('TB' => 'su_oss_chamado.setor', 'OP' => '=', 'P' => '7'),
                                array('TB' => 'su_oss_chamado.id_assunto', 'OP' => 'IN', 'P' => '95,85,149,310,454,359,255,411')
                            ))
                        );

                        // Faz a requisição GET na API
                        $api->get('su_oss_chamado', $params);
                        $retorno = $api->getRespostaConteudo(false);
                        $teste_1 = json_decode($retorno);

                        if ($teste_1 && $teste_1->total > 0) {
                            $id_os_conferencia = $teste_1->registros[0]->id;

                            if ($assunto_instalacao == 10) {
                                $dados = array(
                                    'id_chamado' => $id_os_conferencia, // ID da O.S
                                    'data_inicio' => $currentDateTime, // Data e Hora de Início da finalização
                                    'data_final' => $currentDateTime, // Data e Hora de Final da finalização
                                    'mensagem' => $evaluationText, // Mensagem
                                    'gera_comissao' => '', // "N" para Não "S" para Sim
                                    'id_su_diagnostico' => '', // ID do diagnóstico (não obrigatório)
                                    'id_proxima_tarefa' => '478',
                                    'finaliza_processo' => 'N', // "S" para finalizar o processo
                                    'status' => 'F', // Status "F" para finalizar
                                    'id_tecnico' => $id_ixc_user // ID do técnico responsável
                                );

                                // Execução do método POST na API
                                $api->post('su_oss_chamado_fechar', $dados);

                                // Obtém a resposta da API
                                $retorno = $api->getRespostaConteudo(false);
                            } elseif ($assunto_instalacao == 503 || $assunto_instalacao == 421) {
                                $dados = array(
                                    'id_chamado' => $id_os_conferencia, // ID da O.S
                                    'data_inicio' => $currentDateTime, // Data e Hora de Início da finalização
                                    'data_final' => $currentDateTime, // Data e Hora de Final da finalização
                                    'mensagem' => $evaluationText, // Mensagem
                                    'gera_comissao' => '', // "N" para Não "S" para Sim
                                    'id_su_diagnostico' => '', // ID do diagnóstico (não obrigatório)
                                    'id_proxima_tarefa' => '222',
                                    'finaliza_processo' => 'N', // "S" para finalizar o processo
                                    'status' => 'F', // Status "F" para finalizar
                                    'id_tecnico' => $id_ixc_user // ID do técnico responsável
                                );

                                // Execução do método POST na API
                                $api->post('su_oss_chamado_fechar', $dados);

                                // Obtém a resposta da API
                                $retorno = $api->getRespostaConteudo(false);
                            } else {
                                // Dados para fechar o chamado
                                $dados = array(
                                    'id_chamado' => $id_os_conferencia, // ID da O.S
                                    'data_inicio' => $currentDateTime, // Data e Hora de Início da finalização
                                    'data_final' => $currentDateTime, // Data e Hora de Final da finalização
                                    'mensagem' => $evaluationText, // Mensagem
                                    'gera_comissao' => '', // "N" para Não "S" para Sim
                                    'id_su_diagnostico' => '', // ID do diagnóstico (não obrigatório)
                                    'finaliza_processo' => 'S', // "S" para finalizar o processo
                                    'status' => 'F', // Status "F" para finalizar
                                    'id_tecnico' => $id_ixc_user // ID do técnico responsável
                                );

                                // Execução do método POST na API
                                $api->post('su_oss_chamado_fechar', $dados);

                                // Obtém a resposta da API
                                $retorno = $api->getRespostaConteudo(false);
                            }
                        }
                    }
                } else {
                    $response['message'] = 'Erro ao avaliar!';
                }
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro de conexão: ' . $e->getMessage();
        }
    } else {
        // Campo necessário não foi preenchido
        $response['message'] = 'Preencha todos os campos por favor!';
        $response['missing_fields'] = $missing_fields;
    }
} else {
    // Requisição não é do tipo POST
    $response['message'] = 'Requisição inválida';
}

// Retorna a resposta em formato JSON
echo json_encode($response);
