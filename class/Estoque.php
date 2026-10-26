<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";


class Estoque extends ConexaoSCI{

	
	//BUSCA DADOS
	public function buscaEstoqueResponsavel($usuario_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select permissao.estoque_id, permissao.administrador, permissao.gerar_vale_id
					from permissao_celular_covre permissao with (nolock)
					where permissao.usuario_id = $usuario_id
					and status_id = 'a'";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
			   
	
		$estoque_id			= odbc_result($result,1);
		$administrador		= odbc_result($result,2);
		$gerar_vale_id		= odbc_result($result,3);		
		  
		$retorno->estoque_id = $estoque_id;
		$retorno->administrador = $administrador;
		$retorno->gerar_vale_id = $gerar_vale_id;		

		return $retorno;

	}


	public function buscaAparelhoEstoque($estoque_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		//SELECIONA O NOME DO ESTOQUE
		$query = "select estoque.descricao estoque
					FROM estoque_aparelho_celular estoque with (nolock) 
					where estoque.id = $estoque_id
					and estoque.status_id = 'a'";
		//print $query;
		$result = $con->executar($query);
		$nome_estoque		= odbc_result($result,1);		
		
	 	//SELECIONA OS APARELHOS DISPONIVEIS POR ESTOQUE
		$query = "select aparelho.id, 
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
					aparelho.serie,
					ade.status_id status,
					ade.id
					FROM aparelho_disponivel_estoque ade with (nolock)
					join aparelho_celular aparelho with (nolock) on
						aparelho.id = ade.aparelho_id
					join modelo_aparelho_celular modelo with (nolock) on
						modelo.id = aparelho.modelo_id
					join marca_celular marca with (nolock) on
						marca.id = modelo.marca_id					
					where ade.estoque_id = $estoque_id
					and ade.data_saida is null
					and aparelho.status_id = 'a'
					order by ade.status_id desc, descricao_aparelho";
		//print "<pre>".$query."</pre>";
		$result = $con->executar($query);
		
		$retorno = '';
		$status_transf_anterior = '';
		$contador = 1;
		$table = "";
			   
		$table = "<b>ESTOQUE: </b><font color='#0000FF'>$nome_estoque</font><p></p>";
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$aparelho_id		= odbc_result($result,1);
			$aparelho			= odbc_result($result,2);
			$num_serie			= odbc_result($result,3);
			$status_transf		= odbc_result($result,4);
			$id					= odbc_result($result,5);

			if ($status_transf == 't')
				$disabled = 'disabled';
			else
				$disabled = '';
			
			if (($status_transf_anterior != '') && ($status_transf_anterior != $status_transf))
			{
				$table .= "</tbody></table>";				
				
				$contador = 1;
			}
			
			if (($status_transf == 't') && ($contador == 1))
				$table .= "<table class='table table-bordered table-hover' id='myTable3'>
								<thead>
									<th bgcolor='#d3d3d3' width='120'><a href='javascript:void(0)'>ACEITAR / NEGAR</a></th>
									<th bgcolor='#d3d3d3' width='600' ><a href='javascript:void(0)'>APARELHO À ENTRAR NO ESTOQUE</a></th>							
								</thead><tbody> ";	
			else
				if (($status_transf == 'a') && ($contador == 1))
					$table .= "<table class='table table-bordered table-hover' id='myTable3'>
									<thead>
										<th bgcolor='#d3d3d3' width='50'><a href='javascript:void(0)'>&nbsp;</a></th>
										<th bgcolor='#d3d3d3' width='600' ><a href='javascript:void(0)'>APARELHO EM ESTOQUE</a></th>							
									</thead><tbody>";								
				
			if ($status_transf == 't')
				$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
				  	<td bgcolor='#FFFFFF'><div align='center'><input type='radio' name='radio$id' id='liberar' value='a' onClick='javascript: aceita_aparelho($id, 1, $estoque_id)'/><b>Aceitar &nbsp;&nbsp;&nbsp;<input type='radio' name='radio$id' id='recusar' value='r' onClick='javascript: aceita_aparelho($id, 2, $estoque_id)'/><b>Negar</div></td>";		
			else	
				$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
				  	<td bgcolor='#FFFFFF'><div align='center'><font size='-2'><input type='checkbox' name='check$aparelho_id' $disabled id='check$aparelho_id' value='1' onclick='javascript:seleciona_aparelho(this, ".chr(34).$num_serie.chr(34).")'></font></div></td>";
					
				$table .= "<td bgcolor='#FFFFFF'><left><font size='-1'>".$aparelho."</font></left></td>										
			
			 </tr>"; 
			
			$status_transf_anterior = $status_transf;	  
			$contador++;	 
		 } 
		 
