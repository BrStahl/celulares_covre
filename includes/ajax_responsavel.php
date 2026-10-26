<?php
require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";

    $con = new ConexaoSCI;
    
    $keyword = $_POST["keyword"];


	$query = <<<SQL

		SELECT 
			PPESSOA.CODIGO AS CORP_PESSOA_ID, 
			CPESSOA.PESSOA_ID, 
			UPPER(PPESSOA.NOME) AS NOME_FANTASIA
		FROM 
			corpore..pfunc WITH (NOLOCK)
		JOIN 
			corpore..ppessoa PPESSOA WITH (NOLOCK) 
			ON PPESSOA.CODIGO = pfunc.codpessoa
		LEFT JOIN 
			CARGOSOL..COLABORADOR CO WITH (NOLOCK) 
			ON CO.Num_Funcionario_Alfa COLLATE SQL_Latin1_General_CP1_CI_AI = pfunc.chapa
			AND ((CO.Tab_Status_Id NOT IN (2,8,23)) 
				OR (CO.PESSOA_ID = 3933) /*SELMA*/  
				OR (CO.PESSOA_ID = 46952) /*MURILO*/  
				OR (CO.PESSOA_ID = 75130) /*ADRIANO*/)
		LEFT JOIN 
			CARGOSOL..pessoa CPESSOA WITH (NOLOCK) 
			ON CPESSOA.PESSOA_ID = CO.Pessoa_Id
			AND CPESSOA.Tab_Status_Id NOT IN (2,8,23)
		
		WHERE 
			PFUNC.CODCOLIGADA = 1
            and PFUNC.CODSITUACAO <> 'D'
			and PPESSOA.NOME LIKE '$keyword%'

		UNION
		
		SELECT 
			PPESSOA.CODIGO AS CORP_PESSOA_ID, 
			CPESSOA.PESSOA_ID, 
			UPPER(PPESSOA.NOME) AS NOME_FANTASIA
		FROM 
			corpore..ppessoa PPESSOA WITH (NOLOCK)
		JOIN 
			corpore..VPCOMPL WITH (NOLOCK) 
			ON VPCOMPL.CODPESSOA = PPESSOA.CODIGO
            AND VPCOMPL.TIPO = 2
            AND VPCOMPL.DTDES IS NULL
            /*AND PPESSOA.EMAIL IS NOT NULL*/
		LEFT JOIN 
			CARGOSOL..pessoa CPESSOA WITH (NOLOCK) 
			ON PPESSOA.CPF = CPESSOA.PF_CPF COLLATE SQL_LATIN1_GENERAL_CP1_CI_AI
			AND CPESSOA.Tab_Status_Id NOT IN (2, 8, 23)
		WHERE 
			PPESSOA.NOME LIKE '$keyword%'
			
		ORDER BY 
			NOME_FANTASIA
SQL;

	//print $query;
	$result = $con->select($query);
    //print_r($result);
    // if(!empty($result)) {
    // 
	if ($result) {
	
	?>
     <ul class='select-list ' id="select-list" style="width:500px">
     <?php
    foreach($result as $autocomplete) {
    // ?>
     <li onClick="selectFiltro('<? echo $autocomplete["CORP_PESSOA_ID"]."','".$autocomplete["PESSOA_ID"]."','".$autocomplete["NOME_FANTASIA"] ?>');"><? echo $autocomplete["NOME_FANTASIA"]; ?></li>
     <?php } ?>
     </ul>
     <?php
	}
	else
		print 'vazio';
	?>
    
    
     