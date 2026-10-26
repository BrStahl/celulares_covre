<?
include('../class/Dados.php');


$dados = new Dados();

$id		= $_POST["id"];


	$rel_responsavel = $dados->buscaDadosResponsavel($id);  

	print $rel_responsavel->responsavel;

