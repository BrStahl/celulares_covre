<?php

    /* INICIO SESSAO*/
    session_name("covre_ti");
    session_start();
    /* FIM SESSAO */
    

    /* REQUIRES/INCLUDES */
    include($_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
    require("../SCI/includes/page_func_sci.php");
    require_once "../SCI/class/Autentica.php";
	require_once "class/Dados.php";

	$con = new ConexaoSCI();
	$dados = new Dados;	

    /* INSTANCIAS CLASSES*/
    $autenticacao_sci = new Autentica();	
    $control          = new ConexaoSCI();
    
    /* FIM INSTANCIAS CLASSES*/
    
    $localItem  = "../celulares_covre/permissoes.php";
    $logado     = $_SESSION["usuario_logado"];

    $dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem,$logado);
    $acesso             = $dados_autenticacao->permissao;
    $id_item            = $dados_autenticacao->id_item;
    $usuario_id         = $dados_autenticacao->usuario_id;
    $nome_usuario       = $dados_autenticacao->nome_usuario;
    //$acesso             = 'permitido';

//var_dump($permissoes); die();

    
    if($acesso <> "permitido"){
        print "<script language = 'JavaScript'>alert('Acesso negado para esta página');</script/>";    
        $dados_gravacao = $autenticacao_sci->redirecionaLogin();
        $local  = $dados_gravacao->local;    
        print "<script language = 'JavaScript'>window.location='$local';</script/>";
    }
    else
    {
        $dados_gravacao = $autenticacao_sci->gravaAutenticacao($id_item,$usuario_id);
        /* FECHAMENTO DO ELSE NA LINHA 255*/
		
		
	if($novo != "")
	{
			print"
			<script language='javascript'>
				window.location.href='permissoes.php';
			</script>
			";
	}			
	
	
		
?>
<!DOCTYPE html>
<html lang="en">
<style>
    .regrasBoxFlags{
        background-color: rgba(209, 209, 209, 1);
        width: 670px;
        border-radius: 5px;
        padding: 5px;
    }

</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Permissões - Celulares Covre</title>
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


<script>
function busca_usuario_id(elmnt){	
	//alert(elmnt.value);
	
	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/busca_usuario_id.php", //chama uma pagina
		data: "nome="+elmnt.value, //passa os parametros, se necessário
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			
			var dados = msg.split("|");//quebra a string com base em um caracter
		
			if (dados[1] != '')
			{
				document.getElementById('usuario_id').value 	= dados[1];
				document.getElementById('usuario').value 		= dados[2];
			}
			else
			{
				document.getElementById('usuario_id').value 	= '';
				document.getElementById('usuario').value 		= '';		
			}
		}
	});
	
}
</script>


<script language="javascript">
function grava_registro(){
	//alert();
		
		var responsavel_id	 	 	= document.getElementById('responsavel_id').value;
		var usuario_id	 	 		= document.getElementById('usuario_id').value;
		var responsavel_permissao	= document.getElementById('responsavel_permissao').value;
		var adm_id		 	 		= document.getElementById('adm_id').value;
		var gerar_vale_id			= document.getElementById('gerar_vale_id').value;
		var status_id		  		= document.getElementById('status_id').value;
		var estoque_id	 	 		= document.getElementById('estoque_id').value;

		//alert();
		
		if (responsavel_permissao == '')
		{
			$('#msg').html('Favor inserir o nome');
			$('#myModal_alert').modal('show');
		}			
		else
		if (usuario_id == '')
		{
			$('#msg').html('Favor informar um nome válido');
			$('#myModal_alert').modal('show');
		}	
		else
		{

			$.ajax({
				type: "POST", //define o metуdo de passagem de parametros
				url: "includes/grava_responsavel.php", //chama uma pagina
				data: "responsavel_id="+responsavel_id + "&usuario_id="+usuario_id + "&adm_id="+adm_id + "&status_id="+status_id + "&estoque_id="+estoque_id + "&gerar_vale_id="+gerar_vale_id, //passa os parametros, se necessбrio
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
						
						var dados = msg.split("|");//quebra a string com base em um caracter
						
						document.getElementById('responsavel_id').value = dados[2];

					}						
					
					
				}
			});
			
		}
	
}
</script>


