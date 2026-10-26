<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";


class Usuario extends ConexaoSCI{


	public function buscaUsuario($nome){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
			  
			 
		$query = "select id, usuario
				  from usuario with (nolock)
				  where status = 'a'
				  and nome = '$nome'";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		
 
		$id			= odbc_result($result,1);
		$usuario	= odbc_result($result,2);	


		$retorno->resultado = "1|".$id."|".$usuario."|";

		return $retorno;	
	
	}		
	
	
}


?>