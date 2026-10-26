<?
include('../class/Dados.php');


$dados = new Dados();

$ccusto		= $_POST["ccusto"];


	$rel_permissao = $dados->verificaPermissaoCC($ccusto);  

	print $rel_permissao->permissao;

