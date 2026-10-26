<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');



/**
 * Classe principal para processar consumo de celulares
 */
class ProcessadorConsumo {
    private $conexao;
    
    public function __construct($conexao) {
        $this->conexao = $conexao;
    }
    
    /**
     * Processa arquivo ZIP e retorna resultado final
     */
    public function processar($arquivo, $operadora, $centroCustoPadrao) {
        // 1. Extrair dados do arquivo
        $dadosArquivo = $this->extrairDadosZip($arquivo);
        
        // 2. Buscar celulares da operadora
        $celulares = $this->buscarCelulares($operadora);
        $estoque = $this->buscarEstoque($operadora);
        
        // 3. Calcular consumo por centro de custo
        return $this->calcularConsumo($dadosArquivo, $celulares, $estoque, $centroCustoPadrao);
    }
    
    /**
     * Extrai dados do arquivo ZIP
     */
    private function extrairDadosZip($arquivo) {
        $zip = new ZipArchive;
        if ($zip->open($arquivo['tmp_name']) !== TRUE) {
            throw new Exception("Erro ao abrir arquivo ZIP");
        }
        
        $conteudo = $zip->getStream($zip->getNameIndex(0));
        $linhas = array();
        $valorTotal = 0;
        $dentroSecao = false;
        $primeiraLinha = true;
        
        while (($linha = fgets($conteudo)) !== false) {
            // Controla início e fim da seção de dados
            if (strpos($linha, 'DOC1 - DUMMY LINE') !== false) {
                if ($dentroSecao) break; // Segunda ocorrência = fim
                $dentroSecao = true;
                continue;
            }
            
            if (!$dentroSecao) continue;
            
            // Primeira linha tem o valor total
            if ($primeiraLinha) {
                $valorTotal = $this->extrairValorTotal($linha);
                $primeiraLinha = false;
                continue;
            }
            
            // Extrair telefone e valor das outras linhas
            $dadosLinha = $this->extrairDadosLinha($linha);
            if (!empty($dadosLinha)) {
                $linhas[] = $dadosLinha;
            }
        }
        
        fclose($conteudo);
        $zip->close();
        
        return array(
            'linhas' => $linhas,
            'valorTotal' => $valorTotal
        );
    }
    
    /**
     * Extrai valor total da linha de cabeçalho
     */
    private function extrairValorTotal($linha) {
        $partes = preg_split('/\s{5,}/', trim($linha));
        return (float) $partes[count($partes) - 2];
    }
    
    /**
     * Extrai telefone e valor de uma linha
     */
    private function extrairDadosLinha($linha) {
        $pattern = '/(?P<telefone>\d{2}-\d{5}-\d{4})\s+.*?(?P<valor>\d+\.\d{2})(?:A)/';
        if (preg_match($pattern, $linha, $matches)) {
            return array(
                'telefone' => preg_replace('/[^0-9]/', '', $matches['telefone']),
                'valor' => (float) $matches['valor']
            );
        }
        return null;
    }
    
