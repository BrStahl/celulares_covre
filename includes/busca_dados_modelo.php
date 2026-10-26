<?
include('../class/Dados.php');


$dados = new Dados();

$id		= $_POST["id"];


	$rel_modelo = $dados->buscaDadosModelo($id);  

	print $rel_modelo->modelo;

