<?
session_name("covre_ti");
session_start();


include($_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
require("../SCI/includes/page_func_sci.php");
require_once "../SCI/class/Autentica.php";
require_once "class/Dados.php";
//require_once "class/GravaLog.php";

$autenticacao_sci = new Autentica();

$con = new ConexaoSCI();

$dados = new Dados;
//$logs = new GravaLog;

$localItem = "../celulares_covre/valor_mensal_plano.php";
$logado    = $_SESSION["usuario_logado"];
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem,$logado);
$acesso  = $dados_autenticacao->permissao;
$id_item  = $dados_autenticacao->id_item;
$usuario_id  = $dados_autenticacao->usuario_id;
$nome_usuario  = $dados_autenticacao->nome_usuario;


//$acesso = "permitido";


if($acesso <> "permitido")
{
   print "<script language = 'JavaScript'>alert('Acesso negado para esta página');</script/>";

   $dados_gravacao = $autenticacao_sci->redirecionaLogin();
   $local  = $dados_gravacao->local;

   print "<script language = 'JavaScript'>window.location='$local';</script/>";
}
else
{
	 $dados_gravacao = $autenticacao_sci->gravaAutenticacao($id_item,$usuario_id);

    /** Buttons */
    if($fechar_p != ""){ print"<script language='javascript'>open(location, '_self').close();</script>";}
    if($buscar){}


	$id			 	 	= $_POST['id'];




	if($novo != "")
	{
			print"
			<script language='javascript'>
				window.location.href='cadastro_celular.php';
			</script>
			";
	}







    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Valor Mensal - Plano</title>
    <link rel="stylesheet" href="../SCI/css/bootstrap.css">
    <link rel="stylesheet" href="../SCI/css/estilo_bts.css">
    <link rel="stylesheet" href="css/relsm.css">
    <link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="../SCI/js/datatables/datatables.css"/>
    <link rel="stylesheet" type="text/css" href="https://nightly.datatables.net/buttons/css/buttons.dataTables.min.css"/>
    <script src="../SCI/js/jquery.js"></script>
    <script src="../SCI/js/bootstrap.js"></script>
    <script src="../SCI/js/jquery.ajaxQueue.js"></script>
    <script src="../SCI/js/jquery.mask.js"></script>
    <script type="text/javascript" src="../SCI/js/datatables/datatables.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

    <script type="text/javascript" language="javascript" src="https://nightly.datatables.net/buttons/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript" src="https://nightly.datatables.net/buttons/js/buttons.html5.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://nightly.datatables.net/buttons/js/buttons.print.min.js"></script>

</script>


</head>


<body style="background-color: #3CB371" >
<div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
        <div class="panel-heading panel-legend" id="painel-heading"><b>Valor Mensal - Plano</b></div>
    </div>
    <br>
<div class="col-xs-10">
    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-7 panel-padrao">
            <div class="panel-body col-xs-12">
                <div class="table-responsive covre">

                    <table width="83%" class="table table-bordered tb-padrao">
                        <tbody>

                           	<tr>
                            	<td width="30" class="no-border left altura">&nbsp;</td>
								<td width="257" class="no-border left altura">
									<font size="-1"><b>Plano: </b></font>
                                </td>
								<td width="164" align="left" class="no-border left altura">
									<font size="-1"><b>Valor: </b></font>
                                </td>
								<td width="303" class="no-border left altura">
									<font size="-1"><b>Mes: </b></font>
                                </td>
								<td width="209" class="no-border left altura"><font size="-1"><b>Ano: </b></font></td>
								<td width="112" rowspan="2" class="no-border left altura"><input type='button' class='btn-sm btn-primary' value='Gravar' id='gravar' name='gravar' onClick='javascript:grava_registro_mensal_plano()'></td>
								<td width="17" rowspan="2" class="no-border left altura">&nbsp;</td>
                            </tr>

                            <tr>
	                            <td class="no-border left altura">&nbsp;</td>
								<td class="no-border left altura"><div class="col-xs-12 left-inline"><span class="col-xs-10 left-inline">
								  <?php
                                            $query = "select id, descricao
														from plano_celular with (nolock)
														where status_id = 'a'";
											//print $query;
                                            $result = $con->executar($query);

                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1'
													name='plano_id' id='plano_id' value='$plano_id'><option></option>";

                                            while(odbc_fetch_row($result))
                                            {

                                                if (odbc_result($result, 1) == $plano_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";

                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
							    </span></div></td>
								<td  class="no-border left altura" ><div class="col-xs-11 left-inline"><span class="col-xs-10 left-inline">
								  <input type='text' class='form-control txt-center altura-campo only-number_virgula' aria-describedby='basic-addon1' name='valor'
									id='valor' value='<?php print $valor ?>' size='7'>
								</span></div></td>
								<td  class="no-border left altura"><div class="col-xs-12 left-inline"><span class="col-xs-10 left-inline">
								  <?php
                                            $query = "select 1 id, 'Janeiro' mes
														union
														select 2, 'Fevereiro'
														union
														select 3, 'Marco'
														union
														select 4, 'Abril'
														union
														select 5, 'Maio'
														union
														select 6, 'Junho'
														union
														select 7, 'Julho'
														union
														select 8, 'Agosto'
														union
														select 9, 'Setembro'
														union
														select 10, 'Outubro'
														union
														select 11, 'Novembro'
														union
														select 12, 'Dezembro'
														";
											//print $query;
                                            $result = $con->executar($query);

                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='mes' id='mes' value='$mes'><option></option>";

                                            while(odbc_fetch_row($result))
                                            {

                                                if (odbc_result($result, 1) == $mes)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";

                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
							    </span></div></td>
								<td  class="no-border left altura"><div class="col-xs-8 left-inline">
								  <input name='ano' type='text' class='form-control txt-center altura-campo only-number' id='ano' value='<?php print $ano ?>' size="4"  maxlength='4' aria-describedby='basic-addon1'>
							    </div></td>
						  	</tr>
                       	</tbody>
                    </table>
                </div>
            </div>

              <p></p><p></p>
               <div id="exibe_resultado">
                        <?php

                            $relatorio_valor = $dados->exibeValorMensal();

                            $rel_valor = $relatorio_valor->valor;

                            print $rel_valor;

                        ?>
              </div>

             <div class="panel-body col-lg-12">
               <div class="row">
                    <div class="col-xs-4">
                        <div class="table-responsive radius-border">
<div class="col-xs-12" id="msg-erro-edit"></div>
                        </div>
                    </div>
                </div>
                 <button type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p">Fechar</button>


            </div>
        </div>
    </div>
</form>



</div>
<style media="screen">
  .dataTables_wrapper{
    position:static !important;
  }
</style>

<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/main.js"></script>

<script type="text/javascript">
$(document).ready(function() {
  order_valor_mensal_plano();
});
</script>

</body>
</html>
<?


	include("../SCI/includes/modal.php");
	include("../SCI/includes/modal_list.php");




}
?>
