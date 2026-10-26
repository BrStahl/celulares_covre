<?
include('../class/GravaDados.php');

$deleta_registro = new GravaDados();


$id		 	= $_POST['id'];


$dados = $deleta_registro->excluiValorMensal($id);  

$rel_dados = $dados->resultado;   
	
print $rel_dados;

