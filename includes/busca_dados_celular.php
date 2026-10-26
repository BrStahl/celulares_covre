<?
include('../class/Dados.php');


$dados = new Dados();

$id		= $_POST["id"];


	$rel_celular = $dados->buscaDadosCelular($id);  

	print $rel_celular->celular;

