<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;


$id		 			= $_POST["id"];
$pessoa_id 			= $_POST["pessoa_id"];
$corp_pessoa_id 	= $_POST["corp_pessoa_id"];
$celular_covre_id	= $_POST["celular_covre_id"];
$aparelho_id		= $_POST["aparelho_id"];
$data_ini			= $_POST["data_ini"];
$data_fim			= $_POST["data_fim"];
$cod_ccusto			= $_POST["cod_ccusto"];
$observacao			= $_POST["observacao"];
$id_antigo          = isset($_POST["id_antigo"]) ? $_POST["id_antigo"] : "";
$data_fim_antigo    = isset($_POST["data_fim_antigo"]) ? $_POST["data_fim_antigo"] : "";


	$data_inicio =
	implode(preg_match("~\/~", $data_ini) == 0 ? "/" : "-",
	array_reverse(explode(preg_match("~\/~", $data_ini) == 0 ? "-" : "/", $data_ini)));

	$data_final =
	implode(preg_match("~\/~", $data_fim) == 0 ? "/" : "-",
	array_reverse(explode(preg_match("~\/~", $data_fim) == 0 ? "-" : "/", $data_fim)));

	$data_final_antigo = "";
	if ($data_fim_antigo != "") {
		$data_final_antigo =
		implode(preg_match("~\/~", $data_fim_antigo) == 0 ? "/" : "-",
		array_reverse(explode(preg_match("~\/~", $data_fim_antigo) == 0 ? "-" : "/", $data_fim_antigo)));
	}


	$gravacao = $dados->gravaCelular($id, $pessoa_id, $celular_covre_id, $aparelho_id, $data_inicio, $data_final, $cod_ccusto, $observacao, $corp_pessoa_id, $id_antigo, $data_final_antigo);

	$retorno = $gravacao->ret_gravacao;

	print $retorno;


?>
