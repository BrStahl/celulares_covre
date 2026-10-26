<?
session_name("covre_ti");
session_start();


include($_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
require("../SCI/includes/page_func_sci.php");
require_once "../SCI/class/Autentica.php";
require_once "class/Dados_old.php";


$autenticacao_sci = new Autentica();	

$con = new ConexaoSCI();

$dados = new Dados;	


$localItem = "../celulares_covre/celular_funcionario.php";
$logado    = $_SESSION["usuario_logado"];
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem,$logado);
$acesso  = $dados_autenticacao->permissao;
$id_item  = $dados_autenticacao->id_item;
$usuario_id  = $dados_autenticacao->usuario_id;

$acesso = "permitido";


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
	

	$id		= $_POST['id'];
	
	
	//DADOS PARAMETROS
	$rel_parametro = $dados->buscaParametro();  

	$valor = $rel_parametro->valor;
	$dia_ini = $rel_parametro->dia_ini;
	$dia_fim = $rel_parametro->dia_fim;
		
	
	if($novo != "")
	{
		print"
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
				return "Celulares Vinculados"
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
function grava_registro(dia_ini, dia_fim){
	//alert(dia_ini);

		var id					= document.getElementById('id').value;
		var pessoa_id			= document.getElementById('pessoa_id').value;
		var celular_covre_id	= document.getElementById('celular_covre_id').value;
		var data_ini			= document.getElementById('data_ini').value;	
		var cod_ccusto			= document.getElementById('cod_ccusto').value;		
		var observacao 	 		= document.getElementById('observacao').value;

		var dia_data = data_ini.substring(0, 2);


		
		if (pessoa_id == '')
		{
			$('#msg').html('Favor informar o responsável');
			$('#myModal_alert').modal('show');
		}			
		else
		if (celular_covre_id == '')
		{
			$('#msg').html('Favor selecionar o número do celular');
			$('#myModal_alert').modal('show');
		}	
		else
		if (data_ini == '')
		{
			$('#msg').html('Favor preencher a data de início');
			$('#myModal_alert').modal('show');
		}	
		else
		if (cod_ccusto == '')
		{
			$('#msg').html('Favor preencher o código do centro de custo');
			$('#myModal_alert').modal('show');
		}				
		else		
		{
			
			
			
			let dados = $("form").serialize();
			
			$.ajax({
				type: "POST", //define o metуdo de passagem de parametros
				url: "includes/grava_celular.php", //chama uma pagina
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
						$('#msg_success').html('Gravação efetuada com sucesso');
						$('#myModal_alert_sucess').modal('show');
						
						document.form1.id.value					= '';
						document.form1.responsavel.value		= '';
						document.form1.responsavel.readOnly 	= false;
						document.form1.pessoa_id.value			= '';
						//busca_agregados();
						busca_celulares();
						document.form1.celular_covre_id.value	= '';
						document.form1.data_ini.value			= '';
						document.form1.data_fim.value			= '';	
						document.form1.cod_ccusto.value			= '';											
						document.form1.observacao.value			= '';
												
						exibe_celular();
						
					}
					
					
				}
			});
			
		}
	
}
</script>


</head>


