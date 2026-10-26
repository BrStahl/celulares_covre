<?
session_name("covre_ti");
session_start();


include($_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
require("../SCI/includes/page_func_sci.php");
require_once "../SCI/class/Autentica.php";
require_once "class/Dados.php";
require_once "class/Estoque.php";

$autenticacao_sci = new Autentica();	

$con = new ConexaoSCI();

$dados = new Dados;	
$estoque = new Estoque;	

$localItem = "../celulares_covre/estoque_aparelho.php";
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


	$dados_estoque = $estoque->buscaEstoqueResponsavel($usuario_id);
	$estoque_id 	= $dados_estoque->estoque_id;
	$administrador  = $dados_estoque->administrador;


	if ($administrador == 'S')
	{
		$condicao1 = "";
		$condicao2 = "";
	}
	else
	{
		$condicao1 = "and id = $estoque_id";
		$condicao2 = "and id not in (1) ";
	}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Estoque - Linhas / Aparelhos</title>

	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<!-- <script src="http://code.jquery.com/jquery-1.9.1.js"></script> -->
    <!-- <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script> -->
    <script src="../SCI/js/jquery.js"></script>
    <script type="text/javascript" src="../SCA/includes/calendario/_scripts/jquery.click-calendario-1.0-min.js"></script>		
	<script type="text/javascript" src="../SCA/includes/calendario/_scripts/exemplo-calendario.js"></script>

    <link rel="stylesheet" href="../SCI/css/bootstrap.css">
	<link rel="stylesheet" href="../SCI/css/estilo_bts.css">
    <link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

	<link href="../SCA/includes/calendario/_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css"/>


    <script src="../SCI/js/bootstrap.js"></script>
    
<script language="javascript">
function grava_registro(){
	//alert();
		
		var aparelhos 			= document.form1.aparelho_enviado.value;
		var linhas	 			= document.form1.celular_enviado.value;		
		var estoque_id 			= document.form1.estoque_id.value;
		var estoque_destino_id 	= document.form1.estoque_destino_id.value;
		
		
		if (aparelhos.substring(0, 1) == ',')
			aparelhos = aparelhos.substring(1, 1000);
			
		if (linhas.substring(0, 1) == ',')
			linhas = linhas.substring(1, 1000);			
			
		
		if ((aparelhos == '') && (linhas == ''))
		{
			$('#msg').html('Favor selecionar o(s) aparelho(s) ou linha(s)');
			$('#myModal_alert').modal('show');
		}			
		else
		if (estoque_destino_id == '')
		{
			$('#msg').html('Favor selecionar o estoque destino');
			$('#myModal_alert').modal('show');
		}	
		else
		if (estoque_id == estoque_destino_id)
		{
			$('#msg').html('Favor selecionar um estoque diferente do estoque origem');
			$('#myModal_alert').modal('show');
		}			
		else
		{

			$.ajax({
				type: "POST", //define o metуdo de passagem de parametros
				url: "includes/transf_linha_aparelho_estoque.php", //chama uma pagina
				data:"aparelhos="+aparelhos + "&estoque_id="+estoque_id + "&estoque_destino_id="+estoque_destino_id + "&linhas="+linhas,
				success: function (msg) {
					//alert(msg);
												
					if(msg.indexOf("ok") == -1)
					{
						$('#msg').html(msg);
						$('#myModal_alert').modal('show');
					}
					else
					{
						
						$('#msg_success').html('Transferência efetuada com sucesso');
						$('#myModal_alert_sucess').modal('show');						
						
						document.getElementById('aparelho_enviado').value = '';
						document.getElementById('celular_enviado').value = '';
						document.getElementById('estoque_destino_id').value = '';
						
						busca_aparelho_estoque(estoque_id);
					}						
					
					
				}
			});
			
		}
	
}
</script>



    
</head>
<body>
    <div class="panel-primary col-lg-5" style="max-height: 30px;">
        <div class="panel-heading panel-legend"><b>Estoque - Linhas / Aparelhos</b></div>
    </div>
    <br>
<div class="col-lg-12">
    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-8 panel-padrao">
            <div class="panel-body col-xs-12">    
                
              <div id="exibe_resultado">
                	<?php
					
						$aparelho_estoque = $estoque->buscaAparelhoEstoque($estoque_id);
						$relacao_aparelho	= $aparelho_estoque->table;                
				
						$linha_estoque = $estoque->buscaLinhaEstoque($estoque_id);
						$relacao_linha	= $linha_estoque->table;   
					
						print $relacao_aparelho."<p><hr style='border-color:black'></p>".$relacao_linha;
						
					?>                
              </div>                         
                                <div class="table-responsive covre">
        
                      <table width="83%" class="table table-bordered tb-padrao">
                        <tbody>
                           
                           	<tr>
                            	<td width="41" class="no-border left altura">&nbsp;</td>
								<td width="389" class="no-border left altura">
									<font size="-1"><b>De: </b></font> 
                                  <label for="aparelho_enviado"></label>
                                <input name="aparelho_enviado" type="hidden" id="aparelho_enviado" size="50"></td>  
								<td width="470" class="no-border left altura"><font size="-1"><b>Para: 
								  <input name="celular_enviado" type="hidden" id="celular_enviado" size="50">
								</b></font></td>
								<td width="75" class="no-border left altura">&nbsp;</td>                                  
                            </tr>
                           	<tr>
                           	  <td class="no-border left altura">&nbsp;</td>
                           	  <td class="no-border left altura"><div class="col-xs-11 left-inline"><span class="col-xs-10 left-inline">
                           	    <?php
                                            $query = "select id, descricao
														from estoque_aparelho_celular with (nolock)
														where status_id = 'a'
														$condicao1
														order by descricao";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='marca_id' id='estoque_id' value='$estoque_id' onchange='javascript:busca_aparelho_estoque(this.value)'>";
                                    
                                            while(odbc_fetch_row($result))
                                            {
                                    
                                                if (odbc_result($result, 1) == $estoque_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";
                                    
                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
                       	      </span></div></td>
                           	  <td class="no-border left altura"><div class="col-xs-11 left-inline"><span class="col-xs-10 left-inline">
                           	    <?php
                                            $query = "select id, descricao
														from estoque_aparelho_celular with (nolock)
														where status_id = 'a'
														$condicao2
														order by descricao";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='estoque_destino_id' id='estoque_destino_id' value='$estoque_destino_id'><option></option>";
                                    
                                            while(odbc_fetch_row($result))
                                            {
                                    
                                                if (odbc_result($result, 1) == $estoque_destino_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";
                                    
                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
                       	      </span></div></td>
                           	  <td class="no-border left altura"><input type='button' class='btn-sm btn-primary' value='Gravar' id='gravar' name='gravar' onClick='javascript:grava_registro()'></td>
                       	  </tr>    
                            </tbody>
                    </table>
                   
                </div>
                  
                
                
                <center>
                <input type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p" value="Fechar">
              </center>
                                  
            </div>  
        </div> 
        
        
        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content col-lg-12">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                    </div>
                <div class="modal-body">
                <table class="table table-bordered table-hover" id="myTable4">
                    <thead>
                        <tr>
                            <th bgcolor="#d3d3d3"></a></th>
                            <th bgcolor="#d3d3d3"></a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="bg-default" id=""></td>
                            <td class="bg-default" id=""></td>
                        </tr>
                    </tbody>
                </table>
                </div>
                    <div class="modal-footer">

                        <button type='button' class='btn btn-danger' data-dismiss='modal'>Fechar</button>
					 
						
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>
<?php
	
	include("../SCI/includes/modal.php");
	



}
?>
							