    /**
     * Busca celulares com centro de custo por operadora
     */
    private function buscarCelulares($operadora) {
        $sql = "SELECT 
-- Seleciona o primeiro código de centro de custo válido (aquele que contém um '.')
COALESCE(
    CASE WHEN CHARINDEX('.', FUNCIONARIO.COD_CENTRO_CUSTO) > 0 THEN FUNCIONARIO.COD_CENTRO_CUSTO END,
    CASE WHEN CHARINDEX('.', TERCEIRO.COD_CENTRO_CUSTO) > 0 THEN TERCEIRO.COD_CENTRO_CUSTO END,
    CASE WHEN CHARINDEX('.', GCCUSTO.CODREDUZIDO) > 0 THEN GCCUSTO.CODREDUZIDO END,
    CASE WHEN CHARINDEX('.', LAF.COD_CCUSTO) > 0 THEN LAF.COD_CCUSTO END
) AS COD_CENTRO_CUSTO,

-- Seleciona a descrição/nome correspondente ao primeiro centro de custo válido
COALESCE(
    CASE WHEN CHARINDEX('.', FUNCIONARIO.COD_CENTRO_CUSTO) > 0 THEN FUNCIONARIO.CENTRO_CUSTO END,
    CASE WHEN CHARINDEX('.', TERCEIRO.COD_CENTRO_CUSTO) > 0 THEN TERCEIRO.CENTRO_CUSTO END,
    CASE WHEN CHARINDEX('.', GCCUSTO.CODREDUZIDO) > 0 THEN GCCUSTO.NOME END,
    CASE WHEN CHARINDEX('.', LAF.COD_CCUSTO) > 0 THEN LAF_CCUSTO.NOME END
) AS centro_custo,

  celular.num_celular

        FROM linha_aparelho_funcionario laf WITH (NOLOCK)
        JOIN celular_covre celular WITH (NOLOCK) ON celular.id = laf.celular_id AND celular.status_id = 'a'
        JOIN operadora_celular operadora WITH (NOLOCK) ON operadora.id = celular.operadora_id
        LEFT JOIN aparelho_celular aparelho WITH (NOLOCK) ON aparelho.id = laf.aparelho_id
        LEFT JOIN modelo_aparelho_celular modelo WITH (NOLOCK) ON modelo.id = aparelho.modelo_id
        LEFT JOIN marca_celular marca WITH (NOLOCK) ON marca.id = modelo.marca_id

        /* Funcionario RM */
        OUTER APPLY (
            SELECT 
                PFUNC.CODPESSOA AS CORP_PESSOA_ID,
                PFUNC.NOME COLLATE SQL_Latin1_General_CP1_CI_AI AS NOME,
                PCCUSTO.CODCCUSTO COLLATE Latin1_General_CI_AS + '.567.569' AS COD_CENTRO_CUSTO,
                PCCUSTO.NOME AS CENTRO_CUSTO
            FROM corpore..PFUNC WITH (NOLOCK)
            LEFT JOIN corpore..PFRATEIOFIXO WITH (NOLOCK) ON PFUNC.CHAPA = PFRATEIOFIXO.CHAPA AND PFRATEIOFIXO.CODCOLIGADA = 1
            LEFT JOIN corpore..PCCUSTO WITH (NOLOCK) ON PFRATEIOFIXO.CODCCUSTO = PCCUSTO.CODCCUSTO AND PCCUSTO.CODCOLIGADA = 1
            WHERE PFUNC.CODPESSOA = LAF.CORP_PESSOA_ID AND PFUNC.CODCOLIGADA = 1 AND PFUNC.CODSITUACAO <> 'D'
        ) FUNCIONARIO

        /* Terceiro RM */
        OUTER APPLY (
            SELECT 
                PPESSOA.CODIGO AS CORP_PESSOA_ID,
                PPESSOA.NOME AS NOME,
                GCCUSTO.CODREDUZIDO AS COD_CENTRO_CUSTO,
                GCCUSTO.NOME AS CENTRO_CUSTO
            FROM corpore..PPESSOA WITH (NOLOCK)
            JOIN corpore..VPCOMPL WITH (NOLOCK) ON VPCOMPL.CODPESSOA = PPESSOA.CODIGO AND VPCOMPL.TIPO = 2 AND VPCOMPL.DTDES IS NULL
            LEFT JOIN corpore..ILOCAL WITH (NOLOCK) ON ILOCAL.NOME = PPESSOA.NOME AND ILOCAL.CODCOLIGADA = 1
            LEFT JOIN corpore..GCCUSTO WITH (NOLOCK) ON GCCUSTO.CODCCUSTO = ILOCAL.CODCCUSTO AND GCCUSTO.CODCOLIGADA = 1
            WHERE PPESSOA.CODIGO = LAF.CORP_PESSOA_ID
        ) TERCEIRO

        /* Ecargo */
        LEFT JOIN CARGOSOL..pessoa WITH (NOLOCK) ON pessoa.pessoa_id = laf.pessoa_id
        LEFT JOIN corpore..GCCUSTO WITH (NOLOCK) ON GCCUSTO.CODREDUZIDO = laf.cod_ccusto COLLATE Latin1_General_CI_AS
        LEFT JOIN corpore..PCCUSTO LAF_CCUSTO (NOLOCK) ON LAF_CCUSTO.CODCCUSTO = REPLACE(REPLACE(laf.cod_ccusto, '.557.569', ''), '.567.569', '') COLLATE Latin1_General_CI_AS

        WHERE laf.status_id = 'a' AND operadora.descricao LIKE '{$operadora}%'
        ORDER BY centro_custo";
        
        return $this->executarConsulta($sql);
    }
    