		  $table .= "</tbody></table>";
		  
		  $retorno->table = $table;

		  return $retorno;

	}

	public function buscaLinhaEstoque($estoque_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		
	 	//SELECIONA AS LINHAS DISPONIVEIS POR ESTOQUE
		$query = "select celular.id, '('+substring(num_celular,1,2)+')'+substring(num_celular,3,5)+'-'												
					+substring(num_celular,8,4)+' - '+operadora.descricao descricao_linha,
					lde.status_id status,
					lde.id,
					celular.num_celular
					FROM linha_disponivel_estoque lde with (nolock)
					join celular_covre celular with (nolock) on
						celular.id = lde.linha_id
					join operadora_celular operadora with (nolock) on
						operadora.id = celular.operadora_id
					where lde.estoque_id = $estoque_id
					and lde.data_saida is null
					and celular.status_id = 'a'
					and not exists (select top 1 1
						from linha_aparelho_funcionario laf with (nolock)
						where laf.status_id = 'a'
						and laf.celular_id = celular.id
						and laf.data_fim is null)
					order by lde.status_id desc";
		//print "<pre>".$query."</pre>";
		$result = $con->executar($query);
		
		$retorno = '';
		$status_transf_anterior = '';
		$contador = 1;
		$table2 = "";
		
		while(odbc_fetch_row($result)) 
		{ 
			$celular_id			= odbc_result($result,1);
			$celular			= odbc_result($result,2);
			$status_transf		= odbc_result($result,3);
			$id					= odbc_result($result,4);
			$num_celular		= odbc_result($result,5);

			if ($status_transf == 't')
				$disabled = 'disabled';
			else
				$disabled = '';
			
			if (($status_transf_anterior != '') && ($status_transf_anterior != $status_transf))
			{
				$table2 .= "</tbody></table>";				
				
				$contador = 1;
			}
			
			if (($status_transf == 't') && ($contador == 1))
				$table2 .= "<table class='table table-bordered table-hover' id='myTable3'>
								<thead>
									<th bgcolor='#d3d3d3' width='120'><a href='javascript:void(0)'>ACEITAR / NEGAR</a></th>
									<th bgcolor='#d3d3d3' width='600' ><a href='javascript:void(0)'>LINHA À ENTRAR NO ESTOQUE</a></th>							
								</thead><tbody> ";	
			else
				if (($status_transf == 'a') && ($contador == 1))
					$table2 .= "<table class='table table-bordered table-hover' id='myTable3'>
									<thead>
										<th bgcolor='#d3d3d3' width='50'><a href='javascript:void(0)'>&nbsp;</a></th>
										<th bgcolor='#d3d3d3' width='600' ><a href='javascript:void(0)'>LINHA EM ESTOQUE</a></th>							
									</thead><tbody>";								
				
			if ($status_transf == 't')
				$table2 .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
				  	<td bgcolor='#FFFFFF'><div align='center'><input type='radio' name='radio$id' id='liberar' value='a' onClick='javascript: aceita_linha($id, 1, $estoque_id)'/><b>Aceitar &nbsp;&nbsp;&nbsp;<input type='radio' name='radio$id' id='recusar' value='r' onClick='javascript: aceita_linha($id, 2, $estoque_id)'/><b>Negar</div></td>";		
			else	
				$table2 .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
				  	<td bgcolor='#FFFFFF'><div align='center'><font size='-2'><input type='checkbox' name='check$celular_id' $disabled id='check$celular_id' value='1' onclick='javascript:seleciona_linha(this, ".chr(34).$num_celular.chr(34).")'></font></div></td>";
					
				$table2 .= "<td bgcolor='#FFFFFF'><left><font size='-1'>".$celular."</font></left></td>										
			
			 </tr>"; 
			
			$status_transf_anterior = $status_transf;	  
			$contador++;	 
		 } 
		 
		  $table2 .= "</tbody></table>";
		  
		  $retorno->table = $table2;

		  return $retorno;

	}


}


?>