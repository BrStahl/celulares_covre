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


$localItem = "../celulares_covre/parametro_celular.php";
$logado    = $_SESSION["usuario_logado"];
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem,$logado);
$acesso  = $dados_autenticacao->permissao;
$id_item  = $dados_autenticacao->id_item;
$usuario_id  = $dados_autenticacao->usuario_id;

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
		window.location.href='celular_agregado.php';
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
    <title>Parâmetros - Celular Agregados</title>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="../SCA/includes/calendario/_scripts/jquery.click-calendario-1.0-min.js"></script>		
	<script type="text/javascript" src="../SCA/includes/calendario/_scripts/exemplo-calendario.js"></script>

    <link rel="stylesheet" href="../SCI/css/bootstrap.css">
	<link rel="stylesheet" href="../SCI/css/estilo_bts.css">
    <link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

	<link href="../SCA/includes/calendario/_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../SCI/js/datatables/datatables.css"/>

    <script src="../SCI/js/bootstrap.js"></script>
    <script type="text/javascript" src="../SCI/js/datatables/datatables.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script> 
    <script type="text/javascript" src="../SCI/js/jquery.mask.js"></script>
</script>


<script>    

function order() {

    $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss');    //Formatação com Hora
    $.fn.dataTable.moment('DD/MM/YYYY');    //Formatação sem Hora
	
    $('#myTable3').DataTable({
    "language": {
			           "lengthMenu": 'Mostrar <select class="form-control txt-center altura-campo">'+
			             '<option value="10">10</option>'+
			             '<option value="50">50</option>'+
			             '<option value="100">100</option>'+
			             '<option value="500">500</option>'+
			             '<option value="-1">Todos</option>'+
			             '</select> registros'
	},
    columnDefs: [
        {
            type: 'date-br'
        }
    ],
});
}

</script> 



<script language="javascript">
function grava_registro(){
	//alert();

		var valor			= document.getElementById('valor').value;

	
		if ((valor == '') || (valor <= 0))
		{
			$('#msg').html('Favor preencher o valor');
			$('#myModal_alert').modal('show');
		}	
		else
		{
			//alert('aqui');
			
			let dados = $("form").serialize();
			
			$.ajax({
				type: "POST", //define o metуdo de passagem de parametros
				url: "includes/grava_parametro.php", //chama uma pagina
				data: dados, //passa os parametros, se necessбrio
				success: function (msg) {
					//alert(msg);
					
					if(msg.indexOf("ok") == -1)
					{
						$('#msg').html(msg);
						$('#myModal_alert').modal('show');
					}
					else
					{
						window.location.href='parametro_celular.php';
						
					}
					
					
				}
			});
			
		}
	
}
</script>


</head>


<body style="background-color: #3CB371" >
<div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
        <div class="panel-heading panel-legend" id="painel-heading"><b>Parâmetros - Celulares Covre</b></div>
</div>
    <br>
<div class="col-xs-8">
    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-8 panel-padrao">
          <div class="panel-body col-xs-11">
              <div class="table-responsive covre">
                
                    <table width="100%" class="table table-bordered tb-padrao">
                        <tbody>
                           
                           	<tr>
                            	<td width="21" class="no-border left altura">&nbsp;</td>
								<td width="74" class="no-border left altura">
								<font size="-1"><b>Valor: <span class="input-group date form_datetime col-md-2">
								<input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='id' 
                                    id='id' size='9' maxlength='10' value='<?php print $id ?>'>
								</span></b></font></td>  
								<td width="245" align="left" class="no-border left altura"><div class="col-xs-6 left-inline">
								  <input type='text' class='form-control txt-center altura-campo only-number_virgula' aria-describedby='basic-addon1' name='valor' 
									id='valor' value='<?php print $valor ?>' size='7'>
							    </div></td>
								<td width="172" class="no-border left altura"><div align="right">
								  <input type='button' class='btn-sm btn-primary' value='Gravar' id='gravar' name='gravar' 
								onClick='javascript:grava_registro()'>
							    </div></td>
								<td width="36" class="no-border left altura"><button type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p">Fechar</button></td>
								<td width="36" class="no-border left altura">&nbsp;</td>
							</tr>    
                        </tbody>
                    </table>

            </div>

        </div>  
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
	print "<script language='javascript'>busca_parametro();</script>";
	
	include("../SCI/includes/modal.php");
	include("../SCI/includes/modal_list.php");
	

	
	
} 
?>