<script language="javascript">
function grava_centro_custo(){
	//alert();
		
		var responsavel_id	 	 	= document.getElementById('responsavel_id').value;
		var centro_custo_id	 		= document.getElementById('centro_custo_id').value;


		
		if (centro_custo_id == '')
		{
			$('#msg').html('Favor selecionar o Centro de Custo');
			$('#myModal_alert').modal('show');
		}			
		else
		{

			$.ajax({
				type: "POST", //define o metуdo de passagem de parametros
				url: "includes/grava_centro_custo_responsavel.php", //chama uma pagina
				data: "responsavel_id="+responsavel_id + "&centro_custo_id="+centro_custo_id, //passa os parametros, se necessбrio
				success: function (msg) {
					//alert(msg);
												
					if(msg.indexOf("ok") == -1)
					{
						$('#msg').html(msg);
						$('#myModal_alert').modal('show');
					}
					else
					{
						document.getElementById('centro_custo_id').value = '';
						busca_centro_custo_responsavel(responsavel_id);
					}	
					
					
				}
			});
			
		}
	
}
</script>

<script>
function Modal_usuarios(){
	//alert('inicio');
	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/exibe_responsavel.php", //chama uma pagina
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			$('#resultado_modal').html(msg);
			$('#myModal_usuarios').modal('show');
			
		}
	});
		
}
</script>


</head>
<body>

<div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
    <div class="panel-heading panel-legend" id="painel-heading"><b>Permissões - Celulares Covre</b></div>
