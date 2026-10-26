<?
session_name("covre_ti");
session_start();


include($_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
require("../SCI/includes/page_func_sci.php");
require_once "../SCI/class/Autentica.php";
require_once "class/Dados.php";


$autenticacao_sci = new Autentica();	

$con = new ConexaoSCI();

$dados = new Dados;	


$localItem = "../celulares_covre/celular_aparelho_funcionario.php";
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
	

	$id		= $_GET['id'];
	
	
	$protocolo_entrega = $dados->buscaDadosProtocolo($id);  
	
	$num_celular = $protocolo_entrega->num_celular;
	$aparelho = $protocolo_entrega->aparelho;
	$responsavel = $protocolo_entrega->responsavel;
	$depto = $protocolo_entrega->depto;
	$data_atual = $protocolo_entrega->data_atual;					
	$data_inicio = $protocolo_entrega->data_inicio;	
	$observacao = $protocolo_entrega->observacao;					
	
	if ($obs_linha != '')
		$obs_linha = "OBSERVAÇÃO: ".$observacao;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Protocolo de Entrega</title>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="../SCA/includes/calendario/_scripts/jquery.click-calendario-1.0-min.js"></script>		
	<script type="text/javascript" src="../SCA/includes/calendario/_scripts/exemplo-calendario.js"></script>

	<link href="../SCA/includes/calendario/_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../SCI/js/datatables/datatables.css"/>
    

    <script src="../SCI/js/bootstrap.js"></script>

    <script type="text/javascript" src="../SCI/js/datatables/datatables.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script> 
    <script type="text/javascript" src="../SCI/js/jquery.mask.js"></script>

		<style type='text/css'>
		  .botao_site_1 {
			font-family: Verdana;
			font-size: 15px;
			font-style: normal;
			font-weight: normal;
			font-variant: normal;
			color: #FFFFFF;
			margin: 2px;
			padding: 0px;
			height: 30px;
			width: auto;
			border: 1px solid #FFFFFF;
			background-color:#095D92;
			text-align:center;
			vertical-align:middle;
			cursor:default;
		}
		</style>	


<script language='javascript'>
function imprimi_some()
{
	//alert(1);
	$('#botao_imprimir').hide(); 
	$('#botao_fechar').hide(); 

	self.print();
	open(location, '_self').close();
}
</script>	


</head>


<form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
  <table width="877" border="0" cellspacing=0 cellpadding=2 bordercolor="666633">
  	<tr>
    	<td width="138"><img src="../SCI/images/logo_covre.jpg" width="156" height="50"></td>
        <td width="583"><div align="center"><font size="+2"><b>PROTOCOLO DE ENTREGA</b></font></div></td>
        <td width="144">&nbsp;</td>
    </tr>
  </table>      
<p></p>
  <table width="876" border="1" cellspacing=0 cellpadding=2 bordercolor="666633">
  	<tr>
    	<td width="65" height="40"><div align="left"><font size="+1">DE :</font></div></td>
    	<td width="456"><div align="left"><font size="+1"><?php print $nome_usuario ?></font></div></td>
        <td width="335"><div align="left"><font size="+1">Data: <?php print $data_atual ?></font></div></td>
    </tr>
  	<tr>
    	<td height="40"><div align="left"><font size="+1">PARA: </font></div></td>
    	<td><div align="left"><font size="+1"><?php print $responsavel ?></font></div></td>
        <td><div align="left"><font size="+1">Depto: <?php print $depto ?></font></div></td>
    </tr>    
  </table>   
  <p></p>
  <table width="874" height="126" border="1" cellspacing=0 cellpadding=2 bordercolor="666633">
  	<tr>
    	<td width="160" height="40"><div align="center"><font size="+1"><b>Nº Documento</b></font></div></td>
    	<td width="509"><div align="center"><font size="+1"><b>Título do Documento</b></font></div></td>
        <td width="185"><div align="center"><font size="+1"><b>Data da Entrega</b></font></div></td>
    </tr>
  	<tr height="700">
    	<td valign="top"><p align="center"><font size="+1"><br>01</font></p></td>
    	<td valign="top"><p align="left"><font size="+1"><br><?php print $num_celular ?><p><p><?php print $aparelho ?><p><p><?php print $observacao ?></font></p></td>
        <td valign="top"><p align="center"><font size="+1"><br><?php print $data_inicio ?></font></p></td>
    </tr>    
  </table>           
  <p></p>
  <table width="876" border="1" cellspacing=0 cellpadding=2 bordercolor="666633">
  	<tr>
    	<td width="51"  height="37"><div align="left"><font size="+1">Nome e Assinatura do Responsável (Destinatário):	</font></div></td>
    </tr>    
  	<tr>
    	<td width="51" height="37">&nbsp;</td>
    </tr>    
  </table>   
  <p></p>
  
  <table width="876" border="0" cellspacing=0 cellpadding=2 bordercolor="666633">
  	<tr>
    	<td width="51" height="40"><div align="right">F.92.01 </div></td>
    </tr>    
  </table>   
    <div id='botao_imprimir'>
    <table  width="876" border='0' align='center'>
        <tr>
            <td><center><input name='imprimir' type='button' class='botao_site_1' id='imprimir' value=' Imprimir ' 
                onclick='imprimi_some()' value='' maxlength='14'/></center>
            </td>			
        </tr>
    </table>
    </div>  
</form>


 


</div>
<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript" src="../SCI/js/funcoes.js"></script>




</body>
</html>
<? 

	
} 
?>