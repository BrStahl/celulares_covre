<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$aparelhos		 		= $_POST["aparelhos"];
$linhas			 		= $_POST["linhas"];
$estoque_id 			= $_POST["estoque_id"];
$estoque_destino_id		= $_POST["estoque_destino_id"];


$aparelhos .= ",";
$linhas .= ",";

$count = substr_count($aparelhos, ',');
$count1 = substr_count($linhas, ',');

	
	for($i=1;$i<=$count;$i++)
	{
		
		if ($i < $count)
		{	
			$pos_virgula = strpos($aparelhos,',');	
			$num_serie = substr($aparelhos, 0, $pos_virgula); 
			
			$aparelhos = str_replace($num_serie.",","",$aparelhos);
		}
		else
		{
			$aparelhos = str_replace(",","",$aparelhos);
			$num_serie = $aparelhos;
		}
	
		$gravacao = $dados->transfereAparelhoEstoque($num_serie, $estoque_id, $estoque_destino_id);  

	}//fim for aparelhos
	
	
	for($j=1;$j<=$count1;$j++)
	{
		
		if ($j < $count1)
		{	
			$pos_virgula = strpos($linhas,',');	
			$num_celular = substr($linhas, 0, $pos_virgula); 
			
			$linhas = str_replace($num_celular.",","",$linhas);
		}
		else
		{
			$linhas = str_replace(",","",$linhas);
			$num_celular = $linhas;
		}
	
		$gravacao = $dados->transfereLinhaEstoque($num_celular, $estoque_id, $estoque_destino_id);  

	}//fim for linhas	
	
?>

