<!DOCTYPE html>
<html lang="en">
<head>

  <?
  session_name("covre_ti");
  session_start();

  include ($_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
  require ("../SCI/includes/page_func_sci.php");
  require_once "../SCI/class/Autentica.php";
  require_once "class/Dados.php";
  //require_once "class/GravaLog.php";
  $autenticacao_sci = new Autentica();

  $con = new ConexaoSCI();

  $dados = new Dados;
  //$logs = new GravaLog;
  $localItem = "../celulares_covre/cadastro_aparelho.php";
  $logado = $_SESSION["usuario_logado"];
  $dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem, $logado);
  $acesso = $dados_autenticacao->permissao;
  $id_item = $dados_autenticacao->id_item;
  $usuario_id = $dados_autenticacao->usuario_id;
  $nome_usuario = $dados_autenticacao->nome_usuario;

  //$acesso = "permitido";


  if ($acesso <> "permitido")
  {
    print "<script language = 'JavaScript'>alert('Acesso negado para esta página');</script/>";

    $dados_gravacao = $autenticacao_sci->redirecionaLogin();
    $local = $dados_gravacao->local;

    print "<script language = 'JavaScript'>window.location='$local';</script/>";
  }
  else
  {
    $dados_gravacao = $autenticacao_sci->gravaAutenticacao($id_item, $usuario_id);

    /** Buttons */
    if ($fechar_p != "")
    {
      print "<script language='javascript'>open(location, '_self').close();</script>";
    }
    if ($buscar)
    {
    }

    $id = $_POST['id'];

    if ($novo != "")
    {
      print "
      <script language='javascript'>
      window.location.href='cadastro_aparelho.php';
      </script>
      ";
    }

    ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cadastro - Aparelho Celular</title>
    <link rel="stylesheet" href="../SCI/css/bootstrap.css">
    <link rel="stylesheet" href="../SCI/css/estilo_bts.css">
    <link rel="stylesheet" href="css/relsm.css">
    <link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="../SCI/js/datatables/datatables.css"/>
    <link rel="stylesheet" type="text/css" href="https://nightly.datatables.net/buttons/css/buttons.dataTables.min.css"/>
  </head>




  <script language="javascript">
function email_rel_794(email, id, tipo){
    // Permite email vazio apenas para exclusão (id > 0)
    if(email != "" || id > 0){
        $.ajax({
            type: "POST",
            url: "includes/email_controle_trafego.php",
            data: { email: email, id: id, tipo: tipo },
            success: function(msg){
                if(msg.indexOf("sessao expirada") == -1){
                    var dados = msg.split('|');
                    var n = msg.split('|').length;
                    var contador = n - 1;
                    
                    var email_input = "";
                    for(i=0; i<contador; i++){
                        if(dados[i].indexOf("pertence") != -1){
                            var dados_pertence = dados[i].split(':');				
                            alert("O email: '"+dados_pertence[1]+"' nao pertence ao dom\u00ednio da Covre");
                            email_input += dados_pertence[1] + ";";
                        }
                        
                        if(dados[i].indexOf("inserido") != -1){
                            var dados_inserido = dados[i].split(':');
                            alert("O email '"+dados_inserido[1]+"' ja foi inserido");
                            email_input += dados_inserido[1] + ";";
                        }
                    }
                    
                    // Se houve mensagens (alertas), armazena os e-mails rejeitados no campo oculto
                    if(email_input != ""){
                        document.getElementById('email_input17_hidden').value = email_input;
                    }
                    // Recarrega a página enviando o formulário (e o campo oculto)
                    document.form1.submit();
                }else{
                    alert("Sessao expirada, favor logar novamente");
                }
            }
        });
    }else{
        alert("Favor inserir um e-mail");
    }
}

</script>

<script language="javascript">
function enviar_relatorio(){
    if(confirm("Deseja enviar o relatório do mês anterior para todos os e-mails cadastrados?")){
        $.ajax({
            type: "POST",
            url: "includes/agendar_envio.php",
            dataType: "text",
            beforeSend: function(){
                // Desabilita o botão usando .attr() (compatível com jQuery antigo)
                $("#enviar_relatorio1").attr("disabled", "disabled").val("Enviando...");
            },
            success: function(msg){
                alert(msg);
                // Reabilita o botão
                $("#enviar_relatorio1").removeAttr("disabled").val("Enviar relatório");
            },
            error: function(){
                alert("Erro de comunicação com o servidor.");
                $("#enviar_relatorio1").removeAttr("disabled").val("Enviar relatório");
            }
        });
    }
}



</script>


  <body style="background-color: #3CB371" >
    <div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
      <div class="panel-heading panel-legend" id="painel-heading"><b>Relatório 794</b></div>
    </div>
    <br>
    <div class="col-xs-10">
      <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-12 panel-padrao">
          <div class="panel-body col-xs-12">
            <div class="table-responsive covre">
              <table width="83%" class="table table-bordered tb-padrao">
               





              <table width="524" border="0">
    <tr>
        <td class="txt_home"><font color="#0000FF" size="-1"><b>E-Mail envio relatorio</b></font></td>
    </tr>
</table>
<table border="1">
    <tr>
        <td>
            <table width="500"  class="table table-bordered tb-padrao">
                <tr>
                    <td>
                        <input type="text" name="email17" id="email17" size="68" maxlength="200" value="<?php echo $email_input17; ?>" />
                    </td>
                    <td>
                        <input type="button" name="inserir" id="inserir" class="botao_site" value="Inserir" 
                            onclick="email_rel_794(document.form1.email17.value, 0, 7)"/>
                    </td>
                </tr>
            </table>
            
            <table width="500" border="1">
                <tr>
                    <td width="450" bgcolor="#999999"><font size="2"><b>E-mail</b></font></td>
                    <td width="50" bgcolor="#999999">&nbsp;</td>
                </tr>
                
                <?php
                $query_email = "SELECT id, email FROM email_rel_794";
                $result_email = odbc_exec($conSQL, $query_email) or die("Erro7 ao pesquisar emails");
                
                while(odbc_fetch_row($result_email))
                {
                    $email_id = odbc_result($result_email, 1);
                    $email    = odbc_result($result_email, 2);
                    
                    print "<tr>
                            <td width='450' bgcolor='#FFFFFF'>
                                <font size='2'>$email</font>
                            </td>";
                    
                    // O botão de exclusão agora chama a mesma função, passando id > 0
                    $email_js = htmlspecialchars($email, ENT_QUOTES); // evita quebra no JavaScript
                    print "<td width='50' bgcolor='#FFFFFF' align='center'>
                            <img src='../images/excluir.png' title='Excluir e-mail' style='cursor:pointer;' width='30' height='25'
                                onclick=\"email_rel_794('".$email_js."', $email_id, 7)\"/>
                           </td>";
                    
                    print "</tr>";
                }
                ?>
            </table>
        </td>
    </tr>
</table>


<table width="511" border="0">
    <tr>
        <td align="center" style="padding-top:10px;">
            <input type="button" name="enviar_relatorio1" id="enviar_relatorio1" class="botao_site" 
                   value="Enviar relatório" onclick="enviar_relatorio()"/>
        </td>
    </tr>
</table>

<!-- Campo oculto para preservar os e-mails rejeitados nos alertas -->
<input type="hidden" name="email_input17" id="email_input17_hidden" value="" />

<table width="511" border="0">
    <tr><td class="txt_home">&nbsp;</td></tr>
</table>
<table width="511" border="0">
    <tr><td class="txt_home">&nbsp;</td></tr>
</table>
<table width="524" border="0">
    <tr><td class="txt_home">&nbsp;</td></tr>
    <tr>
        <td class="txt_home"><div align="center">
            <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />
        </div></td>
    </tr>
</table>

</fieldset>
</form>


</div>
<script src="../SCI/js/jquery.js"></script>
<script src="../SCI/js/bootstrap.js"></script>
<script src="../SCI/js/jquery.ajaxQueue.js"></script>
<script src="../SCI/js/jquery.mask.j    s"></script>
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
<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/main.js"></script>

<script type="text/javascript">
$(document).ready(function() {
  order_aparelho();
});
</script>

<style media="screen">
  .dataTables_wrapper{
    position:static !important;
  }
</style>

</body>
</html>
<?

include ("../SCI/includes/modal.php");
include ("../SCI/includes/modal_list.php");
include ("modal/modal_historico.php");

}
?>
