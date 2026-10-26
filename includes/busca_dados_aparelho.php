<?
include('../class/Dados.php');


$dados = new Dados();

$id		= $_POST["id"];


	$rel_aparelho = $dados->buscaDadosAparelho($id);  

	print $rel_aparelho->aparelho;

