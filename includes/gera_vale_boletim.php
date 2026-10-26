<?php
session_name("covre_ti");
session_start();

require_once "../class/GeraVale.php";

$dados = new GeraVale;	


$mes_ano = $_POST["mes_ano"];


$array = spliti ("/", $mes_ano, 2);

$mes = $array[0];
$ano = $array[1];


	$gravacao = $dados->geraValeBoletim($mes, $ano);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>

