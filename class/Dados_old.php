<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";


class Dados extends ConexaoSCI{

	
	//BUSCA DADOS
	public function exibeDadosCelular(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select celular.id,  
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4) celular,
					operadora.descricao, imei,
					case when celular.status_id = 'a'
							then 'Ativo'
							else 'Inativo'
					end,
					celular.observacao
					from celular_covre celular with (nolock)
					join operadora_celular operadora with (nolock) on
						operadora.id = celular.operadora_id
					where 1 = 1
					order by celular";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';

			   
		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>NÚMERO CELULAR</a></th>
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>OPERADORA</a></th>	
						<th bgcolor='#d3d3d3' width='200'><a href='javascript:void(0)'>IMEI</a></th>
						<th bgcolor='#d3d3d3' width='200'><a href='javascript:void(0)'>OBSERVAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='100'><a href='javascript:void(0)'>HISTÓRICO</a></th>						
						<th bgcolor='#d3d3d3' width='70'><a href='javascript:void(0)'>STATUS</a></th>
					</thead><tbody>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$id					= odbc_result($result,1);
			$num_celular		= odbc_result($result,2);
			$operadora			= odbc_result($result,3);			
			$imei				= odbc_result($result,4);
			$status_id			= odbc_result($result,5);
			$observacao			= odbc_result($result,6);
			