</div>
<form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-12 panel-padrao" style="margin-left: 15px; margin-left: 15px; width: 1100px;">
            <div class="panel-body col-xs-12">
                <div class="table-responsive covre" style="padding:5px;">

                    <table width="90%" class="table table-bordered tb-padrao">
                        <tbody>
                           
                           	<tr>
                            	<td width="8" class="no-border left altura">&nbsp;</td>
								<td width="310" class="no-border left altura">
									<font size="-1"><b>Nome: </b></font> 
                                  <span class="col-xs-4 left-inline">
                                    <input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='responsavel_id' 
									id='responsavel_id' value='<?php print $responsavel_id ?>' size='8' maxlength='4' onKeyPress='somente_numero(this)'>
                                </span></td>  
								<td width="240" class="no-border left altura"><font size="-1"><b>Usuário</b>:</font><span class="col-xs-4 left-inline">
								  <input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='usuario_id' 
									id='usuario_id' value='<?php print $usuario_id ?>' size='8' maxlength='4' onKeyPress='somente_numero(this)'>
								</span></td>
								<td width="220" class="no-border left altura"><b><font size="-1">Estoque:</font></b></td>
								<td width="99" class="no-border left altura"><b><font size="-1">Administrar:</font></b></td>
								<td width="94" class="no-border left altura"><b><font size="-1">Gerar Vale:</font></b></td>
							  <td width="72" class="no-border left altura"><b><font size="-1">Status:</font></b></td>
								<td width="50" rowspan="8" class="no-border left altura"><span class="col-md-4"><a href="javascript:Modal_usuarios()"><img id=novo border=0 name=novo alt="Localizar Item" src="../SCA/images/lupa1.jpg" width=48 height=49 /></a></span></td>                                  
                            </tr>
                           	<tr>
                           	  <td class="no-border left altura">&nbsp;</td>
                           	  <td class="no-border left altura"><div class="col-xs-11 left-inline"><span class="col-xs-12 left-inline">
                           	    <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='responsavel_permissao' 
									id='responsavel_permissao' value='<?php print $responsavel ?>' size='50' maxlength='100' onkeyup="passaParametro(this)" onblur="javascript:busca_usuario_id(this)">
                           	  </span></div>
                              <div id="suggesstion-box" ></div>
                              </td>
                              <td class="no-border left altura"><div class="col-xs-11 left-inline"><span class="col-xs-10 left-inline">
                                <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='usuario' 
									id='usuario' value='<?php print $usuario ?>' size='50' maxlength='100' readonly>
                              </span></div></td>
                              <td class="no-border left altura"><div class="col-xs-11 left-inline">
                                <?php
											$query = "select id, descricao
														FROM estoque_aparelho_celular with (nolock)
														where status_id = 'a'
														order by descricao";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' 
													name='estoque_id' id='estoque_id' value='$estoque_id'><option></option>";
                                    
                                            while(odbc_fetch_row($result))
                                            {
                                    
                                                if (odbc_result($result, 1) == $adm_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";
                                    
                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
                              </div></td>
                              <td class="no-border left altura"><div class="col-xs-10 left-inline">
                                <?php
											$query = "select 'S' sigla,'Sim'
													  union
													  select 'N', 'Nao'
													  order by sigla ";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' 
													name='adm_id' id='adm_id' value='$adm_id'>";
                                    
                                            while(odbc_fetch_row($result))
                                            {
                                    
                                                if (odbc_result($result, 1) == $adm_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";
                                    
                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
                              </div></td>
                              <td class="no-border left altura"><div class="col-xs-10 left-inline">
                                <?php
											$query = "select 'S' sigla,'Sim'
													  union
													  select 'N', 'Nao'
													  order by sigla ";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' 
													name='gerar_vale_id' id='gerar_vale_id' value='$gerar_vale_id'>";
                                    
                                            while(odbc_fetch_row($result))
                                            {
                                    
                                                if (odbc_result($result, 1) == $gerar_vale_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";
                                    
                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
                              </div></td>
                           	  <td class="no-border left altura"><div class="col-xs-12 left-inline">
                           	    <?php
											$query = "select 'a' sigla,'Ativo'
													  union
													  select 'i', 'Inativo'
													  order by sigla ";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' 
													name='status_id' id='status_id' value='$status_id'>";
                                    
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
                           	  <td class="no-border left altura">&nbsp;</td>
                           	  <td colspan="6" class="no-border left altura">&nbsp;</td>
                       	  </tr>
                           	<tr>
                           	  <td class="no-border left altura">&nbsp;</td>
                           	  <td colspan="6" class="no-border left altura"><div align="center">
                           	    <input type='submit' class='btn-sm btn-default' value='Novo' id='novo' name='novo'>
                           	    <input type='button' class='btn-sm btn-primary' value='Gravar' id='gravar' name='gravar' onClick='javascript:grava_registro()'>
                       	      </div></td>
                       	  </tr>
                           	<tr>
                           	  <td class="no-border left altura">&nbsp;</td>
                           	  <td colspan="6" class="no-border left altura">&nbsp;</td>
                       	  </tr>
                     </tbody>
                     </table>
                     <table width="90%" class="table table-bordered tb-padrao">
                           	<tr>
                           	  <td width="2%" class="no-border left altura">&nbsp;</td>
                           	  <td colspan="2" class="no-border left altura"><font size="-1"><b>Centro de Custo:</b></font></td>
                           	  <td width="48%" class="no-border left altura"><span class="col-xs-10 left-inline">
                           	    <?php
                                            $query = "select CODREDUZIDO, NOME+' - '+CODREDUZIDO, NOME
														from Corpore..gccusto
														WHERE CODCOLIGADA = 1
														and ATIVO = 'T'
														--AND NOME NOT LIKE ('%Frota Covre%')
														AND(NOME NOT LIKE ('%Frota Covre%') OR CODREDUZIDO = '41303')
														ORDER BY NOME";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' 
													name='centro_custo_id' id='centro_custo_id' value='$centro_custo_id'><option></option>";
                                    
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
                           	  </span></td>
                           	  <td width="34%" valign="baseline" class="no-border left altura"><input type='button' class='btn-sm btn-primary' value='Inserir' id='inserir' name='inserir' onClick='javascript:grava_centro_custo()'></td>
                       	  </tr>
                           	<tr>
                           	  <td class="no-border left altura">&nbsp;</td>
                           	  <td colspan="4" class="no-border left altura"><div id="exibe_ccusto"></div> </td>
                       	  </tr>  
                     </table>

                    
                            <div class="container">
                                <div class="row" style="padding:10px; width:960px;">
                                  <p>
                                  
                                  <div id="exibe_resultado">
										<?php
                                            
                                            //$relatorio_centro_custo = $dados->exibeCCusto();  
                                            
                                            //$rel_centro_custo = $relatorio_centro_custo->ccusto;
                                            
                                            //print $rel_centro_custo;
                                                
                                        ?>
                                  </div>                                    
                                  
                                  
                                  </p>
                                    <div class="col-md-7">   
                                        <center>
                                          <button type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p">Fechar</button>                                        
                                      </center>                                     
                                  </div>

                                </div>
                      </div>

              </div>
            </div>
        </div>

</form>


                     <!-- Modal -->
                    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal_usuarios" class="modal fade">
                          <div class="modal-dialog">
                              <div class="modal-content">
                                  <div class="modal-header" style="background-color:#095D92">
                                      <h4 class="modal-title"><font color="#FFFFFF">USUÁRIOS</font></h4>
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



   
<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/main.js"></script>
    


    
    
</body>
</html>


<?php 
    }
include("../SCI/includes/modal.php");
include("../SCI/includes/modal_list.php");
?>