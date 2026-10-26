<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$mes_ano = $_POST["mes_ano"];


$array = spliti ("/", $mes_ano, 2);

$mes = $array[0];
$ano = $array[1];


	$gravacao = $dados->recalculaValores($mes, $ano);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;

?>

