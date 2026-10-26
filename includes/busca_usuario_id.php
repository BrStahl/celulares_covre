<?
include('../class/Usuario.php');

$busca_usuario = new Usuario();



$nome	 	 	= $_POST['nome'];


$dados = $busca_usuario->buscaUsuario($nome);  

$rel_dados = $dados->resultado;   
	
print $rel_dados;

