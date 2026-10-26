<?
include('../class/Dados.php');


$dados = new Dados();


	$rel_celulares = $dados->buscaCelularesDespesa();  

	print $rel_celulares->celulares;

