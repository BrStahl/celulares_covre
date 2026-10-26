<?
include('../class/GravaDados.php');

$deleta_registro = new GravaDados();


$id		 	= $_POST['id'];


$dados = $deleta_registro->excluiConferenciaMensalMot($id);  

$rel_dados = $dados->resultado;   
	
print $rel_dados;

