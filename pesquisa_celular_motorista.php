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

$localItem = "../celulares_covre/pesquisa_celular_motorista.php";
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



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pesquisa - Celular Motorista</title>

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
    
<!--	<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.js"></script> -->


    
</head>
<body>
    <div class="panel-primary col-lg-5" style="max-height: 30px;">
        <div class="panel-heading panel-legend"><b>	Pesquisa - Celular Motorista</b></div>
    </div>
    <br>
<div class="col-lg-12">
    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-10 panel-padrao">
            <div class="panel-body col-xs-7">
                <div class="table-responsive covre">
        
                   <table width="100%" class="table table-bordered tb-padrao">
                    <tr>
                        <td class="no-border left altura" width="1">&nbsp;</td>
                        <td width="184" class="no-border left altura">
                            <font size="-1"><b>Nome Motorista: </b></font> 
                        </td>  
                        <td width="859" class="no-border left altura">
                        <div class="col-xs-11 left-inline">	
                            <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='nome_motorista' id='nome_motorista' value=''  maxlength='100'>   
                        </div>
                        </td> 
                        <td width="65" class="no-border left altura"><button type='button' class='btn btn-theme altura-campo' id='pesquisar' name='pesquisar' style=' color:#FFF; background-color:#095D92' onclick='javascript:exibe_celular_motorista()'>Pesquisar</button>
                        </td>                                  
                    </tr>
                    </table>
                   
                </div>
            </div>
            <div class="panel-body col-xs-12">    
                
                <div id="exibe_resultado"></div>                         
                
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
							