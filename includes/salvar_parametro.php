<?php

/**
 * Função auxiliar para retornar JSON
 */
function retornarJson($dados, $sucesso = true) {
    $resposta = array(
        'sucesso' => $sucesso,
        'dados' => $dados,
        'timestamp' => date('Y-m-d H:i:s')
    );
    echo json_encode($resposta);
    exit;
}

function retornarErro($mensagem) {
    // PHP 5.3 não tem http_response_code()
    header('HTTP/1.1 400 Bad Request');
    $dados = array('erro' => $mensagem);
    retornarJson($dados, false);
}

/**
 * Conectar ao banco de dados
 */
function conectarBanco() {
    include_once dirname(__FILE__) . "/../../SCI/includes/conect_sqlserver.php";
    if (!isset($conSQL)) {
        throw new Exception("Erro ao conectar ao banco de dados");
    }
    return $conSQL;
}

try {
    $centro_custo = $_POST['centro_custo'];
    if (empty($centro_custo)) {
        throw new Exception("Centro de custo não informado");
    }
    
    // Conectar ao banco
    $conexao = conectarBanco();

    // Inserir dados
    $sql = "UPDATE parametro_celular_agregado 
        SET centro_custo_padrao = '{$centro_custo}' 
        WHERE id = 1";
    
    if (!odbc_exec($conexao, $sql)) {
        throw new Exception(odbc_errormsg($conexao));
    }
    
    retornarJson(array('sucesso' => true));
    
} catch (Exception $e) {
    retornarErro("Erro ao salvar parâmetro: " . $e->getMessage());
}