<body style="background-color: #3CB371" >
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
                            	<td width="33" class="no-border left altura">&nbsp;</td>
								<td width="395" class="no-border left altura">
								<font size="-1"><b>Nome Responsável: <span class="input-group date form_datetime col-md-2">
								<input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='id' 
                                    id='id' size='9' maxlength='10' value='<?php print $id ?>'>
								</span></b></font></td>
								<td width="184" class="no-border left altura"><font size="-1"><b>Nº Celular: </b></font></td>  
								<td align="left" class="no-border left altura"><font size="-1"><b>Data Início: </b></font></td>
								<td width="174" align="left" class="no-border left altura"><font size="-1"><b>Data Fim: </b></font></td>
								<td width="160" align="left" class="no-border left altura"><font size="-1"><b>Cód. Centro Custo: </b></font></td>
								<td width="99" rowspan="2" class="no-border left altura"><div align="right">
								  <input type='button' class='btn-sm btn-primary' value='Gravar' id='gravar' name='gravar' 
								onClick="javascript:grava_registro('<?php print $dia_ini ?>','<?php print $dia_fim ?>')">
							    </div></td>
								<td width="25" rowspan="2" class="no-border left altura">&nbsp;</td>
							</tr>    
                            
                            <tr>  
	                            <td class="no-border left altura">&nbsp;</td>                            
								<td class="no-border left altura"><div class="col-xs-11 left-inline">
								  <input type='text' class='form-control txt-center altura-campo busca-box' aria-describedby='basic-addon1' name='responsavel' id='responsavel' value='<?php print $responsavel ?>' size='50' maxlength='100' onkeyup="passaParametro1(this)" >								  														
								  <span class="col-xs-4 left-inline">
								  <input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='pessoa_id' 
									id='pessoa_id' value='<?php print $pessoa_id ?>' size='8' maxlength='4' onkeypress='somente_numero(this)'>
								  </span>							    
                               <div id="suggesstion-box" ></div>
                               </div></td>
								<td class="no-border left altura"><div class="col-xs-9 left-inline">
								  <?php
											$query = "select celular.id,
														'('+substring(num_celular,1,2)+')'+substring(num_celular,3,5)+'-'
														+substring(num_celular,8,4) celular
														from celular_covre celular with (nolock)
														where celular.status_id = 'a'
														and celular.id not in (select celular_covre_id
																			   from celular_funcionario with (nolock)
																			   where celular_funcionario.status_id = 'a'
																			   and celular_covre_id = celular.id)
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
							    </div></td>                   
							  <td width="181"  class="no-border left altura" ><div class="col-xs-9 left-inline ">
							    <div class="input-group date form_datetime col-md-12"  data-date-format="dd/mm/yyyy">
							      <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data_ini' 
                                    id='data_ini' size='9' maxlength='10' value='<?php print $data_ini ?>' readonly>
							      <span class="input-group-addon altura-campo"> <span class="glyphicon glyphicon-calendar"></span> </span> </div>
						      </div></td>
								<td  class="no-border left altura" ><div class="col-xs-9 left-inline ">
								  <div class="input-group date form_datetime col-md-12"  data-date-format="dd/mm/yyyy">
								    <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data_fim' 
                                    id='data_fim' size='9' maxlength='10' value='<?php print $data_fim ?>' readonly>
								    <span class="input-group-addon altura-campo"> <span class="glyphicon glyphicon-calendar"></span></span></div>
							    </div></td>
								<td  class="no-border left altura" ><div class="col-xs-6 left-inline">
								  <input type='text' class='form-control txt-center altura-campo only-number' aria-describedby='basic-addon1' name='cod_ccusto' id='cod_ccusto' value='<?php print $cpd_ccusto ?>' size='4' maxlength='5' onblur="javascript:verifica_permissao_cc(this)">
							    </div></td>
							</tr>
                            <tr>
                              <td class="no-border left altura">&nbsp;</td>
                              <td colspan="2" class="no-border left altura">&nbsp;</td>
                              <td  class="no-border left altura" >&nbsp;</td>
                              <td  class="no-border left altura" >&nbsp;</td>
                              <td  class="no-border left altura" >&nbsp;</td>
                              <td class="no-border left altura">&nbsp;</td>
                              <td class="no-border left altura">&nbsp;</td>
                            </tr>
                            <tr>
                              <td class="no-border left altura">&nbsp;</td>
                              <td colspan="2" class="no-border left altura"><font size="-1"><b>Observação: </b></font></td>
                              <td  class="no-border left altura" >&nbsp;</td>
                              <td  class="no-border left altura" >&nbsp;</td>
                              <td  class="no-border left altura" >&nbsp;</td>
                              <td class="no-border left altura">&nbsp;</td>
                              <td class="no-border left altura">&nbsp;</td>
                            </tr>
                            <tr>
                              <td class="no-border left altura">&nbsp;</td>
                              <td colspan="5" class="no-border left altura"><div class="col-xs-12 left-inline">
                                <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='observacao' 
									id='observacao' value='<?php print $observacao ?>' size='7'  maxlength='200'>
                              </div></td>
                              <td class="no-border left altura">&nbsp;</td>
                              <td class="no-border left altura">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
              <p></p><p></p>
               <div id="exibe_resultado">
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
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript" src="../SCI/js/funcoes.js"></script>




</body>
</html>
<? 

	
	include("../SCI/includes/modal.php");
	include("../SCI/includes/modal_list.php");
	

	
	
} 
?>