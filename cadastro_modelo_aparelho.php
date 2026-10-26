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

$localItem = "../celulares_covre/cadastro_modelo_aparelho.php";
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
				window.location.href='cadastro_modelo_aparelho.php';
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
    <title>Cadastro - Modelo Aparelho Celular</title>
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
        <div class="panel-heading panel-legend" id="painel-heading"><b>Cadastro - Modelo  Aparelho Celular</b></div>
    </div>
    <br>
<div class="col-xs-10">
    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-9 panel-padrao">
            <div class="panel-body col-xs-12">
                <div class="table-responsive covre">

                    <table width="83%" class="table table-bordered tb-padrao">
                        <tbody>

                           	<tr>
                            	<td width="36" class="no-border left altura">&nbsp;</td>
								<td width="181" class="no-border left altura">
									<font size="-1"><b>Marca: </b></font>
                                </td>
								<td width="733" class="no-border left altura"><font size="-1"><b>Modelo: </b></font></td>
								<td width="145" class="no-border left altura"><font size="-1"><b>Status:</b></font></td>
								<td width="6" rowspan="2" class="no-border left altura">&nbsp;</td>
                            </tr>

                            <tr>
	                            <td class="no-border left altura">&nbsp;</td>
								<td class="no-border left altura">
                                    <div class="col-xs-11 left-inline"><span class="col-xs-10 left-inline">
                                      <?php
                                            $query = "select id, descricao
													  from marca_celular with (nolock)
													  where status_id = 'a'
													  order by descricao";
											//print $query;
                                            $result = $con->executar($query);

                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1'
													name='marca_id' id='marca_id' value='$marca_id'><option></option>";

                                            while(odbc_fetch_row($result))
                                            {

                                                if (odbc_result($result, 1) == $operadora_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";

                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
                                    </span></div>
                                    <span class="col-xs-4 left-inline">
                                    <input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='id' id='id' value='<?php print $id ?>' size='8' maxlength='4' onkeypress='somente_numero(this)'>
                                </span>                                </td>
								<td  class="no-border left altura"><div class="col-xs-11 left-inline">
								  <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='modelo' id='modelo' value='<?php print $modelo ?>'  maxlength='30'>
							    </div></td>
							  	<td  class="no-border left altura"><div class="col-xs-10 left-inline">
							  	  <?php
                                            $query = "select 'a', 'Ativo'
													  union
													  select 'i', 'Inativo'";
											//print $query;
                                            $result = $con->executar($query);

                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='status_id'
													id='status_id' value='$status_id'>";

                                            while(odbc_fetch_row($result))
                                            {

                                                if (odbc_result($result, 1) == $status_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";

                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
						  	    </div></td>
						  	</tr>
                           	<tr>
								<td height="120" class="no-border left altura">&nbsp;</td>
								<td height="120" colspan="4" class="no-border left altura">&nbsp;</td>
							</tr>
                           	<tr>
                           	  <td height="120" class="no-border left altura">&nbsp;</td>
                           	  <td height="120" colspan="4" class="no-border left altura"><div align="center">
                           	    <input type='submit' class='btn-sm btn-default' value='Novo' id='novo' name='novo'>
                           	    <input type='button' class='btn-sm btn-primary' value='Gravar' id='gravar' name='gravar' onClick='javascript:grava_registro_modelo_aparelho()'>
                       	      </div>
                       	  </tr>
                        </tbody>
                    </table>
                </div>
            </div>

              <p></p><p></p>
               <div id="exibe_resultado">
                        <?php

                            $relatorio_modelo = $dados->exibeModeloCelular();

                            $rel_modelo = $relatorio_modelo->modelo;

                            print $rel_modelo;

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

        <!-- Modal -->
 	<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal_informacoes" class="modal fade">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header" style="background-color:#095D92">
                      <h4 class="modal-title"><font color="#FFFFFF">HISTÓRICO DO REGISTRO</font></h4>
                  </div>

                  <div class="modal-body">
                    <div id="resultado_modal">

                    </div>


                 </div>
                  <div class="modal-footer">
                    <button data-dismiss='modal' class='btn btn-default' type='button' style=' color:#FFF; background-color:#FF0000' onclick="limpa_campos_modal()">Fechar</button>

                  </div>
              </div>
          </div>
      </div>



</div>
<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/main.js"></script>
<style media="screen">
  .dataTables_wrapper{
    position:static !important;
  }
</style>
</body>
</html>
<?


	include("../SCI/includes/modal.php");
	include("../SCI/includes/modal_list.php");




}
?>
