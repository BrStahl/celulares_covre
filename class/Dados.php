<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php";

class Dados extends ConexaoSCI
{


	//BUSCA DADOS
	public function exibeDadosCelular()
	{

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


		while (odbc_fetch_row($result)) {
			$id					= odbc_result($result, 1);
			$num_celular		= odbc_result($result, 2);
			$operadora			= odbc_result($result, 3);
			$imei				= odbc_result($result, 4);
			$status_id			= odbc_result($result, 5);
			$observacao			= odbc_result($result, 6);

			$observacao = utf8_encode($observacao);

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_dados_celular($id);'><font size='-1'>" . $num_celular . "</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $operadora . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $imei . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $observacao . "</font></center></td>
					<td><a href='javascript:void(0)' onclick='dados_registro($id);'>Histórico</a></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $status_id . "</font></center></td>
				  </tr>";
		}

		$table .= "</tbody></table>";

		//$table .= "<script>order()</script/>";

		$retorno->celular = $table;

		return $retorno;
	}


	public function exibeCelular()
	{

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
		$permissao_cc	= odbc_result($result, 1);
		$administrador	= odbc_result($result, 2);

		if ($administrador == 'S')
			$condicao_permissao = "";
		else
			$condicao_permissao = "and exists (
						select top 1 1
						FROM ccusto_responsavel_celular crc with (nolock)
						join permissao_celular_covre permissao with (nolock) on
							permissao.id = crc.responsavel_id
							and permissao.status_id = 'a'
						join usuario with (nolock) on
							usuario.id = permissao.usuario_id
							and usuario.status = 'a'
						left join corpore..GCCUSTO
							on GCCUSTO.CODREDUZIDO = crc.centro_custo_id COLLATE Latin1_General_CI_AS
							and GCCUSTO.CODCOLIGADA = 1
						where crc.status_id = 'a'
						and usuario.usuario = '$logado'
						and (crc.centro_custo_id = laf.cod_ccusto or GCCUSTO.CODCCUSTO = laf.cod_ccusto COLLATE Latin1_General_CI_AS)
					)";



