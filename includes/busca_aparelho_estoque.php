<?
include('../class/Estoque.php');

$estoque = new Estoque();

$estoque_id		= $_POST["id"];


	$aparelho_estoque = $estoque->buscaAparelhoEstoque($estoque_id);
	$relacao_aparelho	= $aparelho_estoque->table;                

	$linha_estoque = $estoque->buscaLinhaEstoque($estoque_id);
	$relacao_linha	= $linha_estoque->table;   

	print $relacao_aparelho."<hr style='border-color:black'>".$relacao_linha;

