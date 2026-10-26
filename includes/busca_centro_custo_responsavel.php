<?
include('../class/Dados.php');


$dados = new Dados();

$id		= $_POST["id"];


	$rel_centro_custo = $dados->buscaCCResponsavel($id);  

	print $rel_centro_custo->centro_custo;