		$query = "select laf.id,  coalesce(FUNCIONARIO.nome, TERCEIRO.nome, pessoa.nome_fantasia, pessoa.nome) PESSOA,
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4) celular,
					convert(varchar(10), data_inicio, 103) data_inicio,
					laf.observacao,
					convert(varchar(10), laf.data_gravacao, 103) data_gravacao,
					COALESCE(FUNCIONARIO.COD_CENTRO_CUSTO, TERCEIRO.COD_CENTRO_CUSTO, GCCUSTO.CODREDUZIDO, laf.cod_ccusto) COD_CENTRO_CUSTO,
					COALESCE(FUNCIONARIO.CENTRO_CUSTO, TERCEIRO.CENTRO_CUSTO, GCCUSTO.NOME, LAF_CCUSTO.NOME) centro_custo,
					operadora.descricao operadora,
					marca.descricao+' '+modelo.descricao+' S/N: '+
					aparelho.serie+
					case when aparelho.imei1 is not null
							then ' IMEI: '+aparelho.imei1
							else ''
					end+
					case when aparelho.imei2 is not null
							then ' IMEI: '+aparelho.imei2
							else ''
					end descricao_aparelho,
					celular.id celular_id,
					laf.aparelho_id
					from linha_aparelho_funcionario laf with (nolock)
					join celular_covre celular with (nolock) on
						celular.id = laf.celular_id
						and celular.status_id = 'a'
					join operadora_celular operadora with (nolock) on
						operadora.id = celular.operadora_id
					left join aparelho_celular aparelho with (nolock) on
						aparelho.id = laf.aparelho_id
					left join modelo_aparelho_celular modelo with (nolock) on
						modelo.id = aparelho.modelo_id
					left join marca_celular marca with (nolock) on
						marca.id = modelo.marca_id

				/** Atualização 10/09/2024 **/

					/* Funcionario RM */
					OUTER APPLY (
						SELECT 
							PFUNC.CODPESSOA AS CORP_PESSOA_ID,
							PFUNC.NOME COLLATE SQL_Latin1_General_CP1_CI_AI AS NOME,
							PCCUSTO.CODCCUSTO COLLATE Latin1_General_CI_AS + '.567.569' AS COD_CENTRO_CUSTO,
							PCCUSTO.NOME AS CENTRO_CUSTO
						FROM corpore..PFUNC WITH (NOLOCK)
						LEFT JOIN corpore..PFRATEIOFIXO WITH (NOLOCK) ON PFUNC.CHAPA = PFRATEIOFIXO.CHAPA
							AND PFRATEIOFIXO.CODCOLIGADA = 1
						LEFT JOIN corpore..PCCUSTO WITH (NOLOCK) ON PFRATEIOFIXO.CODCCUSTO = PCCUSTO.CODCCUSTO
							AND PCCUSTO.CODCOLIGADA = 1
						WHERE PFUNC.CODPESSOA = LAF.CORP_PESSOA_ID
							and PFUNC.CODCOLIGADA = 1	
							and PFUNC.CODSITUACAO <> 'D'
					) FUNCIONARIO

					/* Terceiro RM */
					OUTER APPLY (
						SELECT 
							PPESSOA.CODIGO AS CORP_PESSOA_ID,
							PPESSOA.NOME AS NOME,
							GCCUSTO.CODREDUZIDO AS COD_CENTRO_CUSTO,
							GCCUSTO.NOME AS CENTRO_CUSTO
						FROM corpore..PPESSOA WITH (NOLOCK)
						JOIN corpore..VPCOMPL WITH (NOLOCK) ON VPCOMPL.CODPESSOA = PPESSOA.CODIGO
							AND VPCOMPL.TIPO = 2
							AND VPCOMPL.DTDES IS NULL
						LEFT JOIN corpore..ILOCAL WITH (NOLOCK) ON ILOCAL.NOME = PPESSOA.NOME
							AND ILOCAL.CODCOLIGADA = 1
						LEFT JOIN corpore..GCCUSTO WITH (NOLOCK) 
							ON GCCUSTO.CODCCUSTO = ILOCAL.CODCCUSTO
							AND GCCUSTO.CODCOLIGADA = 1
						WHERE PPESSOA.CODIGO = LAF.CORP_PESSOA_ID
					) TERCEIRO
					
					/* Cargosol */
					LEFT JOIN CARGOSOL..pessoa with (nolock) on
						pessoa.pessoa_id = laf.pessoa_id
					LEFT JOIN corpore..GCCUSTO with (nolock) on
						GCCUSTO.CODREDUZIDO = laf.cod_ccusto  COLLATE Latin1_General_CI_AS
					LEFT JOIN corpore..PCCUSTO LAF_CCUSTO (NOLOCK) ON  
						LAF_CCUSTO.CODCCUSTO = REPLACE(REPLACE(laf.cod_ccusto, '.557.569', ''), '.567.569', '') COLLATE Latin1_General_CI_AS

				/*****************************/

					where laf.status_id = 'a' 
					and ( FUNCIONARIO.CORP_PESSOA_ID IS NOT NULL 
						OR TERCEIRO.CORP_PESSOA_ID IS NOT NULL 
						OR PESSOA.PESSOA_ID IS NOT NULL )
						$condicao_permissao
					order by celular ";
		//$condicao_permissao
		//print "<pre>" . $query . "</pre>";
		$result = $con->executar($query);

		$retorno = '';

		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='250'><a href='javascript:void(0)'>RESPONSÁVEL</a></th>
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>NÚMERO CELULAR</a></th>
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>OPERADORA</a></th>
						<th bgcolor='#d3d3d3' width='210' ><a href='javascript:void(0)'>APARELHO CELULAR</a></th>
						<th bgcolor='#d3d3d3' width='70' ><a href='javascript:void(0)'>COD. CENTRO CUSTO</a></th>
						<th bgcolor='#d3d3d3' ><a href='javascript:void(0)'>CENTRO CUSTO</a></th>
						<th bgcolor='#d3d3d3' width='90' ><a href='javascript:void(0)'>DATA INÍCIO</a></th>
						<th bgcolor='#d3d3d3'><a href='javascript:void(0)'>OBSERVAÇÃO</a></th>
						<th bgcolor='#d3d3d3'><a href='javascript:void(0)'>PROTOCOLO</a></th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$id					= odbc_result($result, 1);
			$nome_responsavel	= odbc_result($result, 2);
			$num_celular		= odbc_result($result, 3);
			$data_inicio		= odbc_result($result, 4);
			$observacao			= odbc_result($result, 5);
			$cod_custo			= odbc_result($result, 7);
			$centro_custo		= utf8_encode(odbc_result($result, 8));
			$operadora			= odbc_result($result, 9);
			$aparelho			= odbc_result($result, 10);
			$celular_id			= odbc_result($result, 11);
			$aparelho_id		= odbc_result($result, 12);

			$observacao = utf8_encode($observacao);

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_celular($id);'><font size='-1'>" . $nome_responsavel . "</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:dados_registro($celular_id);'><font size='-1'>" . $num_celular . "</font></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $operadora . "</font></center></td>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:dados_aparelho($aparelho_id);'><font size='-1'>" . $aparelho . "</font></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $cod_custo . "</font></center></td>
					<td bgcolor='#FFFFFF' style='white-space: normal; word-break: break-all;'><center><font size='-1'>" . $centro_custo . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_inicio . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $observacao . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><input type='button' class='btn-sm btn-primary' value='Protocolo' id='protocolo' name='protocolo'
								onClick='javascript:pagina(" . chr(34) . "protocolo_entrega.php?id=$id" . chr(34) . "," . chr(34) . "1100" . chr(34) . "," . chr(34) . "800" . chr(34) . "," . chr(34) . "protocolo" . chr(34) . ")'></td>
				  </tr>";
		}

		$table .= "</tbody></table>";

		//$table .= "<script>order()</script/>";

		$retorno->celular = $table;

		return $retorno;
	}


	public function buscaDadosCelular($id)
	{

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


		$num_celular		= odbc_result($result, 1);
		$operadora_id		= odbc_result($result, 2);
		$imei				= odbc_result($result, 3);
		$status_id			= odbc_result($result, 4);
		$observacao			= odbc_result($result, 5);


		$observacao = utf8_encode($observacao);


		$retorno->celular = "1|" . $id . "|" . $num_celular . "|" . $operadora_id . "|" . $imei . "|" . $status_id . "|" . $observacao . "|";

		return $retorno;
	}


	public function buscaDadosModelo($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select marca_id, descricao, status_id
				  from modelo_aparelho_celular
				  where id = $id";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';


		$marca_id			= odbc_result($result, 1);
		$descricao			= odbc_result($result, 2);
		$status_id			= odbc_result($result, 3);


		$descricao = utf8_encode($descricao);


		$retorno->modelo = "1|" . $id . "|" . $marca_id . "|" . $descricao . "|" . $status_id . "|";

		return $retorno;
	}


	public function buscaDadosAparelho($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select modelo_id, serie, imei1, imei2, observacao, status_id
					from aparelho_celular aparelho with (nolock)
					where id = $id";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';


		$modelo_id			= odbc_result($result, 1);
		$serie				= odbc_result($result, 2);
		$imei1				= odbc_result($result, 3);
		$imei2				= odbc_result($result, 4);
		$observacao			= odbc_result($result, 5);
		$status_id			= odbc_result($result, 6);

		$observacao = utf8_encode($observacao);


		$retorno->aparelho = "1|" . $id . "|" . $modelo_id . "|" . $serie . "|" . $imei1 . "|" . $imei2 . "|" . $observacao . "|" . $status_id . "|";

		return $retorno;
	}


	public function buscaCelular($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select linha_aparelho_funcionario.pessoa_id,
					celular_id celular,
					convert(varchar(10), data_inicio, 103), linha_aparelho_funcionario.observacao, 
					coalesce(ppessoa.nome COLLATE SQL_Latin1_General_CP1_CI_AS, pessoa.nome_fantasia, pessoa.nome),
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4),
					cod_ccusto,
					linha_aparelho_funcionario.aparelho_id,
					marca.descricao+' '+modelo.descricao+' S/N: '+aparelho.serie aparelho,
					linha_aparelho_funcionario.corp_pessoa_id
					from linha_aparelho_funcionario with (nolock)
					
					/** Atualização 10/09/2024 **/
					LEFT JOIN CORPORE..PPESSOA WITH(NOLOCK) ON 
						PPESSOA.CODIGO = linha_aparelho_funcionario.corp_pessoa_id
					LEFT JOIN CARGOSOL..pessoa with (nolock) on
						pessoa.pessoa_id = linha_aparelho_funcionario.pessoa_id
					/*****************************/

					join celular_covre celular with (nolock) on
						celular.id = linha_aparelho_funcionario.celular_id
						and celular.status_id = 'a'
					left join aparelho_celular aparelho with (nolock) on
						aparelho.id = linha_aparelho_funcionario.aparelho_id
					left join modelo_aparelho_celular modelo with (nolock) on
						modelo.id = aparelho.modelo_id
					left join marca_celular marca with (nolock) on
						marca.id = modelo.marca_id
					where linha_aparelho_funcionario.status_id = 'a'
					and linha_aparelho_funcionario.id = $id
					and  (PPESSOA.CODIGO IS NOT NULL OR PESSOA.PESSOA_ID IS NOT NULL)";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';


		$pessoa_id			= odbc_result($result, 1);
		$celular_id			= odbc_result($result, 2);
		$data_inicio		= odbc_result($result, 3);
		$observacao			= odbc_result($result, 4);
		$nome_responsavel	= odbc_result($result, 5);
		$num_celular		= odbc_result($result, 6);
		$cod_ccusto			= odbc_result($result, 7);
		$aparelho_id		= odbc_result($result, 8);
		$aparelho_celular	= odbc_result($result, 9);
		$corp_pessoa_id			= odbc_result($result, 10);

		$observacao = utf8_encode($observacao);


		$retorno->celular = "1|" . $id . "|" . $pessoa_id . "|" . $celular_id . "|" . $data_inicio . "|" . $observacao . "|" . $nome_responsavel . "|" . $num_celular . "|" . $cod_ccusto . "|" . $aparelho_id . "|" . $aparelho_celular . "|" . $corp_pessoa_id . "|";

		return $retorno;
	}


	public function buscaAgregados()
	{

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
												 FROM linha_aparelho_funcionario WITH (NOLOCK)
												 WHERE PESSOA_ID = CO.PESSOA_ID
												 AND STATUS_ID = 'A')
					ORDER BY PESSOA.NOME";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';
		$table = '<option></option>';

		while (odbc_fetch_row($result)) {

			if (odbc_result($result, 1) == $pessoa_id)
				$selected = "selected='selected'";
			else
				$selected = "";

			$table .= "<option value='" . odbc_result($result, 1) . "'$selected>" . utf8_encode(odbc_result($result, 2)) . "</option>";
		}

		$retorno->agregados = $table;

		return $retorno;
	}


	public function buscaCelulares()
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select celular.id,
					'('+substring(num_celular,1,2)+')'+substring(num_celular,3,5)+'-'+substring(num_celular,8,4) celular
					from celular_covre celular with (nolock)
					join linha_disponivel_estoque linha with (nolock) on
						linha.linha_id = celular.id
					where celular.status_id = 'a'
					and linha.status_id = 'a'
					and linha.estoque_id in ((select top 1 estoque_id
											  from permissao_celular_covre with (nolock)
											  where usuario_id = (select top 1 id
																  from usuario with (nolock)
																  where usuario = '$logado'
																  and status = 'a')
											  and permissao_celular_covre.status_id = 'a'
											  ))
					and celular.id not in (select celular_id
										   from linha_aparelho_funcionario with (nolock)
										   where linha_aparelho_funcionario.status_id = 'a'
										   and celular_id = celular.id)
					order by celular";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';
		$table = '<option></option>';

		while (odbc_fetch_row($result)) {

			if (odbc_result($result, 1) == $celular_id)
				$selected = "selected='selected'";
			else
				$selected = "";

			$table .= "<option value='" . odbc_result($result, 1) . "'$selected>" . utf8_encode(odbc_result($result, 2)) . "</option>";
		}

		$retorno->celulares = $table;

		return $retorno;
	}


	public function buscaCelularesDespesa()
	{

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

		while (odbc_fetch_row($result)) {

			if (odbc_result($result, 1) == $celular_id)
				$selected = "selected='selected'";
			else
				$selected = "";

			$table .= "<option value='" . odbc_result($result, 1) . "'$selected>" . utf8_encode(odbc_result($result, 2)) . "</option>";
		}

		$retorno->celulares = $table;

		return $retorno;
	}

	public function buscaParametro()
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select valor, centro_custo_padrao
				  from parametro_celular_agregado";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';

		$valor				= odbc_result($result, 1);
		$centro_custo_padrao				= odbc_result($result, 2);

		$valor = number_format($valor, 2, ',', '.');


		$retorno->valor = $valor;
		$retorno->centro_custo_padrao = $centro_custo_padrao;

		return $retorno;
	}



	public function buscaDadosProtocolo($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select
					'<b>LINHA:</b><br> ('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4)+' '+
					operadora.descricao+' IMEI: '+celular.imei,

					case when linha_aparelho_funcionario.aparelho_id is not null
							then '<b>APARELHO:</b><br>'+marca.descricao+' '+modelo.descricao+' S/N: '+aparelho.serie+
								case when aparelho.imei1 is not null
										then ' IMEI: '+aparelho.imei1
										else ''
								end+
								case when aparelho.imei2 is not null
										then ' IMEI: '+aparelho.imei2
										else ''
								end
							ELSE NULL
					END APARELHO,
					pessoa.Nome_Fantasia,
					isnull(psecao.DESCRICAO, po_pessoa.Nome_Fantasia) depto,
					convert(varchar(10), getdate(), 103) data_atual,
					convert(varchar(10), linha_aparelho_funcionario.data_inicio, 103) data_inicio,
					case when linha_aparelho_funcionario.observacao is not null
							then '<b>OBSERVA&Ccedil;&Atilde;O: </b><br>'+linha_aparelho_funcionario.observacao
							else null
					end
					from celular_covre celular with (nolock)
					join operadora_celular operadora with (nolock) on
						operadora.id = celular.operadora_id
					join linha_aparelho_funcionario with (nolock) on
						linha_aparelho_funcionario.celular_id = celular.id
					left join aparelho_celular aparelho with (nolock) on
						aparelho.id = linha_aparelho_funcionario.aparelho_id
					left join modelo_aparelho_celular modelo with (nolock) on
						modelo.id = aparelho.modelo_id
					left join marca_celular marca with (nolock) on
						marca.id = modelo.marca_id
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = linha_aparelho_funcionario.pessoa_id
					left join CARGOSOL..COLABORADOR CO with (nolock) on
						CO.Pessoa_Id = pessoa.Pessoa_Id
						AND CO.TAB_STATUS_ID <> 2
					LEFT JOIN CARGOSOL..PONTO_OPERACAO PO with (nolock) on
						PO.PONTO_OPERACAO_ID = CO.Ponto_Operacao_Id
					left join CARGOSOL..pessoa po_pessoa with (nolock) on
						po_pessoa.Pessoa_Id = PO.PESSOA_ID
					left join CORPORE..pfunc with (nolock) on
						pfunc.chapa = CO.Num_Funcionario_Alfa collate SQL_Latin1_General_CP1_CI_AI
						and pfunc.CODSITUACAO <> 'D'
						and pfunc.CODCOLIGADA = 1
					left join CORPORE..psecao with (nolock) on
						psecao.CODIGO = pfunc.CODSECAO
						and psecao.CODCOLIGADA = 1
					where celular.status_id = 'a'
					and linha_aparelho_funcionario.id = $id";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';


		$num_celular		= odbc_result($result, 1);
		$aparelho			= odbc_result($result, 2);
		$responsavel		= odbc_result($result, 3);
		$depto				= odbc_result($result, 4);
		$data_atual			= odbc_result($result, 5);
		$data_inicio		= odbc_result($result, 6);
		$observacao			= odbc_result($result, 7);

		$observacao = utf8_encode($observacao);

		$retorno->num_celular = $num_celular;
		$retorno->aparelho = $aparelho;
		$retorno->responsavel = $responsavel;
		$retorno->depto = $depto;
		$retorno->observacao = $observacao;
		$retorno->data_atual = $data_atual;
		$retorno->data_inicio = $data_inicio;
		$retorno->observacao = $observacao;

		return $retorno;
	}



	public function dadosRegistro($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		//VERIFICA SE TEM PERMISSAO DE ADMINISTRADOR
		$query = "select permissao.administrador
					from permissao_celular_covre permissao with (nolock)
					where permissao.usuario_id = (select top 1 id from usuario where usuario = '$logado' and status = 'a')
					and status_id = 'a'";
		//print $query;
		$result = $con->executar($query);
		$administrador	= odbc_result($result, 1);


		//SELECIONA A CHAVE PRIMARIA
		$query = "select
			COALESCE(FUNCIONARIO.nome, TERCEIRO.nome, ECARGO.nome),
			convert(varchar(10), data_inicio, 103) data_inicio,
			case
				when data_fim is not null then convert(varchar(10), data_fim, 103)
				else 'EM USO'
			end data_fim,
			COALESCE(
				FUNCIONARIO.COD_CENTRO_CUSTO,
				TERCEIRO.COD_CENTRO_CUSTO,
				ECARGO.COD_CENTRO_CUSTO,
				laf.cod_ccusto
			) COD_CENTRO_CUSTO,
			COALESCE(
				FUNCIONARIO.CENTRO_CUSTO,
				TERCEIRO.CENTRO_CUSTO,
				ECARGO.CENTRO_CUSTO,
				LAF_CCUSTO.NOME
			) centro_custo,
			convert(varchar(10), laf.data_gravacao, 103) + ' ' + convert(varchar(10), laf.data_gravacao, 108) data_gravacao,
			user_gravacao.nome user_gravacao,
			convert(varchar(10), laf.data_inativacao, 103) + ' ' + convert(varchar(10), laf.data_inativacao, 108) data_inat,
			user_inativacao.nome user_inativacao,
			laf.id
		from
			linha_aparelho_funcionario laf with (nolock)
			join celular_covre celular with (nolock) on celular.id = laf.celular_id
			join operadora_celular operadora with (nolock) on operadora.id = celular.operadora_id
			join usuario user_gravacao with (nolock) on user_gravacao.id = laf.user_gravacao
			LEFT JOIN corpore..PCCUSTO LAF_CCUSTO (NOLOCK) ON LAF_CCUSTO.CODCCUSTO = REPLACE(laf.cod_ccusto, '.567.569', '') COLLATE Latin1_General_CI_AS
			/* Funcionario RM */
			OUTER APPLY (
				SELECT
					PFUNC.CODPESSOA AS CORP_PESSOA_ID,
					PFUNC.NOME COLLATE SQL_Latin1_General_CP1_CI_AI AS NOME,
					PCCUSTO.CODCCUSTO COLLATE Latin1_General_CI_AS + '.567.569' AS COD_CENTRO_CUSTO,
					PCCUSTO.NOME AS CENTRO_CUSTO
				FROM
					corpore..PFUNC WITH (NOLOCK)
					LEFT JOIN corpore..PFRATEIOFIXO WITH (NOLOCK) ON PFUNC.CHAPA = PFRATEIOFIXO.CHAPA
					AND PFRATEIOFIXO.CODCOLIGADA = 1
					LEFT JOIN corpore..PCCUSTO WITH (NOLOCK) ON PFRATEIOFIXO.CODCCUSTO = PCCUSTO.CODCCUSTO
					AND PCCUSTO.CODCOLIGADA = 1
				WHERE
					PFUNC.CODPESSOA = LAF.CORP_PESSOA_ID
					and PFUNC.CODCOLIGADA = 1
					and PFUNC.CODSITUACAO <> 'D'
			) FUNCIONARIO
			/* Terceiro RM */
			OUTER APPLY (
				SELECT
					PPESSOA.CODIGO AS CORP_PESSOA_ID,
					PPESSOA.NOME AS NOME,
					GCCUSTO.CODREDUZIDO AS COD_CENTRO_CUSTO,
					GCCUSTO.NOME AS CENTRO_CUSTO
				FROM
					corpore..PPESSOA WITH (NOLOCK)
					JOIN corpore..VPCOMPL WITH (NOLOCK) ON VPCOMPL.CODPESSOA = PPESSOA.CODIGO
					AND VPCOMPL.TIPO = 2
					AND VPCOMPL.DTDES IS NULL
					LEFT JOIN corpore..ILOCAL WITH (NOLOCK) ON ILOCAL.NOME = PPESSOA.NOME
					AND ILOCAL.CODCOLIGADA = 1
					LEFT JOIN corpore..GCCUSTO WITH (NOLOCK) ON GCCUSTO.CODCCUSTO = ILOCAL.CODCCUSTO
					AND GCCUSTO.CODCOLIGADA = 1
				WHERE
					PPESSOA.CODIGO = LAF.CORP_PESSOA_ID
			) TERCEIRO
			/* Ecargo */
			OUTER APPLY(
				SELECT
					pessoa.pessoa_id AS PESSOA_ID,
					COALESCE(pessoa.nome_fantasia, pessoa.nome) AS NOME,
					GCCUSTO.CODREDUZIDO AS COD_CENTRO_CUSTO,
					GCCUSTO.NOME AS CENTRO_CUSTO
				from
					CARGOSOL..pessoa with (nolock)
					left join corpore..GCCUSTO with (nolock) on GCCUSTO.CODREDUZIDO = laf.cod_ccusto COLLATE Latin1_General_CI_AS
				WHERE
					pessoa.pessoa_id = laf.pessoa_id
			) ECARGO
			/* and celular.status_id = 'a' */
			left join usuario user_inativacao with (nolock) on user_inativacao.id = laf.user_inativacao
		where celular.id = $id 
			AND (
				FUNCIONARIO.CORP_PESSOA_ID IS NOT NULL
				OR TERCEIRO.CORP_PESSOA_ID IS NOT NULL
				OR ECARGO.PESSOA_ID IS NOT NULL
			)";

		//print ($query);
		//die();
		$result = $con->executar($query);

		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='250'><a href='javascript:void(0)'>RESPONSÁVEL</a></th>
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>DATA INÍCIO</a></th>
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>DATA FIM</a></th>
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>COD. CENTRO CUSTO</a></th>
						<th bgcolor='#d3d3d3' width='70' ><a href='javascript:void(0)'>CENTRO CUSTO</a></th>
						<th bgcolor='#d3d3d3' width='70' ><a href='javascript:void(0)'>DATA GRAVAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='150' ><a href='javascript:void(0)'>USUÁRIO GRAVAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='70' ><a href='javascript:void(0)'>DATA FINALIZAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='150' ><a href='javascript:void(0)'>USUÁRIO FINALIZAÇÃO</a></th>
					</thead><tbody>
				  ";

		while (odbc_fetch_row($result)) {

			$nome_responsavel		= odbc_result($result, 1);
			$data_inicio			= odbc_result($result, 2);
			$data_fim				= odbc_result($result, 3);
			$cod_centro_custo		= odbc_result($result, 4);
			$centro_custo			= utf8_encode(odbc_result($result, 5));
			$data_gravacao			= odbc_result($result, 6);
			$user_gravacao			= odbc_result($result, 7);
			$data_inativacao		= odbc_result($result, 8);
			$user_inativacao		= odbc_result($result, 9);
			$laf_id					= odbc_result($result, 10);


			$table .= "<tr>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $nome_responsavel . "</font></center></td>";

			if ($administrador == 'S')
				$table .= "
						<td bgcolor='#FFFFFF'><center><font size='-1'><input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data_inicio' id='data_inicio' value='$data_inicio' size='6' maxlength='10' onblur='javascript:altera_data(this,$laf_id,1)'></font></center></td>";
			else
				$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_inicio . "</font></center></td>";

			if (($data_fim != 'EM USO')	&& ($administrador == 'S'))
				$table .= "
						<td bgcolor='#FFFFFF'><center><font size='-1'><input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data_fim' id='data_fim' value='$data_fim' size='6' maxlength='10' onblur='javascript:altera_data(this,$laf_id,2)'></font></center></td>";
			else
				$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_fim . "</font></center></td>";

			$table .= "	<td bgcolor='#FFFFFF'><center><font size='-1'>" . $cod_centro_custo . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $centro_custo . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_gravacao . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $user_gravacao . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_inativacao . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $user_inativacao . "</font></center></td>
					   </tr>";


			if ($cont_col == 6) {
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



	public function exibeDespesaExtra()
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select despesa.id,
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4) celular,
					CONVERT(VARCHAR(10), despesa.data, 103) data,
					valor, despesa.observacao
					from celular_covre celular with (nolock)
					join despesa_extra_celular despesa with (nolock) on
						despesa.celular_id = celular.id
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


		while (odbc_fetch_row($result)) {
			$id					= odbc_result($result, 1);
			$num_celular		= odbc_result($result, 2);
			$data				= odbc_result($result, 3);
			$valor				= odbc_result($result, 4);
			$observacao			= odbc_result($result, 5);

			$observacao = utf8_encode($observacao);

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_despesas_extras($id);'><font size='-1'>" . $num_celular . "</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . number_format($valor, 2, ',', '.') . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $observacao . "</font></center></td>
					<td><center><font size='-1'><a href='javascript:exclui_despesa_extra($id);'><img src='../SCA/images/excluir1.jpg' width='30' heigth='30' style='border:none'></a></font></center></td>
				  </tr>";
		}

		$table .= "</tbody></table>";

		//$table .= "<script>order()</script/>";

		$retorno->despesa = $table;

		return $retorno;
	}



	public function buscaDespesaExtra($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select
					'('+substring(celular.num_celular,1,2)+')'+substring(celular.num_celular,3,5)+'-'+substring(celular.num_celular,8,4) celular,
					CONVERT(VARCHAR(10), despesa.data, 103), valor, despesa.observacao, despesa.celular_id
					from celular_covre celular with (nolock)
					join despesa_extra_celular despesa with (nolock) on
						despesa.celular_id = celular.id
					where despesa.id = $id";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';


		$num_celular		= odbc_result($result, 1);
		$data				= odbc_result($result, 2);
		$valor				= odbc_result($result, 3);
		$observacao			= odbc_result($result, 4);
		$celular_id			= odbc_result($result, 5);

		$observacao = utf8_encode($observacao);


		$retorno->despesa = "1|" . $id . "|" . $celular_id . "|" . $data . "|" . number_format($valor, 2, ',', '.') . "|" . $observacao . "|" . $num_celular . "|";

		return $retorno;
	}



	public function exibeResponsavel()
	{

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
					where 1 = 1
					order by usuario.nome";
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


		while (odbc_fetch_row($result)) {
			$id				= odbc_result($result, 1);
			$nome			= odbc_result($result, 2);
			$usuario		= odbc_result($result, 3);
			$usuario_id		= odbc_result($result, 4);
			$status			= odbc_result($result, 5);


			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_dados_responsavel($id);'><font size='-1'>" . $nome . "</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $status . "</font></center></td>

				  </tr>";
		}

		$table .= "</table>";


		$retorno->responsavel = $table;

		return $retorno;
	}


	public function buscaDadosResponsavel($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select permissao.id, usuario.nome, usuario.id, usuario.usuario, permissao.administrador, permissao.status_id,
					permissao.estoque_id, permissao.gerar_vale_id
					from permissao_celular_covre permissao with (nolock)
					join usuario with (nolock) on
						usuario.id = permissao.usuario_id
					where permissao.id = $id";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';


		$id					= odbc_result($result, 1);
		$nome				= odbc_result($result, 2);
		$usuario_id			= odbc_result($result, 3);
		$usuario			= odbc_result($result, 4);
		$administrador		= odbc_result($result, 5);
		$status_id			= odbc_result($result, 6);
		$estoque_id			= odbc_result($result, 7);
		$gerar_vale_id		= odbc_result($result, 8);

		$retorno->responsavel = "1|" . $id . "|" . $nome . "|" . $usuario_id . "|" . $usuario . "|" . $administrador . "|" . $status_id . "|" . $estoque_id . "|" . $gerar_vale_id . "|";

		return $retorno;
	}



	public function buscaCCResponsavel($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select crc.centro_custo_id, nome collate sql_latin1_general_cp1251_ci_as, crc.id
					from ccusto_responsavel_celular crc with (nolock)
					join CORPORE..gccusto with (nolock) on
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


		while (odbc_fetch_row($result)) {
			$cod_ccusto		= odbc_result($result, 1);
			$centro_custo	= odbc_result($result, 2);
			$id_cc			= odbc_result($result, 3);

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $cod_ccusto . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $centro_custo . "</font></center></td>
					<td><center><font size='-1'><a href='javascript:exclui_cc_responsavel($id_cc,$id);'><img src='../SCA/images/excluir1.jpg' width='30' heigth='30' style='border:none'></a></font></center></td>

				  </tr>";
		}

		$table .= "</table>";


		$retorno->centro_custo = $table;

		return $retorno;
	}



	public function verificaPermissaoCC($ccusto)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select top 1 'ok'
					FROM permissao_celular_covre permissao with (nolock)
					join usuario with (nolock) on
						usuario.id = permissao.usuario_id
						and usuario.status = 'a'
					left join ccusto_responsavel_celular crc with (nolock) on
						crc.responsavel_id = permissao.id
						and crc.status_id = 'a'
					left join corpore..GCCUSTO
						on GCCUSTO.CODREDUZIDO = crc.centro_custo_id COLLATE Latin1_General_CI_AS
						and GCCUSTO.CODCOLIGADA = 1
					where usuario.usuario = '$logado'
					and permissao.status_id = 'a'
					and ((centro_custo_id = '$ccusto') or (gccusto.codccusto = '$ccusto') or (permissao.administrador = 'S'))";
		//print $query;
		$result = $con->executar($query);
		$permissao_id	= odbc_result($result, 1);

		$retorno = '';


		$retorno->permissao = $permissao_id;

		return $retorno;
	}


	public function verificaCcValido($ccusto)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select CODREDUZIDO
				  FROM CORPORE..GCCUSTO with (nolock)
				  where codcoligada = 1
				  and CODREDUZIDO = '$ccusto'";
		//print $query;
		$result = $con->executar($query);
		$cc_valido	= odbc_result($result, 1);

		$retorno = '';


		$retorno->cc_valido = $cc_valido;

		return $retorno;
	}

	public function exibeCelMotorista($nome_motorista)
	{

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
		left join linha_aparelho_funcionario laf with (nolock) on
			laf.pessoa_id = CO.pessoa_id
			and laf.status_id = 'a'
		left join celular_covre celular with (nolock) on
			celular.id = laf.celular_id
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


		while (odbc_fetch_row($result)) {
			$nome_motorista		= odbc_result($result, 1);
			$sci				= odbc_result($result, 2);
			$whatsapp			= odbc_result($result, 3);
			$celular			= odbc_result($result, 4);
			$recado				= odbc_result($result, 5);
			$situacao			= odbc_result($result, 6);
			$vinculo			= odbc_result($result, 7);

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $nome_motorista . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $vinculo . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $situacao . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $sci . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $whatsapp . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $celular . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $recado . "</font></center></td>
				  </tr>";
		}

		$table .= "</tbody></table>";

		$retorno->celular = $table;

		return $retorno;
	}


	public function exibeModeloCelular()
	{

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


		while (odbc_fetch_row($result)) {
			$id					= odbc_result($result, 1);
			$marca				= odbc_result($result, 2);
			$modelo				= odbc_result($result, 3);
			$status				= odbc_result($result, 4);

			$modelo = utf8_encode($modelo);

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center>
						<a href='javascript:busca_dados_modelo($id);'><font size='-1'>" . $marca . "</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $modelo . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $status . "</font></center></td>
				  </tr>";
		}

		$table .= "</table>";

		$retorno->modelo = $table;

		return $retorno;
	}


	public function exibeDadosAparelho()
	{

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
						<th bgcolor='#d3d3d3' width='130'><a href='javascript:void(0)'>SERIE</a></th>
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>IMEI</a></th>
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>IMEI</a></th>
						<th bgcolor='#d3d3d3' width='350'><a href='javascript:void(0)'>OBSERVAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='80'><a href='javascript:void(0)'>HISTÓRICO</a></th>
						<th bgcolor='#d3d3d3' width='70'><a href='javascript:void(0)'>STATUS</a></th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$id					= odbc_result($result, 1);
			$marca_modelo		= odbc_result($result, 2);
			$serie				= odbc_result($result, 3);
			$imei1				= odbc_result($result, 4);
			$imei2				= odbc_result($result, 5);
			$observacao			= odbc_result($result, 6);
			$status_id			= odbc_result($result, 7);


			$observacao = utf8_encode($observacao);

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'>
						<center><a href='javascript:busca_dados_aparelho($id);'><font size='-1'>" . $marca_modelo . "</font></a></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $serie . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $imei1 . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $imei2 . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $observacao . "</font></center></td>
					<td><a href='javascript:void(0)' onclick='dados_aparelho($id);'>Histórico</a></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $status_id . "</font></center></td>
				  </tr>";
		}

		$table .= "</tbody></table>";

		//$table .= "<script>order()</script/>";

		$retorno->aparelho = $table;

		return $retorno;
	}


	public function exibePlanoAgregado()
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$query = "select id, descricao
					from plano_celular
					where status_id = 'a'";
		//print $query;
		$result2 = $con->executar($query);
		$lista_plano = '';
		while (odbc_fetch_row($result2)) {
			$plano_id2	= odbc_result($result2, 1);
			$descricao_plano2 = odbc_result($result2, 2);

			$lista_plano .= "<option value='$plano_id2'>$descricao_plano2</option>";
		} //while
		$lista_motorista_plano = $lista_plano;


		$query = "select pessoa.nome, convert(varchar(10), data_inicio, 103) data_inicio, plano_id, laf.id
					from linha_aparelho_funcionario laf WITH (NOLOCK)
					join cargosol..pessoa with (nolock) on
						pessoa.pessoa_id = laf.pessoa_id
					join cargosol..Colaborador co with (nolock) on
						co.colaborador_id = pessoa.Pessoa_Id
					where laf.status_id = 'a'
					and co.Tab_Tipo_Colaborador_Id = 3
					and co.Tab_Tipo_Vinculo_Id not in (1)
					order by pessoa.nome";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';

		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='250'><a href='javascript:void(0)'>RESPONSÁVEL</a></th>
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>DATA INÍCIO</a></th>
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>PLANO</a></th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$nome_responsavel	= odbc_result($result, 1);
			$data_inicio		= odbc_result($result, 2);
			$plano_id			= odbc_result($result, 3);
			$id					= odbc_result($result, 4);

			$lista_motorista_plano = str_replace("selected = 'selected'", "", $lista_motorista_plano);
			$lista_motorista_plano = str_replace("value='$plano_id'", "value='$plano_id' selected = 'selected'", $lista_motorista_plano);

			$observacao = utf8_encode($observacao);

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $nome_responsavel . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_inicio . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>

						<select name='plano$id' id='plano$id'
						      onchange='javascript: altera_plano(this,$id)'>
								$lista_motorista_plano
							 </select>

					</font></center></td>
				  </tr>";
		}

		$table .= "</tbody></table>";


		$retorno->plano = $table;

		return $retorno;
	}


	public function exibeValorMensal()
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];



		$query = "select MENSAL.id, plano.descricao plano, valor,
					case mes
						when 1 then 'Janeiro'
						when 2 then 'Fevereiro'
						when 3 then 'Marco'
						when 4 then 'Abril'
						when 5 then 'Maio'
						when 6 then 'Junho'
						when 7 then 'Julho'
						when 8 then 'Agosto'
						when 9 then 'Setembro'
						when 10 then 'Outubro'
						when 11 then 'Novembro'
						when 12 then 'Dezembro'
					end desc_mes,
					ano,
					mes,
					(select top 1 status_id
					 from conferencia_plano_agregado
					 where valor_mensal_plano_id = MENSAL.ID
					 and status_id not in ('i')
					 order by id desc) status_conferencia
					FROM PLANO_CELULAR PLANO WITH (NOLOCK)
					CROSS APPLY (SELECT TOP 12 *
									FROM VALOR_MENSAL_PLANO VMP WITH (NOLOCK)
									WHERE PLANO.ID = VMP.PLANO_ID
									AND VMP.STATUS_ID = 'A'
									ORDER BY ANO DESC, MES DESC) MENSAL
					WHERE PLANO.STATUS_ID = 'A'
					order by MENSAL.ANO DESC, MENSAL.MES DESC, MENSAL.id";
		//print "<pre>".$query."</pre>";
		$result = $con->executar($query);

		$retorno = "";

		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>PLANO</a></th>
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>VALOR</a></th>
						<th bgcolor='#d3d3d3' width='150' ><a href='javascript:void(0)'>MÊS</a></th>
						<th bgcolor='#d3d3d3' width='70' ><a href='javascript:void(0)'>ANO</a></th>
						<th bgcolor='#d3d3d3' width='80' ><a href='javascript:void(0)'>EXCLUIR</a></th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {

			$id					= odbc_result($result, 1);
			$plano				= odbc_result($result, 2);
			$valor				= odbc_result($result, 3);
			$mes				= odbc_result($result, 4);
			$ano				= odbc_result($result, 5);
			$status_conferencia	= odbc_result($result, 7);


			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $plano . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . number_format($valor, 2, ',', '.') . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $mes . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $ano . "</font></center></td>";

			if ($status_conferencia != 'f')
				$table .= "
					<td><center><font size='-1'><a href='javascript:exclui_valor_mensal($id);'><img src='../SCA/images/excluir1.jpg' width='30' heigth='30' style='border:none'></a></font></center></td>";
			else
				$table .= "
					<td><center><font size='-1'> - </font></center></td>";

			$table .= "</tr>";
		}

		$table .= "</tbody></table>";

		$retorno->valor = $table;

		return $retorno;
	}



	public function dadosAparelho($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		//VERIFICA SE TEM PERMISSAO DE ADMINISTRADOR
		$query = "select permissao.administrador
					from permissao_celular_covre permissao with (nolock)
					where permissao.usuario_id = (select top 1 id from usuario where usuario = '$logado' and status = 'a')
					and status_id = 'a'";
		//print $query;
		$result = $con->executar($query);
		$administrador	= odbc_result($result, 1);


		$query = "select COALESCE(FUNCIONARIO.nome, TERCEIRO.nome, ECARGO.nome),
					convert(varchar(10), data_inicio, 103) data_inicio,
					case when data_fim is not null
							then convert(varchar(10), data_fim, 103)
							else 'EM USO'
					end data_fim,
					COALESCE(FUNCIONARIO.COD_CENTRO_CUSTO, TERCEIRO.COD_CENTRO_CUSTO, ECARGO.COD_CENTRO_CUSTO, laf.cod_ccusto) COD_CENTRO_CUSTO,
					COALESCE(FUNCIONARIO.CENTRO_CUSTO, TERCEIRO.CENTRO_CUSTO, ECARGO.CENTRO_CUSTO, LAF_CCUSTO.NOME) centro_custo,
					convert(varchar(10), laf.data_gravacao, 103)+' '+convert(varchar(10), laf.data_gravacao, 108) data_gravacao,
					user_gravacao.nome user_gravacao,
					convert(varchar(10), laf.data_inativacao, 103)+' '+convert(varchar(10), laf.data_inativacao, 108) data_inat,
					user_inativacao.nome user_inativacao,
					laf.aparelho_id,
					laf.id
					from linha_aparelho_funcionario laf with (nolock)
					join aparelho_celular aparelho with (nolock) on
						aparelho.id = laf.aparelho_id
						/*and aparelho.status_id = 'a'*/
					join usuario user_gravacao with (nolock) on
						user_gravacao.id = laf.user_gravacao
					LEFT JOIN corpore..PCCUSTO LAF_CCUSTO (NOLOCK) ON  
						LAF_CCUSTO.CODCCUSTO = REPLACE(laf.cod_ccusto, '.567.569', '') COLLATE Latin1_General_CI_AS
					/* Funcionario RM */
					OUTER APPLY (
						SELECT 
							PFUNC.CODPESSOA AS CORP_PESSOA_ID,
							PFUNC.NOME COLLATE SQL_Latin1_General_CP1_CI_AI AS NOME,
							PCCUSTO.CODCCUSTO COLLATE Latin1_General_CI_AS + '.567.569' AS COD_CENTRO_CUSTO,
							PCCUSTO.NOME AS CENTRO_CUSTO
						FROM corpore..PFUNC WITH (NOLOCK)
						LEFT JOIN corpore..PFRATEIOFIXO WITH (NOLOCK) ON PFUNC.CHAPA = PFRATEIOFIXO.CHAPA
							AND PFRATEIOFIXO.CODCOLIGADA = 1
						LEFT JOIN corpore..PCCUSTO WITH (NOLOCK) ON PFRATEIOFIXO.CODCCUSTO = PCCUSTO.CODCCUSTO
							AND PCCUSTO.CODCOLIGADA = 1
						WHERE PFUNC.CODPESSOA = LAF.CORP_PESSOA_ID
							and PFUNC.CODCOLIGADA = 1	
							and PFUNC.CODSITUACAO <> 'D'
					) FUNCIONARIO

					/* Terceiro RM */
					OUTER APPLY (
						SELECT 
							PPESSOA.CODIGO AS CORP_PESSOA_ID,
							PPESSOA.NOME AS NOME,
							GCCUSTO.CODREDUZIDO AS COD_CENTRO_CUSTO,
							GCCUSTO.NOME AS CENTRO_CUSTO
						FROM corpore..PPESSOA WITH (NOLOCK)
						JOIN corpore..VPCOMPL WITH (NOLOCK) ON VPCOMPL.CODPESSOA = PPESSOA.CODIGO
							AND VPCOMPL.TIPO = 2
							AND VPCOMPL.DTDES IS NULL
						LEFT JOIN corpore..ILOCAL WITH (NOLOCK) ON ILOCAL.NOME = PPESSOA.NOME
							AND ILOCAL.CODCOLIGADA = 1
						LEFT JOIN corpore..GCCUSTO WITH (NOLOCK) 
							ON GCCUSTO.CODCCUSTO = ILOCAL.CODCCUSTO
							AND GCCUSTO.CODCOLIGADA = 1
						WHERE PPESSOA.CODIGO = LAF.CORP_PESSOA_ID
					) TERCEIRO

					/* Ecargo */
					OUTER APPLY(
						SELECT 
							pessoa.pessoa_id AS PESSOA_ID,
							COALESCE(pessoa.nome_fantasia, pessoa.nome) AS NOME,
							GCCUSTO.CODREDUZIDO AS COD_CENTRO_CUSTO,
							GCCUSTO.NOME AS CENTRO_CUSTO
						from CARGOSOL..pessoa with (nolock)
						left join corpore..GCCUSTO with (nolock) on
							GCCUSTO.CODREDUZIDO = laf.cod_ccusto COLLATE Latin1_General_CI_AS
						WHERE pessoa.pessoa_id = laf.pessoa_id
					) ECARGO
					
					left join usuario user_inativacao with (nolock) on
						user_inativacao.id = laf.user_inativacao
					where aparelho.id = $id
					and ( FUNCIONARIO.CORP_PESSOA_ID IS NOT NULL 
						OR TERCEIRO.CORP_PESSOA_ID IS NOT NULL 
						OR ECARGO.PESSOA_ID IS NOT NULL ) ";
		//print "<pre>".$query."</pre>";
		$result = $con->executar($query);

		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='250'><a href='javascript:void(0)'>RESPONSÁVEL</a></th>
						<th bgcolor='#d3d3d3' width='120' ><a href='javascript:void(0)'>DATA INÍCIO</a></th>
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>DATA FIM</a></th>
						<th bgcolor='#d3d3d3' width='100' ><a href='javascript:void(0)'>COD. CENTRO CUSTO</a></th>
						<th bgcolor='#d3d3d3' width='70' ><a href='javascript:void(0)'>CENTRO CUSTO</a></th>
						<th bgcolor='#d3d3d3' width='70' ><a href='javascript:void(0)'>DATA GRAVAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='150' ><a href='javascript:void(0)'>USUÁRIO GRAVAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='70' ><a href='javascript:void(0)'>DATA FINALIZAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='150' ><a href='javascript:void(0)'>USUÁRIO FINALIZAÇÃO</a></th>
					</thead><tbody>
				  ";

		while (odbc_fetch_row($result)) {

			$nome_responsavel		= odbc_result($result, 1);
			$data_inicio			= odbc_result($result, 2);
			$data_fim				= odbc_result($result, 3);
			$cod_centro_custo		= odbc_result($result, 4);
			$centro_custo			= utf8_encode(odbc_result($result, 5));
			$data_gravacao			= odbc_result($result, 6);
			$user_gravacao			= odbc_result($result, 7);
			$data_inativacao		= odbc_result($result, 8);
			$user_inativacao		= odbc_result($result, 9);
			$laf_id					= odbc_result($result, 11);


			$table .= "<tr>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $nome_responsavel . "</font></center></td>";

			if ($administrador == 'S')
				$table .= "
						<td bgcolor='#FFFFFF'><center><font size='-1'><input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data_inicio' id='data_inicio' value='$data_inicio' size='6' maxlength='10' onblur='javascript:altera_data(this,$laf_id,1)'></font></center></td>";
			else
				$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_inicio . "</font></center></td>";

			if (($data_fim != 'EM USO')	&& ($administrador == 'S'))
				$table .= "
						<td bgcolor='#FFFFFF'><center><font size='-1'><input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data_fim' id='data_fim' value='$data_fim' size='6' maxlength='10' onblur='javascript:altera_data(this,$laf_id,2)'></font></center></td>";
			else
				$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_fim . "</font></center></td>";

			$table .= " <td bgcolor='#FFFFFF'><center><font size='-1'>" . $cod_centro_custo . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $centro_custo . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_gravacao . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $user_gravacao . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_inativacao . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $user_inativacao . "</font></center></td>
					   </tr>";


			if ($cont_col == 6) {
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


	public function buscaAparelhos()
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select aparelho.id,
					marca.descricao+' '+modelo.descricao+' S/N: '+aparelho.serie aparelho
					from aparelho_celular aparelho with (nolock)
					join modelo_aparelho_celular modelo with (nolock) on
						modelo.id = aparelho.modelo_id
					join marca_celular marca with (nolock) on
						marca.id = modelo.marca_id
					join aparelho_disponivel_estoque aparelho_estoque with (nolock) on
						aparelho_estoque.aparelho_id = aparelho.id
					where aparelho.status_id = 'a'
					and aparelho_estoque.status_id = 'a'
					and aparelho_estoque.estoque_id in ((select top 1 estoque_id
														 from permissao_celular_covre with (nolock)
														 where usuario_id = (select top 1 id
																			 from usuario with (nolock)
																			 where usuario = '$logado'
																			 and status = 'a')
													    and permissao_celular_covre.status_id = 'a'
														))
					and aparelho.id not in (select aparelho_id
										   from linha_aparelho_funcionario with (nolock)
										   where linha_aparelho_funcionario.status_id = 'a'
										   and aparelho_id = aparelho.id)
					order by aparelho";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';
		$table = '<option></option>';

		while (odbc_fetch_row($result)) {

			if (odbc_result($result, 1) == $celular_id)
				$selected = "selected='selected'";
			else
				$selected = "";

			$table .= "<option value='" . odbc_result($result, 1) . "'$selected>" . utf8_encode(odbc_result($result, 2)) . "</option>";
		}

		$retorno->aparelhos = $table;

		return $retorno;
	}


	public function buscaConferencia($mes, $ano)
	{

		$con = new ConexaoSCI;


		$logado = $_SESSION['usuario_logado'];

		$query = "select permissao.administrador, permissao.gerar_vale_id
					from permissao_celular_covre permissao with (nolock)
					where permissao.usuario_id = (select top 1 id from usuario where usuario = '$logado' and status = 'a')
					and status_id = 'a'";
		//print $query;
		$result = $con->executar($query);
		$administrador	= odbc_result($result, 1);
		$gerar_vale_id	= odbc_result($result, 2);



		$query = "select distinct
					pessoa.nome motorista,
					'('+substring(linha.num_celular,1,2)+')'+substring(linha.num_celular,3,5)+'-'+
					substring(linha.num_celular,8,4) celular,
					plano.descricao plano,
					conferencia.valor_cobrado,
					vf.placa placa,
					conferencia.boletim_servico_id,
					proprietario.nome proprietario,
					conferencia.id,
					conferencia.dia_cobrado_mes,

					case when ((YEAR(data_inicio) = $ano) and (MONTH(data_inicio) = $mes))
							then convert(varchar(10), data_inicio, 103)
							else convert(varchar(10), CAST(cast(conferencia.ano as varchar)+'-'+cast(conferencia.mes as varchar)+'-01' AS DATE), 103)
					end inicio_periodo,

					case when laf.data_fim is not null
							then convert(varchar(10), laf.data_fim, 103)
							else convert(varchar(10), cast(DAY(CORPORE.dbo.ULTIMODIAMES($mes,$ano)) as varchar)+'/'+
								 case when conferencia.mes < 10
										then '0'+cast(conferencia.mes as varchar)
										else cast(conferencia.mes as varchar)
								 end+'/'+cast(conferencia.ano as varchar), 103)
					end fim_periodo,
					vmp.valor,
					conferencia.status_id,
					cast(conferencia.mes as varchar)+'/'+cast(conferencia.ano as varchar)
					from conferencia_plano_agregado conferencia with (nolock)
					join valor_mensal_plano vmp with (nolock) on
						vmp.id = conferencia.valor_mensal_plano_id
						and vmp.status_id = 'a'
					join cargosol..pessoa with (nolock) on
						pessoa.pessoa_id = conferencia.Pessoa_Id
					join linha_aparelho_funcionario laf with (nolock) on
						laf.id = conferencia.linha_aparelho_id
					join celular_covre linha with (nolock) on
						linha.id = laf.celular_id
					join plano_celular plano with (nolock) on
						plano.id = conferencia.plano_id
					left join cargosol..veiculo_fornecedor vf with (nolock) on
					/*((vf.Fornecedor_Id = laf.pessoa_id) or
						vf.Fornecedor_Id = (select Fornecedor_Id
											from cargosol..veiculo_fornecedor with (nolock)
											where Motorista_Id = laf.pessoa_id
											and Tab_Status_Id = 1))*/
						vf.Veiculo_Fornecedor_Id = (select top 1 Veiculo_Fornecedor_Id from cargosol..Boletim_Servico where Boletim_Servico_Id = conferencia.boletim_servico_id and Tab_Status_Id = 1)
						and vf.Tab_Status_Id <> 2
						AND (VF.TAB_STATUS_VEICULO_ID IN (1,2,3))
						and vf.tab_tipo_veiculo_id not in (187,188,189,198,234,237)
					left join cargosol..pessoa proprietario with (nolock) on
						proprietario.pessoa_id = vf.Fornecedor_Id
					left join cargosol..tab_tipo_veiculo ttv with (nolock) on
						ttv.tab_tipo_veiculo_id = vf.tab_tipo_veiculo_id
						and ttv.desc_tipo_veiculo not like '%SEMI REBOQUE%'
					where conferencia.status_id not in ('i')
					and conferencia.mes = $mes
					and conferencia.ano = $ano
					order by motorista";
		//print "<pre>".$query."</pre>";
		$result = $con->executar($query);

		$table = "
				<table class='table  table-hover' id='myTable3' style='border: 1px solid black'>
					<tr>
						<td bgcolor='#d3d3d3' width='250' style='border: 1px solid black'>MOTORISTA</td>
						<td bgcolor='#d3d3d3' width='120' style='border: 1px solid black'>NÚMERO CELULAR</td>
						<td bgcolor='#d3d3d3' width='100' style='border: 1px solid black'>PLANO</td>
						<td bgcolor='#d3d3d3' width='200' style='border: 1px solid black'>PERÍODO</td>
						<td bgcolor='#d3d3d3' width='70'  style='border: 1px solid black'>VALOR MENSAL</td>
						<td bgcolor='#d3d3d3' width='70'  style='border: 1px solid black'>VALOR COBRADO</td>
						<td bgcolor='#d3d3d3' width='50' style='border: 1px solid black'>DIAS COBRADOS</td>
						<td bgcolor='#d3d3d3' width='100' style='border: 1px solid black'>PLACA</td>
						<td bgcolor='#d3d3d3' width='70'  style='border: 1px solid black'>BOLETIM</td>
						<td bgcolor='#d3d3d3' width='150' style='border: 1px solid black'>PROPRIETÁRIO</td>
						<td bgcolor='#d3d3d3' width='50'  style='border: 1px solid black'>EXCLUIR</td>
					</tr>
				  ";

		while (odbc_fetch_row($result)) {

			$motorista				= odbc_result($result, 1);
			$numero_celular			= odbc_result($result, 2);
			$plano					= odbc_result($result, 3);
			$valor_cobrado			= odbc_result($result, 4);
			$placa					= odbc_result($result, 5);
			$boletim				= odbc_result($result, 6);
			$proprietario			= odbc_result($result, 7);
			$conferencia_id			= odbc_result($result, 8);
			$dias_cobrados			= odbc_result($result, 9);
			$inicio_periodo			= odbc_result($result, 10);
			$fim_periodo			= odbc_result($result, 11);
			$valor_mensal			= odbc_result($result, 12);
			$status_id				= odbc_result($result, 13);
			$periodo_conferencia	= odbc_result($result, 14);


			if ($placa == '')
				$placa = "<font color='#FF0000'>SEM PLACA</font>";

			if ($boletim == '')
				$boletim = "<font color='#FF0000'>SEM BOLETIM</font>";


			$valor_cobrado = number_format($valor_cobrado, 2, ',', '.');
			$valor_mensal = number_format($valor_mensal, 2, ',', '.');

			if (($status_id == 'a') && ($administrador == 'S')) {
				$disabled_confere = '';
				$disabled_gera = 'disabled';
			} else
				if (($status_id == 'r') && ($gerar_vale_id == 'S')) {
				$disabled_confere = 'disabled';
				$disabled_gera = '';
			} else {
				$disabled_confere = 'disabled';
				$disabled_gera = 'disabled';
			}


			$table .= "<tr>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $motorista . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $numero_celular . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $plano . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $inicio_periodo . " a " . $fim_periodo . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $valor_mensal . "</font></center></td>";

			//if ($status_id == 'f')
			$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'>" . $valor_cobrado . "</font></center></td>";
			/*
			else
				$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'><input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='valor$conferencia_id' id='valor$conferencia_id' value='$valor_cobrado' size='6' maxlength='10' onblur='javascript:altera_dado(this,$conferencia_id,1)'></font></center></td>";
			*/
			$table .= "
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $dias_cobrados . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $placa . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $boletim . "</font></center></td>
						<td bgcolor='#FFFFFF'><center><font size='-1'>" . $proprietario . "</font></center></td>";
			/*
						<td bgcolor='#FFFFFF'><center><font size='-1'><input type='text' class='form-control txt-center altura-campo only-number' aria-describedby='basic-addon1' name='boletim$conferencia_id' id='boletim$conferencia_id' value='$boletim' size='6' maxlength='10' onblur='javascript:altera_dado(this,$conferencia_id,2)'></font></center></td>
						*/
			if ($status_id == 'f')
				$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'> - </font></center></td>";
			else
				$table .= "
						<td><center><font size='-1'><a href='javascript:exclui_conf_mensal_mot($conferencia_id," . chr(34) . $periodo_conferencia . chr(34) . ");'><img src='../SCA/images/excluir1.jpg' width='30' heigth='30' style='border:none'></a></font></center></td>
					   </tr>";
		} //fim while

		$table .= "</table>";

		$table .= "<input type='button' class='btn-sm btn-primary' value='Conferido' id='conferir' name='conferir' onClick='javascript:conferir_valores()' $disabled_confere>
                   <input type='button' class='btn-sm btn-primary' value='Recalcular Valores' id='recalcular' name='recalcular' onClick='javascript:recalcula_valores()' $disabled_gera>
                   <input type='button' class='btn-sm btn-primary' value='Gerar Vale no Boletim' id='gera_vale' name='gera_vale' onClick='javascript:gera_vale_boletim()' $disabled_gera>";

		$retorno->relatorio = $table;

		return $retorno;
	}
}
