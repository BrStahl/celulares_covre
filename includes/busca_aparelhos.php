<?
include('../class/Dados.php');


$dados = new Dados();


	$rel_aparelhos = $dados->buscaAparelhos();  

	print $rel_aparelhos->aparelhos;

