<?
include('../class/Dados.php');


$dados = new Dados();

$mes_ano = $_POST["mes_ano"];


$array = spliti ("/", $mes_ano, 2);

$mes = $array[0];
$ano = $array[1];


	$rel_dados = $dados->buscaConferencia($mes, $ano);  

	print $rel_dados->relatorio;