    /**
     * Busca celulares em estoque por operadora
     */
    private function buscarEstoque($operadora) {
        $sql = "SELECT celular.num_celular
                FROM celular_covre celular WITH (NOLOCK)
                JOIN operadora_celular operadora WITH (NOLOCK) ON operadora.id = celular.operadora_id
                WHERE celular.status_id = 'a' AND operadora.descricao LIKE '{$operadora}%'";
        
        $celulares = $this->executarConsulta($sql);
        $resultados = array();
        foreach ($celulares as $celular) {
            $resultados[] = $celular['num_celular'];
        }
        return $resultados;
    }
    
    /**
     * Busca nome do centro de custo padrão
     */
    private function buscarNomeCentroCusto($codigo) {
        $sql = "SELECT TOP 1 NOME FROM corpore..PCCUSTO (NOLOCK) WHERE CONCAT(codccusto, '.557.569') = '{$codigo}'";
        $resultado = $this->executarConsulta($sql);
        
        if (empty($resultado)) {
            return 'Centro de Custo Padrão';
        }

        return $this->converterEncoding($resultado[0]['NOME']);
    }
    
    /**
     * Calcula consumo por centro de custo
     */
    private function calcularConsumo($dadosArquivo, $celulares, $estoque, $centroCustoPadrao) {
        // Criar mapa: telefone => centro de custo
        $mapaCelulares = array();
        foreach ($celulares as $celular) {
            if (!empty($celular['cod_centro_custo'])) {
                $mapaCelulares[$celular['num_celular']] = array(
                    'codigo' => trim($celular['cod_centro_custo']),
                    'nome' => trim($celular['centro_custo'])
                );
            }
        }

        // Inicializar resultados
        $centrosCusto = array();
        $totalCalculado = 0;
        $celularesNaoEncontrados = array();
        
        // Adicionar centro de custo padrão
        $centrosCusto[$centroCustoPadrao] = array(
            'centro_custo' => $this->buscarNomeCentroCusto($centroCustoPadrao),
            'valor' => 0
        );

        // Processar cada linha do arquivo
        foreach ($dadosArquivo['linhas'] as $linha) {
            $telefone = $linha['telefone'];
            $valor = $linha['valor'];
            
            // Determinar centro de custo
            if (isset($mapaCelulares[$telefone])) {
                $codigoCentro = $mapaCelulares[$telefone]['codigo'];
                
                // Criar centro de custo se não existir
                if (!isset($centrosCusto[$codigoCentro])) {
                    $centrosCusto[$codigoCentro] = array(
                        'centro_custo' => $mapaCelulares[$telefone]['nome'],
                        'valor' => 0
                    );
                }
            } else {
                // Usar centro de custo padrão
                $codigoCentro = $centroCustoPadrao;
                // Marcar como não encontrado se não estiver no estoque
                if (!in_array($telefone, $estoque)) {
                    $celularesNaoEncontrados[] = $telefone;
                }
            }
            
            // Somar valor
            $centrosCusto[$codigoCentro]['valor'] += $valor;
            $totalCalculado += $valor;
        }
        
        // Calcular porcentagens
        $totalPercentual = 0;
        foreach ($centrosCusto as $codigo => $dados) {
            $percentual = $totalCalculado > 0
                ? round(($dados['valor'] / $totalCalculado) * 100, 2)
                : 0;
            $centrosCusto[$codigo]['porcentagem'] = $percentual;
            $totalPercentual += $percentual;
        }
        
        // Ordenar por nome do centro de custo
        uasort($centrosCusto, function($a, $b) {
            return strcmp($a['centro_custo'], $b['centro_custo']);
        });
        
        // Verificar inconsistências
        $diferenca = $dadosArquivo['valorTotal'] - $totalCalculado;
        $inconsistente = abs($diferenca) > 0.01; // tolerância de 1 centavo
        
        return array(
            'total_inconsistente' => $inconsistente,
            'total_calculado' => round($totalCalculado, 2),
            'total_arquivo' => round($dadosArquivo['valorTotal'], 2),
            'diferenca' => round($diferenca, 2),
            'centros_custo' => $centrosCusto,
            'total_percentual' => floor($totalPercentual),
            'total_linhas_processadas' => count($dadosArquivo['linhas']),
            'celulares_nao_encontrados' => $celularesNaoEncontrados
        );
    }
    
