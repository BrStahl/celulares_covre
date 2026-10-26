<?
include('../class/Dados.php');


$dados = new Dados();

$id		= $_POST["id"]; 


$rel_dados = $dados->dadosAparelho($id);  


print $rel_dados->relatorio;  


