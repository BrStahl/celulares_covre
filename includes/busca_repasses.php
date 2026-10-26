<?
include('../class/Dados.php');


$dados = new Dados();

$repasses = $dados->buscaRepasses($_POST['id']);

print $repasses;
