<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";
require_once "Dados.php";
require("../../SCA/includes/phpmailer/class.phpmailer.php");
require_once "../../SCI/includes/page_func_sci.php";

class GravaDados extends ConexaoSCI{


	public function gravaDadosCelular($id, $num_celular, $operadora_id, $imei, $observacao, $status_id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '')
		{

			$observacao = utf8_decode($observacao);

			if ($id == '')
			{

				//VERIFICA SE O NUMERO DE CELULAR JÁ ESTÁ CADASTRADO
				$query = "select id
						  from celular_covre
						  WHERE num_celular = replace(replace(replace('$num_celular','(',''),')',''),'-','')";
				//print $query;
				$result = $con->executar($query);
				$id_encontrado	= odbc_result($result,1);

				if ($id_encontrado != '')
					$ret_gravacao = "Número de telefone já cadastrado";
				else
				{
					$query = "insert into celular_covre (num_celular, operadora_id, imei, observacao, data_gravacao,
							  user_gravacao, status_id)
							  values (replace(replace(replace('$num_celular','(',''),')',''),'-',''), $operadora_id,
							  case when '$imei' = '' then null else '$imei' end,
							  case when '$observacao' = '' then null else '$observacao' end,
							  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), '$status_id')";
					//print $query;
					$con->executar($query);
					$query = "SELECT @@IDENTITY AS Ident";
					$result = $con->executar($query);
					$linha_id = odbc_result($result, 1);

					//INSERE O APARELHO NO ESTOQUE
					$query = "insert into linha_disponivel_estoque (linha_id, estoque_id, data_hora_gravacao, user_gravacao,
							  data_entrada, user_entrada, estoque_antigo_id, aceitar_negar, status_id)
							  values ($linha_id, (select top 1 estoque_id
														  from permissao_celular_covre with (nolock)
														  where usuario_id = (select top 1 id
																			  from usuario with (nolock)
																			  where usuario = '$logado'
																			  and status = 'a')
												  and permissao_celular_covre.status_id = 'a'),
							  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'),
							  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), NULL,'A','a')";
					//print "<pre>".$query."</pre>";
					$con->executar($query);



					$ret_gravacao = "ok";
				}
			}
			else
			{
				$query = "update celular_covre
						  set operadora_id = '$operadora_id',
						  imei = case when '$imei' = '' then null else '$imei' end,
						  observacao = case when '$observacao' = '' then null else '$observacao' end,
						  status_id = '$status_id'
						  where id = $id";
				//print $query;
				$con->executar($query);


				$ret_gravacao =  "ok";

			}

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}




	public function gravaCelular($id, $pessoa_id, $celular_covre_id, $aparelho_id, $data_inicio, $data_final, $cod_ccusto, $observacao, $corp_pessoa_id, $id_antigo = '', $data_final_antigo = ''){

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		$observacao = utf8_decode($observacao);

		if ($logado != '')
		{

			//VERIFICA SE TEM A DATA FINAL E COMPARA COM A DATA INICIAL
			$query = "SELECT CASE WHEN '$data_final' < '$data_inicio'
									THEN 'MENOR'
									ELSE 'MAIOR'
						     END";
			//print "<pre>".$query."</pre>";
			$result = $con->executar($query);
			$compara_datas	= odbc_result($result,1);


			$query = "select
						case when (('$data_inicio' > CONVERT(VARCHAR(10), GetDate(), 120)) or
									('$data_final' > CONVERT(VARCHAR(10), GetDate(), 120)))
								then 'maior'
								else null
						end";
			//print "<pre>".$query."</pre>";
			$result = $con->executar($query);
			$compara_data_atual = odbc_result($result,1);

			$query = "select id
						from linha_aparelho_funcionario laf with (nolock)
						where ((celular_id = '$celular_covre_id') OR
							   (aparelho_id = '$aparelho_id'))
						and data_fim is not null
						and laf.id not in ('$id', '$id_antigo')
						and (('$data_inicio' between data_inicio and dateadd(dd,-1,data_fim)) or
							 ('$data_final' between data_inicio and dateadd(dd,-1,data_fim)) or
							 ('$data_inicio' < data_inicio))";
			//print "<pre>".$query."</pre>";
			$result = $con->executar($query);
			$compara_uso = odbc_result($result,1);


			if (($data_final != '') && ($compara_datas == 'MENOR'))
				$ret_gravacao = "Data Final menor que Data Inicial";
			else
			if ($compara_data_atual == 'maior')
				$ret_gravacao = 'Data informada maior que data atual';
			else
			if ($compara_uso != '')
				$ret_gravacao = 'Data já utilizada com a linha / aparelho, ou data menor de uso anterior';
			else
			{
				$relatorio_permissao = $dados->verificaPermissaoCC($cod_ccusto);
				$permissao = $relatorio_permissao->permissao;

				//$relatorio_cc_valido = $dados->verificaCcValido($cod_ccusto);
				//$cc_valido = $relatorio_cc_valido->cc_valido;


				if ($permissao == '')
					$ret_gravacao = "Você não tem permissão para gravar este Centro de Custo";
				/*else
				if ($cc_valido == '')
					$ret_gravacao = "Código do Centro de Custo inválido";*/
				else
				if ($id == '')
				{
					if (($id_antigo != '') && ($data_final_antigo != ''))
					{
						$query = "update linha_aparelho_funcionario
								  set data_fim = '$data_final_antigo',
								  status_id = 'i',
								  data_ultima_alteracao = getdate(),
								  user_ultima_alteracao = (select id from usuario where usuario = '$logado' and status = 'a'),
								  data_inativacao = getdate(),
								  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a')
								  where id = $id_antigo";
						$con->executar($query);
					}

					$query = "insert into linha_aparelho_funcionario (corp_pessoa_id, pessoa_id, celular_id, aparelho_id, data_inicio,
							  data_fim, cod_ccusto, plano_id, observacao, data_gravacao, user_gravacao, status_id,
							  data_inativacao, user_inativacao) values (
							  case when '$corp_pessoa_id' = '' then null else '$corp_pessoa_id' end,
							  case when '$pessoa_id' = '' then null else '$pessoa_id' end,
							  $celular_covre_id,
							  case when '$aparelho_id' = '' then null else '$aparelho_id' end, '$data_inicio',
							  case when '$data_final' = '' then null else '$data_final' end,
							  case when '$cod_ccusto' = '' then null else '$cod_ccusto' end, 2,
							  case when '$observacao' = '' then null else '$observacao' end,
							  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'),
							  case when '$data_final' = '' then 'a' else 'i' end,
							  case when '$data_final' = '' then null else getdate() end,
							  case when '$data_final' = '' then null else (select id
																			from usuario
																			where usuario = '$logado'
																			and status = 'a') end)";
					//print $query;
					$con->executar($query);
					$query = "SELECT @@IDENTITY AS Ident";
					$result = $con->executar($query);
					$id = odbc_result($result, 1);


					//TIRA A LINHA DO ESTOQUE ATUAL
					$query = "update linha_disponivel_estoque
							  set data_hora_gravacao = getdate(),
							  user_gravacao = (select id from usuario where usuario = '$logado' and status = 'a'),
							  estoque_destino_id = estoque_id,
							  data_saida = getdate(),
							  user_saida = (select id from usuario where usuario = '$logado' and status = 'a'),
							  status_id = 'f'
							  where linha_id = $celular_covre_id
							  and status_id = 'a'";
					//print "<pre>".$query."</pre>";
					$con->executar($query);

					if ($aparelho_id != '')
					{
						//TIRA O APARELHO DO ESTOQUE ATUAL
						$query = "update aparelho_disponivel_estoque
								  set data_hora_gravacao = getdate(),
								  user_gravacao = (select id from usuario where usuario = '$logado' and status = 'a'),
								  estoque_destino_id = estoque_id,
								  data_saida = getdate(),
								  user_saida = (select id from usuario where usuario = '$logado' and status = 'a'),
								  status_id = 'f'
								  where aparelho_id = $aparelho_id
								  and status_id = 'a'";
						//print "<pre>".$query."</pre>";
						$con->executar($query);
					}

					$ret_gravacao = "ok";
				}
				else
				{
					$query = "update linha_aparelho_funcionario
							  set data_inicio = '$data_inicio',
							  data_fim = case when '$data_final' = '' then null else '$data_final' end,
							  cod_ccusto = case when '$cod_ccusto' = '' then null else '$cod_ccusto' end,
							  observacao = case when '$observacao' = '' then null else '$observacao' end,
							  status_id = case when '$data_final' = '' then 'a' else 'i' end,
							  data_ultima_alteracao = getdate(),
							  user_ultima_alteracao = (select id from usuario where usuario = '$logado' and status = 'a'),							 							  data_inativacao = case when '$data_final' = '' then null else getdate() end,
							  user_inativacao = case when '$data_final' = '' then null else (select id
																							 from usuario
																							 where usuario = '$logado' and status = 'a') end
							  where id = $id";
					//print $query;
					$con->executar($query);

					$ret_gravacao =  "ok";

				}

				//VOLTA A LINHA E O APARELHO PARA O ESTOQUE DO USUARIO QUE EFETUOU A OPERACAO
				if (($id != '') && ($data_final != '') && ($ret_gravacao == 'ok'))
				{

					//INSERE O APARELHO NO ESTOQUE
					$query = "insert into linha_disponivel_estoque (linha_id, estoque_id, data_hora_gravacao, user_gravacao,
							  data_entrada, user_entrada, estoque_antigo_id, aceitar_negar, status_id)
							  values ($celular_covre_id, (select top 1 estoque_id
														  from permissao_celular_covre with (nolock)
														  where usuario_id = (select top 1 id
																			  from usuario with (nolock)
																			  where usuario = '$logado'
																			  and status = 'a')
												  and permissao_celular_covre.status_id = 'a'),
							  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'),
							  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), NULL,'A','a')";
					//print "<pre>".$query."</pre>";
					$con->executar($query);

					if ($aparelho_id != '')
					{
						//INSERE O APARELHO NO NOVO ESTOQUE
						$query = "insert into aparelho_disponivel_estoque (aparelho_id, estoque_id, data_hora_gravacao,
								  user_gravacao, data_entrada, user_entrada, estoque_antigo_id, aceitar_negar, status_id)
								  values ($aparelho_id, (select top 1 estoque_id
														  from permissao_celular_covre with (nolock)
														  where usuario_id = (select top 1 id
																			  from usuario with (nolock)
																			  where usuario = '$logado'
																			  and status = 'a')
														  and permissao_celular_covre.status_id = 'a'),
								  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'),
								  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'),
								  NULL,'A','a')";
						//print $query;
						$con->executar($query);

					}

				}

			}

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}



	public function excluiCelular($id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		$observacao = utf8_decode($observacao);

		if ($logado != '')
		{

			$query = "update celular_funcionario
					  set status_id = 'i', data_inativacao = getdate(),
					  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a')
					  where id = $id";
			//print $query;
			$con->executar($query);


			$ret_gravacao =  "ok";
		}
		else
			$ret_gravacao = "sessao_expirada";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function gravaParametro($valor){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '')
		{

			$query = "update parametro_celular_agregado
					  set valor = replace(replace('$valor','.',''),',','.')
					  where id = 1";
			//print $query;
			$con->executar($query);


			$ret_gravacao =  "ok";
		}
		else
			$ret_gravacao = "sessao_expirada";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}



	public function gravaDespesaExtra($id, $celular_covre_id, $data_despesa, $valor, $observacao){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		$observacao = utf8_decode($observacao);

		if ($logado != '')
		{

			if ($id == '')
			{
				$query = "insert into despesa_extra_celular (celular_covre_id, data, valor, observacao, data_gravacao, user_gravacao, status_id)
						  values ($celular_covre_id, '$data_despesa', replace(replace('$valor','.',''),',','.'),
						  case when '$observacao' = '' then null else '$observacao' end,
						  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'),'a')";
				//print $query;
				$con->executar($query);
			}
			else
			{
				$query = "update despesa_extra_celular
						  set data = '$data_despesa',
						  valor = replace(replace('$valor','.',''),',','.'),
						  observacao = case when '$observacao' = '' then null else '$observacao' end
						  where id = $id";
				//print $query;
				$con->executar($query);
			}

			$ret_gravacao =  "ok";
		}
		else
			$ret_gravacao = "sessao_expirada";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function excluiDespesaExtra($id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "UPDATE DESPESA_EXTRA_CELULAR
				  SET STATUS_ID = 'i',
				  DATA_INATIVACAO = GETDATE(),
				  USER_INATIVACAO = (SELECT TOP 1 ID FROM USUARIO WHERE USUARIO = '$logado' AND STATUS = 'a')
				  WHERE ID = $id";
		//print $query;
		$result = $con->executar($query);

	}


	public function excluiCCResponsavel($id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "UPDATE ccusto_responsavel_celular
				  SET STATUS_ID = 'i'
				  WHERE ID = $id";
		//print $query;
		$result = $con->executar($query);

	}


	public function gravaResponsavel($responsavel_id, $usuario_id, $estoque_id, $adm_id, $gerar_vale_id, $status_id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '')
		{

			if ($responsavel_id == '')
			{

				//VERIFICA SE O USUARIO JÁ ESTÁ CADASTRADO
				$query = "SELECT id
						  FROM permissao_celular_covre
						  WHERE usuario_id = $usuario_id";
				//print $query;
				$result = $con->executar($query);
				$user_cadastrado	= odbc_result($result,1);

				if ($user_cadastrado != '')
					$ret_gravacao = "Usuário já cadastrado";
				else
				{
					$query = "insert into permissao_celular_covre (usuario_id, administrador, status_id, estoque_id,
							  gerar_vale_id) values ($usuario_id, '$adm_id', '$status_id',
							  case when '$estoque_id' = '' then null else '$estoque_id' end,
							  case when '$gerar_vale_id' = '' then null else '$gerar_vale_id' end)";
					//print $query;
					$con->executar($query);

					$query = "SELECT @@IDENTITY AS Ident";
					$result = $con->executar($query);
					$responsavel_id = odbc_result($result, 1);

					$ret_gravacao = '1|ok|'.$responsavel_id;
				}

			}
			else
			{
				$query = "update permissao_celular_covre
						  set usuario_id = '$usuario_id',
						  administrador = '$adm_id',
						  status_id = '$status_id',
						  estoque_id = case when '$estoque_id' = '' then null else '$estoque_id' end,
						  gerar_vale_id = case when '$gerar_vale_id' = '' then null else '$gerar_vale_id' end
						  where id = $responsavel_id";
				//print $query;
				$con->executar($query);


				$ret_gravacao = '1|ok|'.$responsavel_id;

			}

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function gravaCentroCusto($responsavel_id,$centro_custo_id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '')
		{

			//VERIFICA SE O CENTRO DE CUSTO JÁ ESTÁ VINCULADO AO USUARIO
			$query = "select id
						from ccusto_responsavel_celular
						WHERE responsavel_id = $responsavel_id
						and centro_custo_id = $centro_custo_id
						and status_id = 'a'";
			//print $query;
			$result = $con->executar($query);
			$ccusto_cadastrado	= odbc_result($result,1);


			if ($ccusto_cadastrado != '')
				$ret_gravacao = "Centro de Custo já cadastrado para este funcionário";
			else
			{
				$query = "insert into ccusto_responsavel_celular (responsavel_id, centro_custo_id, status_id)
						  values ($responsavel_id, '$centro_custo_id', 'a')";
				//print $query;
				$con->executar($query);

				$ret_gravacao = 'ok';
			}
		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function gravaModeloAparelho($id, $marca_id, $modelo, $status_id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '')
		{

			$modelo = utf8_decode($modelo);


			//VERIFICA SE O MODELO JÁ ESTÁ CADASTRADO
			$query = "select id
					  from modelo_aparelho_celular with (nolock)
					  WHERE descricao = '$modelo'
					  and marca_id = $marca_id";
			//print $query;
			$result = $con->executar($query);
			$id_encontrado	= odbc_result($result,1);


			if ($id == '')
			{


				if ($id_encontrado != '')
					$ret_gravacao = "Modelo já cadastrado";
				else
				{
					$query = "insert into modelo_aparelho_celular (marca_id,descricao,data_hora_gravacao,user_gravacao,status_id)
							  values ($marca_id, '$modelo', getdate(),
							  (select id from usuario where usuario = '$logado' and status = 'a'), '$status_id')";
					//print $query;
					$con->executar($query);

					$ret_gravacao = "ok";
				}
			}
			else
			{
				if (($id_encontrado != '') && ($id_encontrado != $id))
					$ret_gravacao = "Modelo já cadastrado";
				else
				{
				$query = "update modelo_aparelho_celular
						  set marca_id = '$marca_id',
						  descricao = '$modelo',
						  status_id = '$status_id'
						  where id = $id";
				//print $query;
				$con->executar($query);

				$ret_gravacao =  "ok";
				}

			}

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function gravaDadosAparelho($id, $modelo_id, $serie, $imei1, $imei2, $observacao, $status_id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '')
		{

			$observacao = utf8_decode($observacao);

			//VERIFICA SE OS DADOS DO APARELHO JÁ FORAM CADASTRADOS
			$query = "select id, serie, imei1, imei2
						from aparelho_celular aparelho with (nolock)
						where 1 = 1
						and ((serie = '$serie') or
						(imei1 = '$imei1') or (imei1 = '$imei2') or
						(imei2 = '$imei1') or (imei2 = '$imei2'))
						and id <> '$id'";
			//print $query;
			$result = $con->executar($query);
			$id_encontrado		= odbc_result($result,1);
			$serie_duplicada	= odbc_result($result,2);
			$imei1_duplicado	= odbc_result($result,3);
			$imei2_duplicado	= odbc_result($result,4);


			if ($id == '')
			{

				if (($serie_duplicada == $serie) && ($id_encontrado != ''))
					$ret_gravacao = "Número de Série já cadastrado";
				else
				if (($imei1_duplicado == $imei1) && ($id_encontrado != ''))
					$ret_gravacao = "IMEI 1 já cadastrado";
				else
				if (($imei1_duplicado == $imei2) && ($id_encontrado != ''))
					$ret_gravacao = "IMEI 2 já cadastrado";
				else
				if (($imei2_duplicado == $imei1) && ($id_encontrado != ''))
					$ret_gravacao = "IMEI 1 já cadastrado";
				else
				if (($imei2_duplicado == $imei2) && ($id_encontrado != ''))
					$ret_gravacao = "IMEI 2 já cadastrado";
				else
				{
					$query = "insert into aparelho_celular (modelo_id, serie, imei1, imei2, observacao, data_hora_gravacao,
							  user_gravacao, status_id)
							  values ($modelo_id, '$serie',
							  case when '$imei1' = '' then null else '$imei1' end,
							  case when '$imei2' = '' then null else '$imei2' end,
							  case when '$observacao' = '' then null else '$observacao' end,
							  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), '$status_id')";
					//print $query;
					$con->executar($query);

					$query = "SELECT @@IDENTITY AS Ident";
					$result = $con->executar($query);
					$aparelho_id = odbc_result($result, 1);

					//INSERE O APARELHO NO ESTOQUE
					$query = "insert into aparelho_disponivel_estoque (aparelho_id, estoque_id, data_hora_gravacao,
							  user_gravacao, data_entrada, user_entrada, estoque_antigo_id, aceitar_negar, status_id)
							  values ($aparelho_id, (select top 1 estoque_id
														  from permissao_celular_covre with (nolock)
														  where usuario_id = (select top 1 id
																			  from usuario with (nolock)
																			  where usuario = '$logado'
																			  and status = 'a')
												  and permissao_celular_covre.status_id = 'a'),
							  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'),
							  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), NULL,'A','a')";
					//print "<pre>".$query."</pre>";
					$con->executar($query);

					$ret_gravacao = "ok";
				}
			}
			else
			{
				if (($serie_duplicada == $serie) && ($id != $id_encontrado) && ($id_encontrado != ''))
					$ret_gravacao = "Número de Série já cadastrado";
				else
				if (($imei1_duplicado == $imei1) && ($id != $id_encontrado) && ($id_encontrado != ''))
					$ret_gravacao = "IMEI 1 já cadastrado";
				else
				if (($imei1_duplicado == $imei2) && ($id != $id_encontrado) && ($id_encontrado != ''))
					$ret_gravacao = "IMEI 2 já cadastrado";
				else
				if (($imei2_duplicado == $imei1) && ($id != $id_encontrado) && ($id_encontrado != ''))
					$ret_gravacao = "IMEI 1 já cadastrado";
				else
				if (($imei2_duplicado == $imei2) && ($id != $id_encontrado) && ($id_encontrado != ''))
					$ret_gravacao = "IMEI 2 já cadastrado";
				else
				{
					$query = "update aparelho_celular
							  set modelo_id = '$modelo_id',
							  serie = '$serie',
							  imei1 = case when '$imei1' = '' then null else '$imei1' end,
							  imei2 = case when '$imei2' = '' then null else '$imei2' end,
							  observacao = case when '$observacao' = '' then null else '$observacao' end,
							  status_id = '$status_id'
							  where id = $id";
					//print $query;
					$con->executar($query);


					$ret_gravacao =  "ok";
				}

			}

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}



	public function alteraPlano($plano_id, $id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '')
		{

			$query = "update linha_aparelho_funcionario
					  set plano_id = $plano_id
					  where id = $id";
			//print $query;
			$con->executar($query);

			$ret_gravacao =  "ok";

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function gravaValorMensalAgregado($plano_id, $mes, $ano, $acao){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		//verifica se os dados ja foram digitados anteriormente
		$query = "SELECT DISTINCT *
					FROM (SELECT distinct
							dados.Pessoa_Id,
							dados.plano_id,
							mes,
							ano,
							case when dados.dias = ultimo_dia_mes
									then cast(valor_plano as numeric(15,2))
									else cast(((valor_plano * dados.dias) / dados.ultimo_dia_mes) as numeric(15,2))
							end valor_cobrado,
							linha_aparelho_id,
							vmp_id,
							dados.dias,
							Boletim_Servico_Id,
							fornecedor_id,
							valor_mensal
							FROM (select distinct
									vmp.valor valor_plano,
									BS.Boletim_Servico_Id,
									case when laf.data_fim is not null
											then case when  cast(laf.data_inicio as date) > CAST(CAST($ano AS VARCHAR)+'-'+CAST($mes AS VARCHAR)+'-01' AS DATE)
														then day(laf.data_fim) - day(laf.data_inicio)
														else DATEDIFF(dd, CAST(CAST($ano AS VARCHAR)+'-'+CAST($mes AS VARCHAR)+'-01' AS DATE), CAST(laf.data_fim AS DATE))
												 end
											else case when ((laf.data_fim is null) and ( cast(laf.data_inicio as date) > CAST(CAST($ano AS VARCHAR)+'-'+CAST($mes AS VARCHAR)+'-01' AS DATE)))
													THEN DATEDIFF(dd, laf.data_inicio, corpore.dbo.ULTIMODIAMES($mes,$ano))
													ELSE DAY(corpore.dbo.ULTIMODIAMES($mes,$ano))
													end
									end dias,
									DAY(corpore.dbo.ULTIMODIAMES($mes,$ano)) ultimo_dia_mes,
									laf.data_inicio,
									laf.data_fim,
									vf.placa,
									pessoa.Pessoa_Id,
									plano.id plano_id,
									mes,
									ano,
									laf.id linha_aparelho_id,
									vmp.id vmp_id,
									bs.fornecedor_id,
									vmp.valor valor_mensal
									from linha_aparelho_funcionario laf with (nolock)
									join cargosol..colaborador co with (nolock) on
										co.pessoa_id = laf.pessoa_id
									join cargosol..pessoa with (nolock) on
										pessoa.pessoa_id = co.Pessoa_Id
									join celular_covre linha with (nolock) on
										linha.id = laf.celular_id
									join plano_celular plano with (nolock) on
										plano.id = laf.plano_id
									join valor_mensal_plano vmp with (nolock) on
										vmp.plano_id = laf.plano_id
									left join cargosol..veiculo_fornecedor vf with (nolock) on
										((vf.Fornecedor_Id = laf.pessoa_id) or
										  vf.Fornecedor_Id = (select Fornecedor_Id
															  from cargosol..veiculo_fornecedor with (nolock)
															  where Motorista_Id = laf.pessoa_id
															  and Tab_Status_Id = 1))
										and vf.Tab_Status_Id <> 2
									left join CARGOSOL..BOLETIM_SERVICO BS with (nolock) on
										((bs.Fornecedor_Id = laf.pessoa_id) or
										  bs.Fornecedor_Id = (select Fornecedor_Id
															  from cargosol..veiculo_fornecedor with (nolock)
															  where Motorista_Id = laf.pessoa_id
															  and Tab_Status_Id = 1))
										and bs.Tab_Status_Id <> 2
										and bs.Data_Fechamento is null
										and bs.Boletim_Servico_Id in (select top 1 boletim_servico_id
																		from CARGOSOL..boletim_servico with (nolock)
																		where ((Fornecedor_Id = laf.pessoa_id) or
																				Fornecedor_Id = (select Fornecedor_Id
																								 from cargosol..veiculo_fornecedor with (nolock)
																								 where Motorista_Id = laf.pessoa_id
																								 and Tab_Status_Id = 1))
																		and Tab_Status_Id <> 2
																		and Data_Fechamento is null
																		and Veiculo_Fornecedor_Id is not null
																		order by data_emissao,boletim_servico_id)
									where co.tab_status_id <> 2
									and co.tab_tipo_vinculo_id = 2
									/*and laf.status_id = 'a'*/
									and vmp.ano = $ano
									and vmp.mes = $mes
									and plano.id = $plano_id
									and vmp.status_id = 'a'
									and pessoa.pessoa_id not in (select top 1 conferencia.pessoa_id
																 from conferencia_plano_agregado conferencia with (nolock)
																 where status_id = 'f'
																 and conferencia.pessoa_id = laf.pessoa_id
																 and conferencia.linha_aparelho_id = laf.id
																 and conferencia.pagto_fim_antecipado = 'S'
																 order by ano desc, mes desc)
								)DADOS
						)DADOS1
						WHERE 1 = 1
						and valor_cobrado > 0";
		//print $query;
		$result = $con->executar($query);
		while(odbc_fetch_row($result))
		{

			$pessoa_id				= odbc_result($result,1);
			$plano_id				= odbc_result($result,2);
			$mes					= odbc_result($result,3);
			$ano					= odbc_result($result,4);
			$valor_cobrado			= odbc_result($result,5);
			$linha_aparelho_id		= odbc_result($result,6);
			$valor_mensal_plano_id	= odbc_result($result,7);
			$dia_cobrado_mes		= odbc_result($result,8);
			$boletim_servico_id		= odbc_result($result,9);
			$fornecedor_id			= odbc_result($result,10);
			$valor_mensal			= odbc_result($result,11);

			if ($acao == 1)
				$status_id = 'a';
			else
				if ($acao == 2)
					$status_id = 'r';

			if ($valor_cobrado > $valor_mensal)
				$pagto_fim_antecipado = 'S';
			else
				$pagto_fim_antecipado = '';


			$query1 = "insert into conferencia_plano_agregado (pessoa_id, plano_id, mes, ano, valor_cobrado,
					  boletim_servico_id, linha_aparelho_id, valor_mensal_plano_id, dia_cobrado_mes, fornecedor_id,
					  pagto_fim_antecipado, data_hora_gravacao, user_gravacao, status_id)
					  values ($pessoa_id, $plano_id, $mes, $ano, '$valor_cobrado',
					  case when '$boletim_servico_id' = '' then NULL else '$boletim_servico_id' end,
					  $linha_aparelho_id, $valor_mensal_plano_id, $dia_cobrado_mes,
					  case when '$fornecedor_id' = '' then NULL else '$fornecedor_id' end,
					  case when '$pagto_fim_antecipado' = '' then NULL else '$pagto_fim_antecipado' end,
					  getdate(),
					  (select id from usuario where usuario = '$logado' and status = 'a'), '$status_id')";
			//print "<pre>".$query1."</pre>";
			$con->executar($query1);

		}

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}



	public function recalculaValores($mes, $ano){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '')
		{
			//VERIFICA O PLANO
			$query = "select distinct plano_id
						from conferencia_plano_agregado
						where status_id = 'r'
						and mes = $mes
						and ano = $ano";
			//print $query;
			$result = $con->executar($query);

			while(odbc_fetch_row($result))
			{
				$plano_id	= odbc_result($result,1);

				$query = "update conferencia_plano_agregado
						  set data_hora_inativacao = getdate(),
						  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a'),
						  status_id = 'i'
						  where plano_id = $plano_id
						  and status_id = 'r'
						  and mes = $mes
						  and ano = $ano";
				//print $query;
				$con->executar($query);

				$this->gravaValorMensalAgregado($plano_id, $mes, $ano, 2);

				$ret_gravacao =  "ok";
			}

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;

	}







	public function gravaValorMensal($plano_id, $valor, $mes, $ano){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		//verifica se os dados ja foram digitados anteriormente
		$query = "select id
					from valor_mensal_plano vmp with(nolock)
					where status_id = 'a'
					and plano_id = $plano_id
					and mes = $mes
					and ano = $ano";
		//print $query;
		$result = $con->executar($query);
		$id_encontrado		= odbc_result($result,1);

		if ($ano < 2020)
			$ret_gravacao = "Favor informar o ano a partir de 2020";
		else
		if ($id_encontrado != '')
			$ret_gravacao = "Dados já gravados para o plano e período";
		else
		if ($logado != '')
		{

			$query = "insert into valor_mensal_plano (plano_id, valor, mes, ano, data_hora_gravacao,
					  user_gravacao, status_id) values ($plano_id, replace(replace('$valor','.',''),',','.'), $mes, $ano,
					  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), 'a')";
			//print $query;
			$con->executar($query);

			$ret_gravacao = "ok";


			$this->gravaValorMensalAgregado($plano_id, $mes, $ano, 1);



		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function excluiValorMensal($id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '')
		{

			$query = "update valor_mensal_plano
					  set data_hora_inativacao = getdate(),
					  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a'),
					  status_id = 'i'
					  where id = $id";
			//print $query;
			$con->executar($query);


			$query = "update conferencia_plano_agregado
					  set data_hora_inativacao = getdate(),
					  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a'),
					  status_id = 'i'
					  where valor_mensal_plano_id = $id";
			//print $query;
			$con->executar($query);

			$ret_gravacao =  "ok";

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;

	}


	public function excluiConferenciaMensalMot($id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '')
		{


			$query = "update conferencia_plano_agregado
					  set data_hora_inativacao = getdate(),
					  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a'),
					  status_id = 'i'
					  where id = $id";
			//print $query;
			$con->executar($query);

			$ret_gravacao =  "ok";

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;

	}

	public function transfereAparelhoEstoque($num_serie, $estoque_id, $estoque_destino_id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '')
		{
			//BUSCA O ID DO APARELHO PELO NUMERO DE SERIE
			$query = "SELECT ID
						FROM APARELHO_CELULAR WITH (NOLOCK)
						WHERE SERIE = '$num_serie'
						AND STATUS_ID = 'a'";
			//print "<pre>".$query."</pre>";
			$result = $con->executar($query);
			$aparelho_id = odbc_result($result,1);

			//TIRA O APARELHO DO ESTOQUE ATUAL
			$query = "update aparelho_disponivel_estoque
					  set data_hora_gravacao = getdate(),
					  user_gravacao = (select id from usuario where usuario = '$logado' and status = 'a'),
					  estoque_destino_id = $estoque_destino_id,
					  data_saida = getdate(),
					  user_saida = (select id from usuario where usuario = '$logado' and status = 'a'),
					  status_id = 'f'
					  where aparelho_id = $aparelho_id
					  and status_id = 'a'
					  and estoque_id = $estoque_id";
			//print "<pre>".$query."</pre>";
			$con->executar($query);

			if ($estoque_destino_id == 1) //MANUTENCAO EXTERNA
			{
				//INSERE O APARELHO NO NOVO ESTOQUE
				$query = "insert into aparelho_disponivel_estoque (aparelho_id, estoque_id, data_hora_gravacao, user_gravacao,
						  data_entrada, user_entrada, estoque_antigo_id, status_id) values ($aparelho_id, $estoque_destino_id,
						  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'),
						  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), $estoque_id, 'a')";
				//print $query;
				$con->executar($query);

			}
			else
			{
				//INSERE O APARELHO NO NOVO ESTOQUE
				$query = "insert into aparelho_disponivel_estoque (aparelho_id, estoque_id, data_hora_gravacao, user_gravacao,
						  estoque_antigo_id, status_id) values ($aparelho_id, $estoque_destino_id, getdate(),
						  (select id from usuario where usuario = '$logado' and status = 'a'), $estoque_id, 't')";
				//print $query;
				$con->executar($query);
			}

			$ret_gravacao =  "ok";

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		//$retorno->ret_gravacao = $ret_gravacao;

		print $ret_gravacao;

	}


	public function transfereLinhaEstoque($num_celular, $estoque_id, $estoque_destino_id){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '')
		{
			//BUSCA O ID DA LINHA PELO NUMERO DO CELULAR
			$query = "SELECT ID
						FROM CELULAR_COVRE WITH (NOLOCK)
						WHERE NUM_CELULAR = '$num_celular'
						AND STATUS_ID = 'a'";
			//print "<pre>".$query."</pre>";
			$result = $con->executar($query);
			$linha_id = odbc_result($result,1);

			//TIRA O APARELHO DO ESTOQUE ATUAL
			$query = "update linha_disponivel_estoque
					  set data_hora_gravacao = getdate(),
					  user_gravacao = (select id from usuario where usuario = '$logado' and status = 'a'),
					  estoque_destino_id = $estoque_destino_id,
					  data_saida = getdate(),
					  user_saida = (select id from usuario where usuario = '$logado' and status = 'a'),
					  status_id = 'f'
					  where linha_id = $linha_id
					  and status_id = 'a'
					  and estoque_id = $estoque_id";
			//print "<pre>".$query."</pre>";
			$con->executar($query);

			if ($estoque_destino_id == 1) //MANUTENCAO EXTERNA
			{
				//INSERE A LINHA NO NOVO ESTOQUE
				$query = "insert into linha_disponivel_estoque (linha_id, estoque_id, data_hora_gravacao, user_gravacao,
						  data_entrada, user_entrada, estoque_antigo_id, status_id) values ($linha_id, $estoque_destino_id,
						  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'),
						  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), $estoque_id, 'a')";
				//print $query;
				$con->executar($query);

			}
			else
			{
				//INSERE A LINHA NO NOVO ESTOQUE
				$query = "insert into linha_disponivel_estoque (linha_id, estoque_id, data_hora_gravacao, user_gravacao,
						  estoque_antigo_id, status_id) values ($linha_id, $estoque_destino_id, getdate(),
						  (select id from usuario where usuario = '$logado' and status = 'a'), $estoque_id, 't')";
				//print $query;
				$con->executar($query);
			}

			$ret_gravacao =  "ok";

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		//$retorno->ret_gravacao = $ret_gravacao;

		print $ret_gravacao;

	}



	public function aceitaAparelho($id, $acao){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '')
		{
			if ($acao == 1)
			{
				//ACEITA A ENTRADA DO APARELHO NO ESTOQUE
				$query = "update aparelho_disponivel_estoque
						  set data_entrada = getdate(),
						  user_entrada = (select id from usuario where usuario = '$logado' and status = 'a'),
						  aceitar_negar = 'A',
						  status_id = 'a'
						  where id = $id
						  and status_id = 't'";
				//print "<pre>".$query."</pre>";
				$con->executar($query);
			}
			else
			{
				//TIRA O APARELHO DA ENTRADA PARA O ESTOQUE E DEVOLVE PARA O ESTOQUE QUE ENVIOU
				$query = "update aparelho_disponivel_estoque
						  set data_hora_gravacao = getdate(),
						  user_gravacao = (select id from usuario where usuario = '$logado' and status = 'a'),
						  data_saida = getdate(),
						  user_saida = (select id from usuario where usuario = '$logado' and status = 'a'),
						  aceitar_negar = 'N',
						  estoque_destino_id = estoque_antigo_id,
						  status_id = 'f'
						  where id = $id
						  and status_id = 't'";
				//print "<pre>".$query."</pre>";
				$con->executar($query);


				//INSERE O APARELHO NO NOVO ESTOQUE
				$query = "insert into aparelho_disponivel_estoque (aparelho_id, estoque_id, data_hora_gravacao, user_gravacao,
						  data_entrada, user_entrada, estoque_antigo_id, status_id)
						  select aparelho_id, estoque_antigo_id, getdate(),
						  (select id from usuario where usuario = '$logado' and status = 'a'),
						  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), estoque_id, 'a'
						  from aparelho_disponivel_estoque
						  where id = $id";
				//print $query;
				$con->executar($query);
			}

			$ret_gravacao =  "ok";

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		//$retorno->ret_gravacao = $ret_gravacao;

		print $ret_gravacao;

	}


	public function aceitaLinha($id, $acao){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '')
		{
			if ($acao == 1)
			{
				//ACEITA A ENTRADA DA LINHA NO ESTOQUE
				$query = "update linha_disponivel_estoque
						  set data_entrada = getdate(),
						  user_entrada = (select id from usuario where usuario = '$logado' and status = 'a'),
						  aceitar_negar = 'A',
						  status_id = 'a'
						  where id = $id
						  and status_id = 't'";
				//print "<pre>".$query."</pre>";
				$con->executar($query);
			}
			else
			{
				//TIRA A LINHA DA ENTRADA PARA O ESTOQUE E DEVOLVE PARA O ESTOQUE QUE ENVIOU
				$query = "update linha_disponivel_estoque
						  set data_hora_gravacao = getdate(),
						  user_gravacao = (select id from usuario where usuario = '$logado' and status = 'a'),
						  data_saida = getdate(),
						  user_saida = (select id from usuario where usuario = '$logado' and status = 'a'),
						  aceitar_negar = 'N',
						  estoque_destino_id = estoque_antigo_id,
						  status_id = 'f'
						  where id = $id
						  and status_id = 't'";
				//print "<pre>".$query."</pre>";
				$con->executar($query);


				//INSERE A LINHA NO NOVO ESTOQUE
				$query = "insert into linha_disponivel_estoque (linha_id, estoque_id, data_hora_gravacao, user_gravacao,
						  data_entrada, user_entrada, estoque_antigo_id, status_id)
						  select linha_id, estoque_antigo_id, getdate(),
						  (select id from usuario where usuario = '$logado' and status = 'a'),
						  getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), estoque_id, 'a'
						  from linha_disponivel_estoque
						  where id = $id";
				//print $query;
				$con->executar($query);
			}

			$ret_gravacao =  "ok";

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		//$retorno->ret_gravacao = $ret_gravacao;

		print $ret_gravacao;

	}


	public function alteraDadoConferencia($valor, $conferencia_id, $numero_campo){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '')
		{
			if ($numero_campo == 1)
			{
				//GRAVA LOG DE ALTERACAO
				$query = "insert into valor_alterado_conferencia (conferencia_id, valor_antigo, novo_valor, data_hora_gravacao,
						  user_gravacao, status_id)
						  select id, valor_cobrado, replace(replace('$valor','.',''), ',', '.'), getdate(),
						  (select top 1 id from usuario where usuario = '$logado' and status = 'a'),'a'
						  from conferencia_plano_agregado
						  where id = $conferencia_id";
				//print $query;
				$con->executar($query);

				//ALTERA O VALOR
				$query = "update conferencia_plano_agregado
						  set valor_cobrado = replace(replace('$valor','.',''), ',', '.')
						  where id = $conferencia_id";
				//print $query;
				$con->executar($query);

			}

			$query = "update conferencia_plano_agregado
					  set $nome_campo = replace(replace('$valor','.',''), ',', '.')
					  where id = $conferencia_id";
			//print $query;
			$con->executar($query);

			$ret_gravacao =  "ok";

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;

	}


	public function alteraData($data, $id, $numero_campo){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '')
		{
			if ($numero_campo == 1)
				$campo_data = 'data_inicio';
			else
				$campo_data = 'data_fim';

			$query = "select
						case when '$numero_campo' = 1
								then case when '$data' > data_fim
											then 'invalido'
											else null
									 end
								else case when '$numero_campo' = 2
											then case when data_inicio > '$data'
														then 'invalido'
														else null
													end
										end
						END
						from linha_aparelho_funcionario laf with (nolock)
						where id in ($id)";
			//print "<pre>".$query."</pre>";
			$result = $con->executar($query);
			$compara_datas = odbc_result($result,1);


			$query = "select
						case when '$data' > CONVERT(VARCHAR(10), GetDate(), 120)
								then 'maior'
								else null
						end";
			//print "<pre>".$query."</pre>";
			$result = $con->executar($query);
			$compara_data_atual = odbc_result($result,1);

			/*
			$query = "select id
						from linha_aparelho_funcionario laf with (nolock)
						where ((celular_id = (select celular_id
											  from linha_aparelho_funcionario laf with (nolock)
											where id in ($id))) OR
							   (aparelho_id = (select aparelho_id
											   from linha_aparelho_funcionario laf with (nolock)
											   where id in ($id))))
						and laf.id not in ($id)
						and (('$data' between dateadd(dd,+1,data_inicio) and
						isnull(dateadd(dd,-1,data_fim), CONVERT(VARCHAR(10), GetDate(), 120))) OR
						    (('$data' < data_inicio) and (isnull((select data_fim
															      from linha_aparelho_funcionario laf with (nolock)
															      where id in ($id)),CONVERT(VARCHAR(10), GetDate(), 120)) >
																  data_fim)) OR
							('$data' < data_fim))";
			//print "<pre>".$query."</pre>";
			$result = $con->executar($query);
			$compara_uso = odbc_result($result,1);
			*/

			$query = "select id, data_inicio, data_fim
						from linha_aparelho_funcionario laf with (nolock)
						where ((celular_id = (select celular_id
											  from linha_aparelho_funcionario laf with (nolock)
											where id in ($id))) OR
							   (aparelho_id = (select aparelho_id
											   from linha_aparelho_funcionario laf with (nolock)
											   where id in ($id))))
						and laf.id < ($id)
						and '$data' < data_fim
						union
						select id, data_inicio, data_fim
						from linha_aparelho_funcionario laf with (nolock)
						where ((celular_id = (select celular_id
											  from linha_aparelho_funcionario laf with (nolock)
											where id in ($id))) OR
							   (aparelho_id = (select aparelho_id
											   from linha_aparelho_funcionario laf with (nolock)
											   where id in ($id))))
						and laf.id > ($id)
						and '$data' > data_inicio";
			//print "<pre>".$query."</pre>";
			$result = $con->executar($query);
			$compara_uso = odbc_result($result,1);


			if ($compara_datas == 'invalido')
				$ret_gravacao = 'Data Inicial maior que Data Final';
			else
			if ($compara_data_atual == 'maior')
				$ret_gravacao = 'Data informada maior que data atual';
			else
			if ($compara_uso != '')
				$ret_gravacao = 'Período de data informada já utilizada com a linha ou aparelho';
			else
			{
				//ALTERA O VALOR
				$query = "update linha_aparelho_funcionario
						  set $campo_data = '$data',
						  data_ultima_alteracao = getdate(),
						  user_ultima_alteracao = (select id from usuario where usuario = '$logado' and status = 'a')
						  where id = $id";
				//print $query;
				$con->executar($query);

				$ret_gravacao =  "ok";
			}
		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;

	}


	public function confereValores($mes, $ano){

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '')
		{

			$query = "update conferencia_plano_agregado
					  set data_hora_conferencia = getdate(),
					  user_conferencia = (select id from usuario where usuario = '$logado' and status = 'a'),
					  status_id = 'r'
					  where mes = $mes
					  and ano = $ano
					  and status_id = 'a'";
			//print $query;
			$con->executar($query);

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
			$enviou = enviar_email("helpdesk@covre.com.br", "SCI- Celulares Covre", "$email_destino", "DADOS CONFERIDOS - CELULARES COVRE",
							"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
							<html xmlns='http://www.w3.org/1999/xhtml'>
							<head>
							<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
							</head>
							<body>
							<b>E-MAIL AUTOM&Aacute;TICO</b>
							<br><br><br>A Confer&ecirc;ncia de valores dos celulares dos agregados do per&iacute;odo $descricao_mes_ano, est&atilde;o dispon&iacute;veis para gera&ccedil;&atilde;o dos vales.
							<br><br><br>

							<br><b>SCI - CELULARES COVRE</b>","0","");

			$query = "insert into log_email_celular values ($mes, $ano, getdate(), 'dados conferidos', '$email_destino')";
			//print $query;
			$con->executar($query);


			$ret_gravacao =  "ok";

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;

	}


}



?>