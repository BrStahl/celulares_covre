<?
include('../class/Dados.php');


$dados = new Dados();

$id		= $_POST["id"];


	$rel_despesa = $dados->buscaDespesaExtra($id);  

	print $rel_despesa->despesa;