			$observacao = utf8_encode($observacao);
	
			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_dados_celular($id);'><font size='-1'>".$num_celular."</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$operadora."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$imei."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$observacao."</font></center></td>	
					<td><a href='javascript:void(0)' onclick='dados_registro($id);'>Histórico</a>
					</td>								
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$status_id."</font></center></td>
				  </tr>"; 
				 
		 } 
		 
		  $table .= "</tbody></table>";
		  
		  $table .= "<script>order()</script>";
		  
		  $retorno->celular = $table;

		  return $retorno;

	}


	public function exibeCelular(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		
		//VERIFICA PERMISSAO
		$query = "select TOP 1 centro_custo_id, permissao.administrador
					FROM permissao_celular_covre permissao with (nolock)
					left join ccusto_responsavel_celular crc with (nolock) on
						crc.responsavel_id = permissao.id
						and crc.status_id = 'a'
					where permissao.usuario_id = (SELECT TOP 1 ID FROM USUARIO WHERE USUARIO = '$logado' AND STATUS = 'a')
					and permissao.status_id = 'a'";
		//print $query;
		$result = $con->executar($query);		
		$permissao_cc	= odbc_result($result,1);
		$administrador	= odbc_result($result,2);
		
		if ($administrador == 'S')
			$condicao_permissao = "";
		else
			$condicao_permissao = "and cf.cod_ccusto in (select centro_custo_id
															FROM ccusto_responsavel_celular crc with (nolock)
															join permissao_celular_covre permissao with (nolock) on
																permissao.id = crc.responsavel_id
																and permissao.status_id = 'a'
															where crc.status_id = 'a'
															and permissao.usuario_id = (SELECT TOP 1 ID 
																						FROM USUARIO with (NOLOCK)
																						WHERE USUARIO = '$logado' 
																						AND STATUS = 'a'))";

		
	 
		$query = "select cf.id, isnull(pessoa.nome_fantasia, pessoa.nome), 
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4) celular, 
					convert(varchar(10), data_inicio, 103) data_inicio,
					cf.observacao, 
					convert(varchar(10), cf.data_gravacao, 103) data_gravacao,
					cf.cod_ccusto COD_CENTRO_CUSTO,
					GCCUSTO.nome collate sql_latin1_general_cp1251_ci_as centro_custo,
					operadora.descricao operadora,
					celular.observacao obs_linha
					from celular_funcionario cf with (nolock)
					join celular_covre celular with (nolock) on
						celular.id = cf.celular_covre_id
						and celular.status_id = 'a'
					join CARGOSOL..pessoa with (nolock) on
						pessoa.pessoa_id = cf.pessoa_id
					left join corpore..GCCUSTO with (nolock) on
						GCCUSTO.CODREDUZIDO = CAST(cf.cod_ccusto AS VARCHAR)
					join operadora_celular operadora with (nolock) on
						operadora.id = celular.operadora_id
					where cf.status_id = 'a'
					$condicao_permissao
					order by celular ";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='300'><a href='javascript:void(0)'>RESPONSÁVEL</a></th>
						<th bgcolor='#d3d3d3' width='120' ><a href='javascript:void(0)'>NÚMERO CELULAR</a></th>	
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>OPERADORA</a></th>
						<th bgcolor='#d3d3d3' width='150' ><a href='javascript:void(0)'>OBSERVAÇÃO LINHA</a></th>							
						<th bgcolor='#d3d3d3' width='70' ><a href='javascript:void(0)'>CENTRO CUSTO</a></th>	
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>DATA INÍCIO</a></th>												
						<th bgcolor='#d3d3d3'><a href='javascript:void(0)'>OBSERVAÇÃO</a></th>
						<th bgcolor='#d3d3d3'><a href='javascript:void(0)'>PROTOCOLO</a></th>
					</thead><tbody>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$id					= odbc_result($result,1);
			$nome_responsavel	= odbc_result($result,2);
			$num_celular		= odbc_result($result,3);
			$data_inicio		= odbc_result($result,4);			
			$observacao			= odbc_result($result,5);			
			$cod_custo			= odbc_result($result,7);
			$centro_custo		= odbc_result($result,8);			
			$operadora			= odbc_result($result,9);
			$obs_linha			= odbc_result($result,10);
			
			$observacao = utf8_encode($observacao);
			$obs_linha = utf8_encode($obs_linha);
	
			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_celular($id);'><font size='-1'>".$nome_responsavel."</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$num_celular."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$operadora."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$obs_linha."</font></center></td>											
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$cod_custo."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_inicio."</font></center></td>										
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$observacao."</font></center></td>
					<td bgcolor='#FFFFFF'><center><input type='button' class='btn-sm btn-primary' value='Protocolo' id='protocolo' name='protocolo' 
								onClick='javascript:pagina(".chr(34)."protocolo_entrega.php?id=$id".chr(34).",".chr(34)."1100".chr(34).",".chr(34)."800".chr(34).",".chr(34)."protocolo".chr(34).")'></td>
				  </tr>"; 
				 
		 } 
		 
		  $table .= "</tbody></table>";
		  
		  $table .= "<script>order()</script>";
		  
		  $retorno->celular = $table;

		  return $retorno;

	}


	public function buscaDadosCelular($id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select 
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4) celular,
					operadora.id, imei, celular.status_id,
					celular.observacao
					from celular_covre celular with (nolock)
					join operadora_celular operadora with (nolock) on
						operadora.id = celular.operadora_id
					where celular.id = $id";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
	
		$num_celular		= odbc_result($result,1);
		$operadora_id		= odbc_result($result,2);
		$imei				= odbc_result($result,3);	
		$status_id			= odbc_result($result,4);
		$observacao			= odbc_result($result,5);		
		
		
		$observacao = utf8_encode($observacao);
	
		  
		$retorno->celular = "1|".$id."|".$num_celular."|".$operadora_id."|".$imei."|".$status_id."|".$observacao."|";

		return $retorno;

	}


	public function buscaDadosModelo($id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select marca_id, descricao, status_id
				  from modelo_aparelho_celular
				  where id = $id";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
	
		$marca_id			= odbc_result($result,1);
		$descricao			= odbc_result($result,2);
		$status_id			= odbc_result($result,3);
		
		
		$descricao = utf8_encode($descricao);
	
		  
		$retorno->modelo = "1|".$id."|".$marca_id."|".$descricao."|".$status_id."|";

		return $retorno;

	}


	public function buscaDadosAparelho($id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select modelo_id, serie, imei1, imei2, observacao, status_id
					from aparelho_celular aparelho with (nolock)
					where id = $id";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
	
		$modelo_id			= odbc_result($result,1);
		$serie				= odbc_result($result,2);
		$imei1				= odbc_result($result,3);	
		$imei2				= odbc_result($result,4);			
		$observacao			= odbc_result($result,5);		
		$status_id			= odbc_result($result,6);		
		
		$observacao = utf8_encode($observacao);
	
		  
		$retorno->aparelho = "1|".$id."|".$modelo_id."|".$serie."|".$imei1."|".$imei2."|".$observacao."|".$status_id."|";

		return $retorno;

	}


	public function buscaCelular($id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select celular_funcionario.pessoa_id, 
					celular_covre_id celular,
					convert(varchar(10), data_inicio, 103), celular_funcionario.observacao, isnull(pessoa.nome_fantasia, pessoa.nome),
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4),
					cod_ccusto
					from celular_funcionario with (nolock)
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = celular_funcionario.pessoa_id
					join celular_covre celular with (nolock) on
						celular.id = celular_funcionario.celular_covre_id
						and celular.status_id = 'a'
					where celular_funcionario.status_id = 'a'
					and celular_funcionario.id = $id";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
	
		$pessoa_id			= odbc_result($result,1);
		$celular_covre_id	= odbc_result($result,2);
		$data_inicio		= odbc_result($result,3);			
		$observacao			= odbc_result($result,4);	
		$nome_responsavel	= odbc_result($result,5);
		$num_celular		= odbc_result($result,6);	
		$cod_ccusto			= odbc_result($result,7);					
		
		
		$observacao = utf8_encode($observacao);
	
		  
		$retorno->celular = "1|".$id."|".$pessoa_id."|".$celular_covre_id."|".$data_inicio."|".$observacao."|".$nome_responsavel."|".$num_celular."|".$cod_ccusto."|";

		return $retorno;

	}
	
	
	public function buscaAgregados(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "SELECT PESSOA.PESSOA_ID, PESSOA.NOME
					FROM CARGOSOL..COLABORADOR CO WITH (NOLOCK)
					JOIN CARGOSOL..PESSOA WITH (NOLOCK) ON
						PESSOA.PESSOA_ID = CO.PESSOA_ID
						AND PESSOA.TAB_STATUS_ID IN (1, 1071)
						AND PF_CPF IS NOT NULL
					WHERE CO.TAB_STATUS_ID IN (1, 1071)
					AND CO.TAB_TIPO_COLABORADOR_ID = 3
					AND CO.TAB_TIPO_VINCULO_ID NOT IN (1)
					AND PESSOA.PESSOA_ID NOT IN (SELECT TOP 1 PESSOA_ID
												 FROM CELULAR_FUNCIONARIO WITH (NOLOCK)
												 WHERE PESSOA_ID = CO.PESSOA_ID
												 AND STATUS_ID = 'A')
					ORDER BY PESSOA.NOME";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		$table = '<option></option>';
	
		while(odbc_fetch_row($result))
		{

			if (odbc_result($result, 1) == $pessoa_id)
				$selected = "selected='selected'";
			else
				$selected = "";

			 $table .= "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
		}

		$retorno->agregados = $table;
		
		return $retorno;

	}	
	
	
	public function buscaCelulares(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select celular.id,
					'('+substring(num_celular,1,2)+')'+substring(num_celular,3,5)+'-'+substring(num_celular,8,4) celular
					from celular_covre celular with (nolock)
					where celular.status_id = 'a'
					and celular.id not in (select celular_covre_id
										   from celular_funcionario with (nolock)
										   where celular_funcionario.status_id = 'a'
										   and celular_covre_id = celular.id)
					order by celular";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		$table = '<option></option>';
	
		while(odbc_fetch_row($result))
		{

			if (odbc_result($result, 1) == $celular_covre_id)
				$selected = "selected='selected'";
			else
				$selected = "";

			 $table .= "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
		}

		$retorno->celulares = $table;
		
		return $retorno;

	}	


	public function buscaCelularesDespesa(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select celular.id,
					'('+substring(num_celular,1,2)+')'+substring(num_celular,3,5)+'-'+substring(num_celular,8,4) celular
					from celular_covre celular with (nolock)
					where celular.status_id = 'a'
					order by celular";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		$table = '<option></option>';
	
		while(odbc_fetch_row($result))
		{

			if (odbc_result($result, 1) == $celular_covre_id)
				$selected = "selected='selected'";
			else
				$selected = "";

			 $table .= "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
		}

		$retorno->celulares = $table;
		
		return $retorno;

	}

	public function buscaParametro(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select valor
				  from parametro_celular_agregado";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
		$valor				= odbc_result($result,1);
		
		$valor = number_format($valor, 2, ',', '.');
	
		 
		$retorno->valor = $valor;
		

		return $retorno;

	}



	public function buscaDadosProtocolo($id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select 
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4) celular,
					operadora.descricao, 
					celular.imei,
					pessoa.Nome_Fantasia,
					isnull(psecao.DESCRICAO, po_pessoa.Nome_Fantasia) depto,
					celular_funcionario.observacao,
					convert(varchar(10), getdate(), 103) data_atual,
					convert(varchar(10), celular_funcionario.data_inicio, 103) data_inicio,
					celular.observacao
					from celular_covre celular with (nolock)
					join operadora_celular operadora with (nolock) on
						operadora.id = celular.operadora_id
					join celular_funcionario with (nolock) on
						celular_funcionario.celular_covre_id = celular.id
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = celular_funcionario.pessoa_id
					left join CARGOSOL..COLABORADOR CO with (nolock) on
						CO.Pessoa_Id = pessoa.Pessoa_Id
						AND CO.TAB_STATUS_ID <> 2
					LEFT JOIN CARGOSOL..PONTO_OPERACAO PO with (nolock) on
						PO.PONTO_OPERACAO_ID = CO.Ponto_Operacao_Id
					left join CARGOSOL..pessoa po_pessoa with (nolock) on
						po_pessoa.Pessoa_Id = PO.PESSOA_ID
					left join corpore..pfunc with (nolock) on
						pfunc.chapa = CO.Num_Funcionario_Alfa collate SQL_Latin1_General_CP1_CI_AI
						and pfunc.CODSITUACAO <> 'D'
						and pfunc.CODCOLIGADA = 1
					left join corpore..psecao with (nolock) on
						psecao.CODIGO = pfunc.CODSECAO
						and psecao.CODCOLIGADA = 1
					where celular.status_id = 'a'
					and celular_funcionario.id = $id";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
	
		$num_celular		= odbc_result($result,1);
		$operadora			= odbc_result($result,2);
		$imei				= odbc_result($result,3);	
		$responsavel		= odbc_result($result,4);
		$depto				= odbc_result($result,5);				
		$observacao			= odbc_result($result,6);
		$data_atual			= odbc_result($result,7);
		$data_inicio		= odbc_result($result,8);	
		$obs_linha			= odbc_result($result,9);		
		
		$observacao = utf8_encode($observacao);
		$obs_linha = utf8_encode($obs_linha);
		  
		$retorno->num_celular = $num_celular;
		$retorno->operadora = $operadora;
		$retorno->imei = $imei;  
		$retorno->responsavel = $responsavel;  
		$retorno->depto = $depto;  
		$retorno->observacao = $observacao;  
		$retorno->data_atual = $data_atual; 
		$retorno->data_inicio = $data_inicio;
		$retorno->obs_linha = $obs_linha; 						

		return $retorno;

	}



	public function dadosRegistro($id){
		
		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];
		
		//SELECIONA A CHAVE PRIMARIA
		$query = "select isnull(pessoa.nome_fantasia, pessoa.nome), 
					convert(varchar(10), data_inicio, 103) data_inicio,
					case when data_fim is not null	
							then convert(varchar(10), data_fim, 103)
							else 'EM USO'
					end data_fim,
					cf.cod_ccusto COD_CENTRO_CUSTO,
					GCCUSTO.nome collate sql_latin1_general_cp1251_ci_as centro_custo
					from celular_funcionario cf with (nolock)
					join celular_covre celular with (nolock) on
						celular.id = cf.celular_covre_id
						and celular.status_id = 'a'
					join CARGOSOL..pessoa with (nolock) on
						pessoa.pessoa_id = cf.pessoa_id
					left join corpore..GCCUSTO with (nolock) on
						GCCUSTO.CODREDUZIDO = CAST(cf.cod_ccusto AS VARCHAR)
					join operadora_celular operadora with (nolock) on
						operadora.id = celular.operadora_id
					where celular.id = $id ";
		//print $query;
		$result = $con->executar($query);			
		
		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='300'><a href='javascript:void(0)'>RESPONSÁVEL</a></th>
						<th bgcolor='#d3d3d3' width='120' ><a href='javascript:void(0)'>DATA INÍCIO</a></th>	
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>DATA FIM</a></th>
						<th bgcolor='#d3d3d3' width='150' ><a href='javascript:void(0)'>COD. CENTRO CUSTO</a></th>							
						<th bgcolor='#d3d3d3' width='70' ><a href='javascript:void(0)'>CENTRO CUSTO</a></th>	
					</thead><tbody>
				  ";	
		
		while(odbc_fetch_row($result)) 
		{ 
		
			$nome_responsavel		= odbc_result($result,1);
			$data_inicio			= odbc_result($result,2);
			$data_fim				= odbc_result($result,3);
			$cod_centro_custo		= odbc_result($result,4);						
			$centro_custo			= odbc_result($result,5);


			$table .= "<tr>
						<td bgcolor='#FFFFFF'><center><font size='-1'>".$nome_responsavel."</font></center></td>	
						<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_inicio."</font></center></td>	
						<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_fim."</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>".$cod_centro_custo."</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>".$centro_custo."</font></center></td>
					   </tr>";
					  
			
			if ($cont_col == 6)
			{
				$table .= "</tr>";
				$cont_col = 0;
			}
							
			$cont++;
			$cont_col++;			
		}
		
		$table .= "</table>";

		$retorno->relatorio = $table;

		return $retorno;	
	
	}



	public function exibeDespesaExtra(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select despesa.id,  
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4) celular,
					CONVERT(VARCHAR(10), despesa.data, 103) data,
					valor, despesa.observacao
					from celular_covre celular with (nolock)
					join despesa_extra_celular despesa with (nolock) on
						despesa.celular_covre_id = celular.id
					where 1 = 1
					and despesa.status_id = 'a'
					order by celular";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>NÚMERO CELULAR</a></th>
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>DATA</a></th>	
						<th bgcolor='#d3d3d3' width='200'><a href='javascript:void(0)'>VALOR</a></th>
						<th bgcolor='#d3d3d3' width='200'><a href='javascript:void(0)'>OBSERVAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='50'><a href='javascript:void(0)'>EXCLUIR</a></th>
					</thead><tbody>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$id					= odbc_result($result,1);
			$num_celular		= odbc_result($result,2);
			$data				= odbc_result($result,3);			
			$valor				= odbc_result($result,4);
			$observacao			= odbc_result($result,5);
			
			$observacao = utf8_encode($observacao);
	
			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_despesas_extras($id);'><font size='-1'>".$num_celular."</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$data."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".number_format($valor, 2, ',', '.')."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$observacao."</font></center></td>	
					<td><center><font size='-1'><a href='javascript:exclui_despesa_extra($id);'><img src='../SCA/images/excluir1.jpg' width='30' heigth='30' style='border:none'></a></font></center></td>						
				  </tr>"; 
				 
		 } 
		 
		  $table .= "</tbody></table>";
		  
		  $table .= "<script>order()</script>";
		  
		  $retorno->despesa = $table;

		  return $retorno;

	}



	public function buscaDespesaExtra($id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select 
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4) celular,
					CONVERT(VARCHAR(10), despesa.data, 103), valor, despesa.observacao, despesa.celular_covre_id
					from celular_covre celular with (nolock)
					join despesa_extra_celular despesa with (nolock) on
						despesa.celular_covre_id = celular.id
					where despesa.id = $id";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
	
		$num_celular		= odbc_result($result,1);
		$data				= odbc_result($result,2);
		$valor				= odbc_result($result,3);	
		$observacao			= odbc_result($result,4);		
		$celular_covre_id	= odbc_result($result,5);			
		
		$observacao = utf8_encode($observacao);
	
		  
		$retorno->despesa = "1|".$id."|".$celular_covre_id."|".$data."|".number_format($valor, 2, ',', '.')."|".$observacao."|".$num_celular."|";

		return $retorno;

	}



	public function exibeResponsavel(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select permissao.id, usuario.nome, usuario.usuario, usuario.id,
					case when permissao.status_id = 'a'
							then 'Ativo'
							else 'Inativo'
					end status
					from permissao_celular_covre permissao with (nolock)
					join usuario with (nolock) on
						usuario.id = permissao.usuario_id
					where 1 = 1";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
		$table = "
				<table class='table table-bordered table-hover'>
					<tr>
						<td bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>NOME</a></td>
						<td bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>STATUS</a></td>	
					</tr>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$id				= odbc_result($result,1);
			$nome			= odbc_result($result,2);
			$usuario		= odbc_result($result,3);			
			$usuario_id		= odbc_result($result,4);
			$status			= odbc_result($result,5);
			
	
			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_dados_responsavel($id);'><font size='-1'>".$nome."</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$status."</font></center></td>	
				
				  </tr>"; 
				 
		 } 
		 
		  $table .= "</table>";

		  
		  $retorno->responsavel = $table;

		  return $retorno;

	}
	
	
	public function buscaDadosResponsavel($id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select permissao.id, usuario.nome, usuario.id, usuario.usuario, permissao.administrador, permissao.status_id
					from permissao_celular_covre permissao with (nolock)
					join usuario with (nolock) on
						usuario.id = permissao.usuario_id
					where permissao.id = $id";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
	
		$id					= odbc_result($result,1);
		$nome				= odbc_result($result,2);
		$usuario_id			= odbc_result($result,3);	
		$usuario			= odbc_result($result,4);	
		$administrador		= odbc_result($result,5);		
		$status_id			= odbc_result($result,6);	
		  
		$retorno->responsavel = "1|".$id."|".$nome."|".$usuario_id."|".$usuario."|".$administrador."|".$status_id."|";

		return $retorno;

	}	
	

	
	public function buscaCCResponsavel($id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select crc.centro_custo_id, nome collate sql_latin1_general_cp1251_ci_as, crc.id
					from ccusto_responsavel_celular crc with (nolock)
					join corpore..gccusto with (nolock) on
						gccusto.CODREDUZIDO = crc.centro_custo_id collate SQL_Latin1_General_CP1_CI_AI
						and gccusto.CODCOLIGADA = 1
					where responsavel_id = $id
					and crc.status_id = 'a'";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
		$table = "
				<table class='table table-bordered table-hover'>
					<tr>
						<td bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>COD.</a></td>
						<td bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>CENTRO DE CUSTO</a></td>
						<td bgcolor='#d3d3d3' width='50'><a href='javascript:void(0)'>EXCLUIR</a></td>		
					</tr>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$cod_ccusto		= odbc_result($result,1);
			$centro_custo	= odbc_result($result,2);
			$id_cc			= odbc_result($result,3);
	
			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$cod_ccusto."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$centro_custo."</font></center></td>	
					<td><center><font size='-1'><a href='javascript:exclui_cc_responsavel($id_cc,$id);'><img src='../SCA/images/excluir1.jpg' width='30' heigth='30' style='border:none'></a></font></center></td>	
				
				  </tr>"; 
				 
		 } 
		 
		  $table .= "</table>";

		  
		  $retorno->centro_custo = $table;

		  return $retorno;

	}	



	public function verificaPermissaoCC($ccusto){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select top 1 'ok'
					FROM permissao_celular_covre permissao with (nolock) 
					left join ccusto_responsavel_celular crc with (nolock) on
						crc.responsavel_id = permissao.id
						and crc.status_id = 'a'
					join usuario with (nolock) on
						usuario.id = permissao.usuario_id
						and usuario.status = 'a'
					where usuario.usuario = '$logado'
					and permissao.status_id = 'a'
					and ((centro_custo_id = '$ccusto') or (permissao.administrador = 'S'))";
		//print $query;
		$result = $con->executar($query);
		$permissao_id	= odbc_result($result,1);
		
		$retorno = '';

		  
		$retorno->permissao = $permissao_id;

	    return $retorno;

	}	


	public function exibeCelMotorista($nome_motorista){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "
		SELECT pessoa.nome, 
		'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4) SCI,
		CASE WHEN LEN(cod_pager) = 11
				THEN '('+substring(cod_pager,1,2)+')'+substring(cod_pager,3,5)+'-'+substring(cod_pager,8,4)
				ELSE CASE WHEN LEN(cod_pager) = 9
							THEN substring(cod_pager,1,5)+'-'+substring(cod_pager,6,4)
							ELSE cod_pager
					 END
		END WHATSAPP,
		CASE WHEN ((LEN(REPLACE(REPLACE(celular,' ',''),'-',''))) = 11 AND (SUBSTRING(celular,1,1) <> '0'))
				THEN '('+substring(REPLACE(celular,' ',''),1,2)+')'+substring(REPLACE(celular,' ',''),3,5)+'-'+substring(REPLACE(celular,' ',''),8,4)
				ELSE CASE WHEN LEN(REPLACE(celular,' ','')) = 9
							THEN '('+cast(Ddd_Celular as varchar)+')'+substring(REPLACE(celular,' ',''),1,5)+'-'+substring(REPLACE(celular,' ',''),6,4) 
							ELSE REPLACE(celular,' ','')
					 END
		END CELULAR,
		CASE WHEN LEN(REPLACE(Tel_Recado,'-','')) = 11
				THEN '('+substring(Tel_Recado,1,2)+')'+substring(Tel_Recado,3,5)+'-'+substring(Tel_Recado,8,4)
				ELSE CASE WHEN LEN(Tel_Recado) = 10
							THEN '('+substring(Tel_Recado,1,2)+')'+substring(Tel_Recado,3,4)+'-'+substring(Tel_Recado,7,4)
							ELSE CASE WHEN LEN(Tel_Recado) = 9
										THEN '('+cast(Ddd_Tel_Recado as varchar)+')'+substring(Tel_Recado,1,5)+'-'+substring(Tel_Recado,6,4) 
										ELSE Tel_Recado
								 END
					 END
		END RECADO,
		UPPER(tab_status.desc_status) SITUACAO,
		UPPER(TTV.desc_vinculo collate sql_latin1_general_cp1251_ci_as) VINCULO
		from cargosol..colaborador CO with (nolock) 
		join cargosol..pessoa with (nolock) on
			pessoa.pessoa_id = CO.pessoa_id
			and pessoa.tab_status_id <> 2
		join cargosol..tab_status with (nolock) on	
			tab_status.TAB_STATUS_ID = CO.Tab_Status_Id
		join cargosol..TAB_TIPO_VINCULO TTV with (nolock) on
			TTV.TAB_TIPO_VINCULO_ID = CO.Tab_Tipo_vinculo_Id			
		left join celular_funcionario cf with (nolock) on
			cf.pessoa_id = CO.pessoa_id
			and cf.status_id = 'a'
		left join celular_covre celular with (nolock) on
			celular.id = cf.celular_covre_id
			and celular.status_id = 'a'
		where CO.tab_tipo_colaborador_id = 3
		and CO.Tab_Status_Id not in (2)
		and pessoa.nome like '%$nome_motorista%'
		order by nome";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='300'>MOTORISTA</th>	
						<th bgcolor='#d3d3d3' width='150'>VÍNCULO</th>	
						<th bgcolor='#d3d3d3' width='110'>SITUAÇÃO</th>																								
						<th bgcolor='#d3d3d3' width='110'>LINHA COVRE</th>
						<th bgcolor='#d3d3d3' width='110'>WHATSAPP</th>
						<th bgcolor='#d3d3d3' width='110'>CELULAR</th>
						<th bgcolor='#d3d3d3' width='110'>RECADO</th>

					</thead><tbody>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$nome_motorista		= odbc_result($result,1);
			$sci				= odbc_result($result,2);
			$whatsapp			= odbc_result($result,3);
			$celular			= odbc_result($result,4);
			$recado				= odbc_result($result,5);
			$situacao			= odbc_result($result,6);
			$vinculo			= odbc_result($result,7);
	
			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$nome_motorista."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$vinculo."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$situacao."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$sci."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$whatsapp."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$celular."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$recado."</font></center></td>
				  </tr>"; 
				 
		 } 
		 
		  $table .= "</tbody></table>";
		  
		  $retorno->celular = $table;

		  return $retorno;

	}


	public function exibeModeloCelular(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select modelo.id, marca.descricao marca, modelo.descricao modelo,
					case when modelo.status_id = 'a'
							then 'ATIVO'
							else 'INATIVO'
					end status
					from modelo_aparelho_celular modelo with (nolock)
					join marca_celular marca with (nolock) on
						marca.id = modelo.marca_id
					where 1 = 1
					order by marca, modelo";
		//print "<pre>".$query."</pre>";
		$result = $con->executar($query);
		
		$retorno = '';
			   
		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='200'><a href='javascript:void(0)'>MARCA</a></th>
						<th bgcolor='#d3d3d3' width='500'><a href='javascript:void(0)'>MODELO</a></th>	
						<th bgcolor='#d3d3d3' width='100'><a href='javascript:void(0)'>STATUS</a></th>
					</thead>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$id					= odbc_result($result,1);
			$marca				= odbc_result($result,2);
			$modelo				= odbc_result($result,3);			
			$status				= odbc_result($result,4);
			
			$modelo = utf8_encode($modelo);
	
			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_dados_modelo($id);'><font size='-1'>".$marca."</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$modelo."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$status."</font></center></td>
				  </tr>"; 
				 
		 } 
		 
		  $table .= "</table>";
		  
		  $retorno->modelo = $table;

		  return $retorno;

	}


	public function exibeDadosAparelho(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select aparelho.id, marca.descricao+' '+modelo.descricao marca_modelo, serie, imei1, imei2, observacao,
					case when aparelho.status_id = 'a'
							then 'ATIVO'
							ELSE 'INATIVO'
					end status
					from aparelho_celular aparelho with (nolock)
					join modelo_aparelho_celular modelo with (nolock) on
						modelo.id = aparelho.modelo_id
					join marca_celular marca with (nolock) on
						marca.id = modelo.marca_id
					where 1 = 1
					order by marca_modelo
					";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';

			   
		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='250'><a href='javascript:void(0)'>MARCA / MODELO</a></th>
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>SERIE</a></th>	
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>IMEI</a></th>
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>IMEI</a></th>						
						<th bgcolor='#d3d3d3' width='300'><a href='javascript:void(0)'>OBSERVAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='70'><a href='javascript:void(0)'>STATUS</a></th>
					</thead><tbody>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$id					= odbc_result($result,1);
			$marca_modelo		= odbc_result($result,2);
			$serie				= odbc_result($result,3);			
			$imei1				= odbc_result($result,4);
			$imei2				= odbc_result($result,5);			
			$observacao			= odbc_result($result,6);
			$status_id			= odbc_result($result,7);

			
			$observacao = utf8_encode($observacao);
	
			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_dados_aparelho($id);'><font size='-1'>".$marca_modelo."</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$serie."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$imei1."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$imei2."</font></center></td>					
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$observacao."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$status_id."</font></center></td>
				  </tr>"; 
				 
		 } 
		 
		  $table .= "</tbody></table>";
		  
		  $table .= "<script>order()</script>";
		  
		  $retorno->aparelho = $table;

		  return $retorno;

	}

}


?>