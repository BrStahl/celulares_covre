<?
session_name("covre_ti");
session_start();


include($_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
require("../SCI/includes/page_func_sci.php");
require_once "../SCI/class/Autentica.php";
require_once "class/Dados.php";
require_once "class/Estoque.php";


$autenticacao_sci = new Autentica();

$con = new ConexaoSCI();

$dados = new Dados;
$estoque = new Estoque;


$localItem = "../celulares_covre/celular_aparelho_funcionario.php";
$logado    = $_SESSION["usuario_logado"];
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem, $logado);
$acesso  = $dados_autenticacao->permissao;
$id_item  = $dados_autenticacao->id_item;
$usuario_id  = $dados_autenticacao->usuario_id;

//$acesso = "permitido";


if ($acesso <> "permitido") {
  print "<script language = 'JavaScript'>alert('Acesso negado para esta página');</script/>";

  $dados_gravacao = $autenticacao_sci->redirecionaLogin();
  $local  = $dados_gravacao->local;

  print "<script language = 'JavaScript'>window.location='$local';</script/>";
} else {
  $dados_gravacao = $autenticacao_sci->gravaAutenticacao($id_item, $usuario_id);


  $dados_estoque = $estoque->buscaEstoqueResponsavel($usuario_id);
  $estoque_id   = $dados_estoque->estoque_id;
  $administrador  = $dados_estoque->administrador;


  /** Buttons */
  if ($fechar_p != "") {
    print "<script language='javascript'>open(location, '_self').close();</script>";
  }
  if ($buscar) {
  }


  $id    = $_POST['id'];


  //DADOS PARAMETROS
  $rel_parametro = $dados->buscaParametro();

  $valor = $rel_parametro->valor;
  $dia_ini = $rel_parametro->dia_ini;
  $dia_fim = $rel_parametro->dia_fim;


  if ($novo != "") {
    print "
		<script language='javascript'>
		window.location.href='celular_funcionario.php';
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
    <title>Celular - Funcionários/Agregados</title>
    <link rel="stylesheet" href="../SCI/css/bootstrap.css">
    <link rel="stylesheet" href="../SCI/css/estilo_bts.css">
    <link rel="stylesheet" href="css/relsm.css">
    <link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="../SCI/js/datatables/datatables.css" />
    <link rel="stylesheet" type="text/css" href="https://nightly.datatables.net/buttons/css/buttons.dataTables.min.css" />
    <script src="../SCI/js/jquery.js"></script>
    <script src="../SCI/js/bootstrap.js"></script>
    <script src="../SCI/js/jquery.ajaxQueue.js"></script>
    <script src="../SCI/js/jquery.mask.js"></script>
    <script type="text/javascript" src="../SCI/js/datatables/datatables.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

    <!--     <script type="text/javascript" language="javascript" src="https://nightly.datatables.net/buttons/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript" src="https://nightly.datatables.net/buttons/js/buttons.html5.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://nightly.datatables.net/buttons/js/buttons.print.min.js"></script>
 -->
    <style>
      /*#myTable3 {
        table-layout: fixed;
      }*/

      #myTable3 th:nth-child(5) {
        width: 100px !important;
      }
    </style>

    </script>

  </head>


  <body style="background-color: #3CB371">
    <div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
      <div class="panel-heading panel-legend" id="painel-heading"><b>Celular - Funcionários / Agregados</b></div>
    </div>
    <br>
    <div class="col-xs-11">
      <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-12 panel-padrao">
          <div class="panel-body col-xs-12">
            <div class="table-responsive covre">

              <table width="109%" class="table table-bordered tb-padrao">
                <tbody>

                  <tr>
                    <td width="11" class="no-border left altura">&nbsp;</td>
                    <td width="370" class="no-border left altura">
                      <font size="-1"><b>Nome Responsável: </b></font>
                      <input type='button' class='btn-xs btn-warning' value='Trocar Responsável' id='trocar_resp_label' name='trocar_resp_label' style='display:none; margin-left:5px;' onClick="javascript:trocar_responsavel()">
                      <span class="input-group date form_datetime col-md-2">
                        <input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='id'
                          id='id' size='9' maxlength='10' value='<?php print $id ?>'>
                        <input type='hidden' name='id_antigo' id='id_antigo' value=''>
                        <input type='hidden' name='data_fim_antigo' id='data_fim_antigo' value=''>
                      </span>
                    </td>
                    <td width="161" class="no-border left altura">
                      <font size="-1"><b>Nº Celular: </b></font>
                    </td>
                    <td width="256" class="no-border left altura">
                      <font size="-1"><b>Aparelho: </b></font>
                    </td>
                    <td align="left" class="no-border left altura">
                      <font size="-1"><b>Data Início: </b></font>
                    </td>
                    <td width="126" align="left" class="no-border left altura">
                      <font size="-1"><b>Data Fim: </b></font>
                    </td>
                    <td width="16" rowspan="2" class="no-border left altura">&nbsp;</td>
                  </tr>

                  <tr>
                    <td class="no-border left altura">&nbsp;</td>
                    <td class="no-border left altura">
                      <div class="col-xs-11 left-inline">
                        <input type='text' autocomplete="stop" class='form-control txt-center altura-campo busca-box' aria-describedby='basic-addon1' name='responsavel' id='responsavel' value='<?php print $responsavel ?>' size='50' maxlength='100' onkeyup="passaParametro1(this)">
                        <span class="col-xs-4 left-inline">
                          <input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='pessoa_id'
                            id='pessoa_id' value='<?php print $pessoa_id ?>' size='8' maxlength='4' onkeypress='somente_numero(this)'>
                          <input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='corp_pessoa_id'
                            id='corp_pessoa_id' value='<?php print $corp_pessoa_id ?>' size='8' maxlength='4' onkeypress='somente_numero(this)'>
                        </span>
                        <div id="suggesstion-box"></div>
                      </div>
                    </td>
                    <td class="no-border left altura">
                      <div class="col-xs-9 left-inline">
                        <?php
                        $query = "select celular.id,
														'('+substring(num_celular,1,2)+')'+substring(num_celular,3,5)+'-'
														+substring(num_celular,8,4) celular
														from celular_covre celular with (nolock)
														join linha_disponivel_estoque linha with (nolock) on
															linha.linha_id = celular.id
														where celular.status_id = 'a'
														and linha.status_id = 'a'
														and linha.estoque_id in ($estoque_id)
														and celular.id not in (select celular_id
																			   from linha_aparelho_funcionario with (nolock)
																			   where linha_aparelho_funcionario.status_id = 'a'
																			   and celular_id = celular.id)
														order by celular";
                        //print "<pre>".$query."</pre>";
                        $result = $con->executar($query);

                        print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1'
													name='celular_covre_id' id='celular_covre_id' value='$celular_covre_id'><option></option>";

                        while (odbc_fetch_row($result)) {

                          if (odbc_result($result, 1) == $pessoa_id)
                            $selected = "selected='selected'";
                          else
                            $selected = "";

                          print "<option value='" . odbc_result($result, 1) . "'$selected>" . utf8_encode(odbc_result($result, 2)) . "</option>";
                        }
                        print "</select>";
                        ?>
                      </div>
                    </td>
                    <td class="no-border left altura">
                      <div class="col-xs-11 left-inline">
                        <?php
                        $query = "select aparelho.id,
														marca.descricao+' '+modelo.descricao+' IMEI: '+aparelho.imei1 aparelho
														/*marca.descricao+' '+modelo.descricao+' S/N: '+aparelho.serie aparelho*/
														from aparelho_celular aparelho with (nolock)
														join modelo_aparelho_celular modelo with (nolock) on
															modelo.id = aparelho.modelo_id
														join marca_celular marca with (nolock) on
															marca.id = modelo.marca_id
														join aparelho_disponivel_estoque aparelho_estoque with (nolock) on
															aparelho_estoque.aparelho_id = aparelho.id
														where aparelho.status_id = 'a'
														and aparelho_estoque.status_id = 'a'
														and aparelho_estoque.estoque_id in ($estoque_id)
														and aparelho.id not in (select aparelho_id
																			   from linha_aparelho_funcionario with (nolock)
																			   where linha_aparelho_funcionario.status_id = 'a'
																			   and aparelho_id = aparelho.id)
													    order by aparelho";
                        //print $query;
                        $result = $con->executar($query);

                        print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1'
													name='aparelho_id' id='aparelho_id' value='$aparelho_id'><option></option>";

                        while (odbc_fetch_row($result)) {

                          if (odbc_result($result, 1) == $aparelho_id)
                            $selected = "selected='selected'";
                          else
                            $selected = "";

                          print "<option value='" . odbc_result($result, 1) . "'$selected>" . utf8_encode(odbc_result($result, 2)) . "</option>";
                        }
                        print "</select>";
                        ?>
                      </div>
                    </td>
                    <td width="135" class="no-border left altura">
                      <div class="col-xs-9 left-inline ">
                        <div class="input-group date form_datetime col-md-12" data-date-format="dd/mm/yyyy">
                          <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data_ini'
                            id='data_ini' size='9' maxlength='10' value='<?php print $data_ini ?>' readonly>
                          <span class="input-group-addon altura-campo"> <span class="glyphicon glyphicon-calendar"></span> </span>
                        </div>
                      </div>
                    </td>
                    <td class="no-border left altura">
                      <div class="col-xs-9 left-inline ">
                        <div class="input-group date form_datetime col-md-12" data-date-format="dd/mm/yyyy">
                          <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data_fim'
                            id='data_fim' size='9' maxlength='10' value='<?php print $data_fim ?>' readonly>
                          <span class="input-group-addon altura-campo"> <span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="no-border left altura">&nbsp;</td>
                    <td colspan="3" class="no-border left altura">&nbsp;</td>
                    <td class="no-border left altura">&nbsp;</td>
                    <td class="no-border left altura">&nbsp;</td>
                    <td class="no-border left altura">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="no-border left altura">&nbsp;</td>
                    <td colspan="3" class="no-border left altura">
                      <font size="-1"><b>Observação: </b></font>
                    </td>
                    <td class="no-border left altura">
                      <font size="-1"><b>Centro Gasto (opcional): </b></font>
                    </td>
                    <td rowspan="2" class="no-border left altura">
                      <div align="center">
                        <input type='button' class='btn-sm btn-primary' value='Gravar' id='gravar' name='gravar'
                          onClick="javascript:grava_registro_aparelho_funcionario('<?php print $dia_ini ?>','<?php print $dia_fim ?>')">
                        <br><br>
                        <input type='button' class='btn-sm btn-warning' value='Trocar Responsável' id='trocar_resp' name='trocar_resp' style='display:none;'
                          onClick="javascript:trocar_responsavel()">
                      </div>
                    </td>
                    <td class="no-border left altura">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="no-border left altura">&nbsp;</td>
                    <td colspan="3" class="no-border left altura">
                      <div class="col-xs-11 left-inline">
                        <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='observacao'
                          id='observacao' value='<?php print $observacao ?>' size='7' maxlength='200'>
                      </div>
                    </td>
                    <td class="no-border left altura">
                      <div class="col-xs-7 left-inline">
                        <input type='text' class='form-control txt-center altura-campo only-number' aria-describedby='basic-addon1' name='cod_ccusto' id='cod_ccusto' value='<?php print $cod_ccusto ?>' size='4' maxlength='13' style="width: 140px;"> <!-- onblur="javascript:verifica_permissao_cc(this)"> -->
                      </div>
                    </td>
                    <td class="no-border left altura">&nbsp;</td>
                  </tr>
                </tbody>
              </table>

            </div>
            <p></p>
            <p></p>
            <div id="exibe_resultado" class="table-responsive">
              <?php

              $relatorio_celular = $dados->exibeCelular();

              $rel_celular = $relatorio_celular->celular;

              print $rel_celular;

              ?>
            </div>



          </div>
          <div class="panel-body col-xs-12"></div>
          <div class="panel-body col-lg-12">
            <div class="row">
            </div>
            <button type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p">Fechar</button>


          </div>
        </div>
    </div>
    </form>





    </div>
    <script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
    <script type="text/javascript" src="js/main.js?v=5"></script>
    <script type="text/javascript" src="../SCI/js/funcoes.js"></script>

    <script type="text/javascript">
      $(document).ready(function() {
        order_aparelho_funcionario();
      });
    </script>


  </body>

  </html>
<?


  include("../SCI/includes/modal.php");
  include("../SCI/includes/modal_list.php");
  include("modal/modal_historico.php");
}
?>