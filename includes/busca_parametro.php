<?
include('../class/Dados.php');


$dados = new Dados();



	$rel_parametro = $dados->buscaParametro();  

	$valor = $rel_parametro->valor;
	$dia_ini = $rel_parametro->dia_ini;
	$dia_fim = $rel_parametro->dia_fim;

	print "1|".$valor."|".$dia_ini."|".$dia_fim."|";


