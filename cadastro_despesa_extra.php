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

$localItem = "../celulares_covre/cadastro_despesa_extra.php";
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
    <title>Cadastro - Despesas Extras</title>
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


<script>    

function order() {

    $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss');    //Formatação com Hora
    $.fn.dataTable.moment('DD/MM/YYYY');    //Formatação sem Hora
	
    $('#myTable3').DataTable({
	dom: 'Bfrtip',
	buttons: [
		{
			extend: 'excel',
			text: '<i class="fa fa-files-o">Excel</i>',
			title:  function () {
				var d = new Date();
				var n = d.getSeconds();
				return "Celulares Covre"
			}, customize: function (xlsx) {
				var sheet = xlsx.xl.worksheets['sheet1.xml'];
				$('row:first c', sheet).attr('s', '2');
			}
		},
		// { extend: 'print', text:'<i class="fa fa-files-o">Imprimir</i>' },
	],			
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
		
		var id					= document.getElementById('id').value;
		var celular_covre_id 	= document.getElementById('celular_covre_id').value;
		var data				= document.getElementById('data').value;
		var valor		 	 	= document.getElementById('valor').value;
		var observacao 			= document.getElementById('observacao').value;

		
		if (celular_covre_id == '')
		{
			$('#msg').html('Favor selecionar o número do celular');
			$('#myModal_alert').modal('show');
		}			
		else
		if (data == '')
		{
			$('#msg').html('Favor informar a data');
			$('#myModal_alert').modal('show');
		}	
		else
		if (valor == '')
		{
			$('#msg').html('Favor informar o valor');
			$('#myModal_alert').modal('show');
		}	
		else		
		{

			let dados = $("form").serialize();

			$.ajax({
				type: "POST", //define o metуdo de passagem de parametros
				url: "includes/grava_despesa_extra.php", //chama uma pagina
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
						document.getElementById('celular_covre_id').value = '';
						document.getElementById('data').value = '';
						document.getElementById('valor').value = '';
						document.getElementById('observacao').value = '';
						
						busca_celulares_despesa();
						
						exibe_despesas_extras();
					}						
					
					
				}
			});
			
		}
	
}
</script>


</head>


<body style="background-color: #3CB371" >
<div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
        <div class="panel-heading panel-legend" id="painel-heading"><b>Cadastro - Despesas Extras</b></div>
    </div>
    <br>
<div class="col-xs-10">
    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-11 panel-padrao">
            <div class="panel-body col-xs-12">
                <div class="table-responsive covre">
                
                    <table width="83%" class="table table-bordered tb-padrao">
                        <tbody>
                           
                           	<tr>
                            	<td width="36" class="no-border left altura">&nbsp;</td>
								<td width="211" class="no-border left altura">
									<font size="-1"><b>Número Celular: </b></font> 
                                </td>  
								<td width="165" align="left" class="no-border left altura">
									<font size="-1"><b>Data: </b></font> 
                                </td>
								<td width="149" class="no-border left altura">
									<font size="-1"><b>Valor: </b></font>
                                </td>
								<td width="566" class="no-border left altura"><font size="-1"><b>Observação: </b></font></td>
								<td width="91" rowspan="2" class="no-border left altura"><input type='button' class='btn-sm btn-primary' value='Gravar' id='gravar' name='gravar' onClick='javascript:grava_registro()'></td>
								<td width="21" rowspan="2" class="no-border left altura">&nbsp;</td>                                  
                            </tr>    
                            
                            <tr>  
	                            <td class="no-border left altura">&nbsp;</td>                            
								<td class="no-border left altura"><div class="col-xs-9 left-inline">
								  <?php
											$query = "select celular.id,
														'('+substring(num_celular,1,2)+')'+substring(num_celular,3,5)+'-'
														+substring(num_celular,8,4) celular
														from celular_covre celular with (nolock)
														where celular.status_id = 'a'
														order by celular";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' 
													name='celular_covre_id' id='celular_covre_id' value='$celular_covre_id'><option></option>";
                                    
                                            while(odbc_fetch_row($result))
                                            {
                                    
                                                if (odbc_result($result, 1) == $pessoa_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";
                                    
                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
							    </div>
								  <span class="col-xs-4 left-inline">
								  <input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='id' 
									id='id' value='<?php print $id ?>' size='8' maxlength='4' onkeypress='somente_numero(this)'>
							    </span></td>                   
								<td  class="no-border left altura" ><div class="col-xs-9 left-inline ">
								  <div class="input-group date form_datetime col-md-12"  data-date-format="dd/mm/yyyy">
								    <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data' 
                                    id='data' size='9' maxlength='10' value='<?php print $data ?>' readonly>
								    <span class="input-group-addon altura-campo"> <span class="glyphicon glyphicon-calendar"></span></span></div>
							    </div></td>
								<td  class="no-border left altura"><div class="col-xs-9 left-inline">
								  <input type='text' class='form-control txt-center altura-campo only-number_virgula' aria-describedby='basic-addon1' name='valor' 
									id='valor' value='<?php print $valor ?>' size='7'>
							    </div></td>
								<td  class="no-border left altura"><div class="col-xs-11 left-inline">
								  <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='observacao' id='observacao' value='<?php print $imei ?>'  maxlength='80'>
							    </div></td>
						  	</tr>   
                       	</tbody>
                    </table>
                </div>
            </div>
            
              <p></p><p></p>
               <div id="exibe_resultado">
                        <?php
                            
                            $relatorio_despesa = $dados->exibeDespesaExtra();  
                            
                            $rel_despesa = $relatorio_despesa->despesa;
                            
                            print $rel_despesa;
                                
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

</body>
</html>
<? 

	
	include("../SCI/includes/modal.php");
	include("../SCI/includes/modal_list.php");
	

	
	
} 
?>