<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$aparelhos		 		= $_POST["aparelhos"];
$estoque_id 			= $_POST["estoque_id"];
$estoque_destino_id		= $_POST["estoque_destino_id"];


$aparelhos .= ",";

$count = substr_count($aparelhos, ',');

	
	for($i=1;$i<=$count;$i++)
	{
		//$tracking_id = '';
		
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
	

	}//fim for
	
?>

