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

$localItem = "../celulares_covre/relatorio_consumo_mensal.php";
$logado    = $_SESSION["usuario_logado"];
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem, $logado);
$acesso  = $dados_autenticacao->permissao;
$id_item  = $dados_autenticacao->id_item;
$usuario_id  = $dados_autenticacao->usuario_id;

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

  $id = $_POST['id'];

  //DADOS PARAMETROS
  $rel_parametro = $dados->buscaParametro();
  $valor = $rel_parametro->valor;
  $dia_ini = $rel_parametro->dia_ini;
  $dia_fim = $rel_parametro->dia_fim;

  if ($novo != "") {
    print "
    <script language='javascript'>
    window.location.href='relatorio_consumo_mensal.php';
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
  <title>Relatório de Consumo Mensal</title>
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

  <style>
    #myTable3 th:nth-child(5) {
      width: 100px !important;
    }
  </style>
</head>

<body style="background-color: #3CB371">
  <div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
    <div class="panel-heading panel-legend" id="painel-heading"><b>Relatório de Consumo Mensal</b></div>
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
                    <font size="-1"><b>Arquivo: </b></font>
                  </td>
                  <td width="161" class="no-border left altura">
                    <font size="-1"><b>Operadora: </b></font>
                  </td>
                  <td width="256" class="no-border left altura">
                    <font size="-1"><b>Centro de Custo Padrão: </b></font>
                  </td>
                  <td width="16" rowspan="2" class="no-border left altura">&nbsp;</td>
                </tr>

                <tr>
                  <td class="no-border left altura">&nbsp;</td>
                  <td class="no-border left altura">
                    <div class="col-xs-11 left-inline">
                      <input type='file' class='form-control altura-campo' name='arquivo' id='arquivo' accept='.xlsx,.xls'>
                    </div>
                  </td>
                  <td class="no-border left altura">
                    <div class="col-xs-9 left-inline">
                      <select class='form-control txt-center altura-campo' name='operadora' id='operadora'>
                        <option value='vivo' selected>Vivo</option>
                        <option value='claro'>Claro</option>
                        <option value='tim'>Tim</option>
                        <option value='oi'>Oi</option>
                      </select>
                    </div>
                  </td>
                  <td class="no-border left altura">
                    <div class="col-xs-11 left-inline">
                      <input type='text' class='form-control txt-center altura-campo' name='centro_custo_padrao' id='centro_custo_padrao' value='3.10.39.557.569' size='15' maxlength='20'>
                    </div>
                  </td>
                </tr>
                
                <tr>
                  <td class="no-border left altura">&nbsp;</td>
                  <td colspan="2" class="no-border left altura">
                    <div align="center">
                      <input type='button' class='btn-sm btn-primary' value='Upload' id='upload' name='upload' style='margin-right: 10px;'>
                      <input type='button' class='btn-sm btn-success' value='Exportar Excel' id='export_excel' name='export_excel'>
                    </div>
                  </td>
                  <td class="no-border left altura">&nbsp;</td>
                </tr>
                
              </tbody>
            </table>

          </div>
          <p></p>
          <p></p>
          
        </div>
        <div class="panel-body col-xs-12"></div>
        <div class="panel-body col-lg-12">
          <div class="row">
          </div>
          <button type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p">Fechar</button>
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
      // Add your custom JavaScript here
    });
    
    function fechar() {
      window.close();
    }
  </script>

</body>
</html>

<?
  include("../SCI/includes/modal.php");
  include("../SCI/includes/modal_list.php");
  include("modal/modal_historico.php");
}
?>