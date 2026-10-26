<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";
require_once "Dados.php";
require("../../SCA/includes/phpmailer/class.phpmailer.php");
require_once "../../SCI/includes/page_func_sci.php";

class GeraVale extends ConexaoSCI{



	public function insereValeEcargo($id, $boletim_servico_id, $fornecedor_id, $valor_cobrado, $observacao, $usuario_ecargo, $veiculo_fornecedor_id, $data_vencto){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
	

				//SELECIONA O ULTIMO CODIGO E ADICIONA 1
				$query = "SELECT LANCAMENTO_OPERACAO_ID + 1 
						   FROM CARGOSOL..INFRA_IDS";
				$result = $con->executar($query);
				$lancamento_operacao_id	= odbc_result($result,1);		   
						   
				
				//ATUALIZA A TABELA INFRA_IDS DO E-CARGO
				$query = "UPDATE CARGOSOL..INFRA_IDS 
						  SET LANCAMENTO_OPERACAO_ID = $lancamento_operacao_id";
				//print $query1;
				$con->executar($query);


				//INSERE NA TABELA LANCAMENTO_OPERACAO_ID
				$query1 = "INSERT INTO CARGOSOL..LANCAMENTO_OPERACAO (TAB_STATUS_ID, LANCAMENTO_OPERACAO_ID, 
							TAB_STATUS_PAGTO_ID, TAB_TIPO_FAVORECIDO_ID, SUB_GRUPO_CONTABIL_ID, FORNECEDOR_ID, EMPRESA_ID, 
							PONTO_OPERACAO_ID, VALOR_LANC_RS, DATA_INCL, USUARIO_INCL, DATA_VENCTO_INI, DATA_VENCTO, 
							DATA_EMISSAO, OBSERV, NUM_PARCELA, SENSO, VALOR_LANC_INI, DATA_COMPETENCIA, VEICULO_FORNECEDOR_ID, 
							BOLETIM_SERVICO_ID, PONTO_OPERACAO_CAIXA_ID, COLABORADOR_ID) 
							VALUES (1, $lancamento_operacao_id, 135, 3,  5026, $fornecedor_id, 1, 1, 
							replace(replace('$valor_cobrado','.',''),',','.'), GETDATE(), '$usuario_ecargo', NULL, 
							'$data_vencto', '$data_vencto', '$observacao', 1, 'D', 
							replace(replace('$valor_cobrado','.',''),',','.'), '$data_vencto', $veiculo_fornecedor_id, 
							$boletim_servico_id, 1, NULL) ";
				//print "<pre>".$query1."</pre>";
				$con->executar($query1);
				
				
				$query2 = "INSERT INTO LOG_CELULAR_LANCAMENTO_OPERACAO VALUES (1, $lancamento_operacao_id, 135, 3,  5026, 
							$fornecedor_id, 1, 1, replace(replace('$valor_cobrado','.',''),',','.'), GETDATE(), 
							'$usuario_ecargo', NULL, '$data_vencto', '$data_vencto', '$observacao', 1, 'D', 
							replace(replace('$valor_cobrado','.',''),',','.'), '$data_vencto', $veiculo_fornecedor_id, 
							$boletim_servico_id, 1, NULL, getdate())";
				//print "<pre>".$query2."</pre>";
				$con->executar($query2);				

				
				//FINALIZA O STATUS
				$query = "update conferencia_plano_agregado
						   set status_id = 'f', data_hora_geracao_vale = getdate(), 
						   user_geracao_vale = (select id from usuario where usuario = '$logado' and status = 'a'),
						   lancamento_operacao_id = $lancamento_operacao_id
						   where id = $id";
				//print $query1;
				$con->executar($query);				

		return $lancamento_operacao_id;
	}	




		
	public function geraValeBoletim($mes, $ano){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
	
		
		if ($logado != '')
		{

			$table = "
					<table border=1 cellspacing=0 cellpadding=2' style='border: 1px solid black'>
						<tr>
							<td bgcolor='#d3d3d3' width='300' style='border: 1px solid black'>MOTORISTA</td>	
							<td bgcolor='#d3d3d3' width='150' style='border: 1px solid black'>CELULAR</td>	
							<td bgcolor='#d3d3d3' width='110' style='border: 1px solid black'>BOLETIM</td>																								
							<td bgcolor='#d3d3d3' width='110' style='border: 1px solid black'>VALE</td>
							<td bgcolor='#d3d3d3' width='110' style='border: 1px solid black'>VALOR COBRADO</td>
							<td bgcolor='#d3d3d3' width='110' style='border: 1px solid black'>OBSERVACAO</td>
						</tr>";		
						

			$query = "select dados.id conferencia_id, dados.pessoa_id, valor_cobrado, boletim_servico_id,
				'('+substring(dados.num_celular,1,2)+')'+substring(dados.num_celular,3,5)+'-'+substring(dados.num_celular,8,4),
				'CEL. '+operadora+' 		'+'('+substring(dados.num_celular,1,2)+')'+substring(dados.num_celular,3,5)+'-'+substring(dados.num_celular,8,4)+' - '+DESC_MES+'/'+cast(ano as varchar),
					dados.motorista,
					dados.usuario_ecargo,
					Veiculo_Fornecedor_Id,
					data_vencto,
					fornecedor_id
					from (
					select conferencia.id, conferencia.pessoa_id, valor_cobrado, conferencia.boletim_servico_id,
					CASE MES
						WHEN 1 THEN 'JANEIRO'
						WHEN 2 THEN 'FEVEREIRO'
						WHEN 3 THEN 'MARCO'
						WHEN 4 THEN 'ABRIL'
						WHEN 5 THEN 'MAIO'
						WHEN 6 THEN 'JUNHO'
						WHEN 7 THEN 'JULHO'
						WHEN 8 THEN 'AGOSTO'
						WHEN 9 THEN 'SETEMBRO'
						WHEN 10 THEN 'OUTUBRO'
						WHEN 11 THEN 'NOVEMBRO'
						WHEN 12 THEN 'DEZEMBRO'
					END DESC_MES,
					linha.num_celular,
					operadora.descricao operadora,
					conferencia.ano,
					pessoa.nome motorista,
					(SELECT NOME_USUARIO
					 FROM CARGOSOL..USUARIO WITH (NOLOCK)
					 WHERE COLABORADOR_ID IN (SELECT TOP 1 PESSOA_ID 
											  FROM USUARIO WITH (NOLOCK)
											  WHERE USUARIO = 'vinicius.mtz' 
											  AND STATUS = 'a')
					 AND TAB_STATUS_ID = 1) usuario_ecargo,
					 bs.Veiculo_Fornecedor_Id,
					 bs.data_vencto data_vencto,
					 conferencia.fornecedor_id
						from conferencia_plano_agregado conferencia with (nolock)
						join linha_aparelho_funcionario laf with (nolock) on
							laf.id = conferencia.linha_aparelho_id
						join celular_covre linha with (nolock) on
							linha.id = laf.celular_id
						join operadora_celular operadora with (nolock) on
							operadora.id = linha.operadora_id
						join cargosol..pessoa with (nolock) on
							pessoa.Pessoa_Id = conferencia.pessoa_id
						left join cargosol..Boletim_Servico bs with (nolock) on
							bs.Boletim_Servico_Id = conferencia.Boletim_Servico_Id
							and bs.Data_Fechamento is null
							and bs.Tab_Status_Id = 1
						where conferencia.status_id = 'r'
						and mes = $mes
						and ano = $ano
		)dados";
			//print $query;
			$result = $con->executar($query);

			while(odbc_fetch_row($result)) 
			{ 
			
				$id						= odbc_result($result,1);
				$pessoa_id				= odbc_result($result,2);
				$valor					= odbc_result($result,3);
				$boletim_servico_id		= odbc_result($result,4);	
				$num_celular			= odbc_result($result,5);
				$observacao				= odbc_result($result,6);
				$motorista				= odbc_result($result,7);
				$usuario_ecargo			= odbc_result($result,8);
				$veiculo_fornecedor_id	= odbc_result($result,9);
				$data_vencto			= odbc_result($result,10);		
				$fornecedor_id			= odbc_result($result,11);								

				$valor_cobrado = number_format($valor, 2, ',', '.');
	
			
				//EFETUA A GERACAO DO VALE NO E-CARGO
				$lancamento_operacao_id = $this->insereValeEcargo($id, $boletim_servico_id, $fornecedor_id, $valor_cobrado, $observacao, $usuario_ecargo, $veiculo_fornecedor_id, $data_vencto);

				//$table .= $lancamento_operacao_id;
			
			
				$table .= "
					  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
						<td bgcolor='#FFFFFF' style='border: 1px solid black'><center>
							<font size='-1'>".$motorista."</font></center></td>	
						<td bgcolor='#FFFFFF' style='border: 1px solid black'><center>
							<font size='-1'>".$num_celular."</font></center></td>
						<td bgcolor='#FFFFFF' style='border: 1px solid black'><center>
							<font size='-1'>".$boletim_servico_id."</font></center></td>
						<td bgcolor='#FFFFFF' style='border: 1px solid black'><center>
							<font size='-1'>".$lancamento_operacao_id."</font></center></td>
						<td bgcolor='#FFFFFF' style='border: 1px solid black'><center>
							<font size='-1'>".$valor_cobrado."</font></center></td>
						<td bgcolor='#FFFFFF' style='border: 1px solid black'><center>
							<font size='-1'>".$observacao."</font></center></td>
					  </tr>"; 

				
			}//FIM WHILE
			
			$table .= "</table>";	
			
			//SELECIONA A DESCRICAO DO MES E ANO
			$query = "SELECT 
						CASE '$mes'
							WHEN 1 THEN 'JANEIRO'
							WHEN 2 THEN 'FEVEREIRO'
							WHEN 3 THEN 'MARCO'
							WHEN 4 THEN 'ABRIL'
							WHEN 5 THEN 'MAIO'
							WHEN 6 THEN 'JUNHO'
							WHEN 7 THEN 'JULHO'
							WHEN 8 THEN 'AGOSTO'
							WHEN 9 THEN 'SETEMBRO'
							WHEN 10 THEN 'OUTUBRO'
							WHEN 11 THEN 'NOVEMBRO'
							WHEN 12 THEN 'DEZEMBRO'
						END+'/'+CAST('$ano' AS VARCHAR)";
			//print "<pre>".$query."</pre>";
			$result = $con->executar($query);			
			$descricao_mes_ano = odbc_result($result,1);			
			
			$email_destino = "acerto@covre.com.br";
				
			//ENVIA EMAIL
			$enviou = enviar_email("helpdesk@covre.com.br", "SCI- Celulares Covre", "$email_destino", "VALES GERADOS - CELULAR AGREGADOS",
							"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
							<html xmlns='http://www.w3.org/1999/xhtml'>
							<head>
							<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
							</head>
							<body>
							<b>E-MAIL AUTOM&Aacute;TICO</b>
							<br><br><br>Vales gerados para os agregados que utilizaram celulares Covre no per&iacute;odo $descricao_mes_ano.	
							<br><br>						
							<font size='+1' color='blue'><b>$table</font></b>
							<br><br><br>

							<br><b>SCI - CELULARES COVRE</b>","0","");		
			
			$query = "insert into log_email_celular values ($mes, $ano, getdate(), 'vales gerados', '$email_destino')";
			//print $query;
			$con->executar($query);						
			
			$ret_gravacao =  "ok";
		}
		else
			$ret_gravacao = "sessao_expirada";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}	
	

	
}
	


?>