    /**
     * Executa consulta SQL e retorna resultados
     */
    private function executarConsulta($sql, $parametros = array()) {
        $stmt = odbc_prepare($this->conexao, $sql);
        if (!$stmt || !odbc_execute($stmt, $parametros)) {
            throw new Exception("Erro ao executar consulta: " . odbc_errormsg($this->conexao) . "\n\n" . $sql);
        }
        
        $resultados = array();
        while ($linha = odbc_fetch_array($stmt)) {
            // Converter chaves para lowercase
            $linha = array_change_key_case($linha, CASE_LOWER);

            // Converter encoding se necessário
            foreach ($linha as $chave => $valor) {
                if (is_string($valor)) {
                    $linha[$chave] = $this->converterEncoding($valor);
                }
            }
            $resultados[] = $linha;
        }
        
        odbc_free_result($stmt);
        return $resultados;
    }
    
    /**
     * Converte encoding para UTF-8 se necessário
     */
    private function converterEncoding($texto) {
        if (!mb_check_encoding($texto, 'UTF-8')) {
            return mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
        }
        return $texto;
    }
}

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

// ==================== PROCESSAMENTO PRINCIPAL ====================

try {
    // Verificar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        retornarErro('Apenas método POST é permitido');
    }

    // Validar dados recebidos
    if (!isset($_FILES['arquivo'])) {
        retornarErro('Arquivo não enviado');
    }

    if (empty($_POST['operadora'])) {
        retornarErro('Operadora é obrigatória');
    }

    if (empty($_POST['centro_custo'])) {
        retornarErro('Centro de custo é obrigatório');
    }

    $arquivo = $_FILES['arquivo'];
    $operadora = trim($_POST['operadora']);
    $centro_custo_padrao = $_POST['centro_custo'];

    // Verificar erros no upload
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        retornarErro('Erro no upload do arquivo (código: ' . $arquivo['error'] . ')');
    }

    // Conectar ao banco
    $conexao = conectarBanco();

    // Processar arquivo ZIP
    $processador = new ProcessadorConsumo($conexao);
    
    $resultado = $processador->processar(
        $arquivo,
        $operadora,
        trim($centro_custo_padrao)
    );

    // Gerar HTML da tabela de consumo
    ob_start();
    include __DIR__.'/tabela_consumo_mensal.php';
    $tabelaHtml = ob_get_clean();

    // Retornar resultado
    retornarJson(array('html' => $tabelaHtml));

} catch (Exception $e) {
    retornarErro('Erro ao processar: ' . $e->getMessage());
}
?>