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

$localItem = "../celulares_covre/conferencia_valores.php";
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
	 
	 
	$dados_estoque = $estoque->buscaEstoqueResponsavel($usuario_id);
	$estoque_id 	= $dados_estoque->estoque_id;
	$administrador  = $dados_estoque->administrador;	
	$gerar_vale_id  = $dados_estoque->gerar_vale_id; 
	 

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
    <title>Conferência Valores / Geração Vales</title>
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


</head>


<body style="background-color: #3CB371" >
<div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
        <div class="panel-heading panel-legend" id="painel-heading"><b>Conferência Valores / Geração Vales</b></div>
    </div>
    <br>
<div class="col-xs-12">
    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-12 panel-padrao">
            <div class="panel-body col-xs-4">
                <div class="table-responsive covre">
                
                    <table width="83%" class="table table-bordered tb-padrao">
                        <tbody>
                           
                           	<tr>  
	                            <td width="14" class="no-border left altura">&nbsp;</td>
	                            <td width="31" class="no-border left altura"><font size="-1"><b>Período: </b></font></td>                            
								<td class="no-border left altura"><div class="col-xs-8 left-inline"><span class="col-xs-12 left-inline">
								  <?php
                                            $query = "select distinct cast(valor_mensal_plano.mes as varchar)+'/'+cast(valor_mensal_plano.ano as varchar) periodo,
                                                                    CASE valor_mensal_plano.mes
                                                                        WHEN 1 THEN 'JANEIRO'
                                                                        WHEN 2 THEN 'FEVEREIRO'
                                                                        WHEN 3 THEN 'MARCO'
                                                                        WHEN 4 THEN 'ABRIL'
                                                                        WHEN 5 THEN 'MAIO'
                                                                        WHEN 6 THEN 'JUNHO'
                                                                        WHEN 7 THEN 'JULHO'
                                                                        WHEN 8 THEN 'AGOSTO'
                                                                        WHEN 9 THEN 'SETEMBRO'
                                                                        WHEN 10 THEN 'OUTUBRO'
                                                                        WHEN 11 THEN 'NOVEMBRO'
                                                                        WHEN 12 THEN 'DEZEMBRO'
                                                                    END+' / '+CAst(valor_mensal_plano.ano as varchar) descricao,
                                                                    valor_mensal_plano.mes,
                                                                    valor_mensal_plano.ano,
                                                                    case	
                                                                        when DADOS_TOTAL_ITENS.total <> 0 and DADOS_TOTAL_ITENS.total = DADOS_ITENS_F.TOTAL then 'finalizado'
                                                                        else 'pendente' 
                                                                    end geracao_finalizada
                                                                    
                                                            from valor_mensal_plano with (nolock)
                                                            
                                                            outer apply(
                                                                select
                                                                    count(*) TOTAL
                                                                    from conferencia_plano_agregado conferencia with (nolock)
                                                                                join valor_mensal_plano vmp with (nolock) on
                                                                                    vmp.id = conferencia.valor_mensal_plano_id
                                                                                    and vmp.status_id = 'a'
                                                                                join cargosol..pessoa with (nolock) on
                                                                                    pessoa.pessoa_id = conferencia.Pessoa_Id
                                                                                join linha_aparelho_funcionario laf with (nolock) on
                                                                                    laf.id = conferencia.linha_aparelho_id
                                                                                join celular_covre linha with (nolock) on
                                                                                    linha.id = laf.celular_id
                                                                                join plano_celular plano with (nolock) on
                                                                                    plano.id = conferencia.plano_id
                                                                                left join cargosol..veiculo_fornecedor vf with (nolock) on
                                                                                ((vf.Fornecedor_Id = laf.pessoa_id) or 
                                                                                    vf.Fornecedor_Id = (select Fornecedor_Id
                                                                                                        from cargosol..veiculo_fornecedor with (nolock)
                                                                                                        where Motorista_Id = laf.pessoa_id
                                                                                                        and Tab_Status_Id = 1))
                                                                                    and vf.Tab_Status_Id <> 2
                                                                                    AND (VF.TAB_STATUS_VEICULO_ID IN (1,2,3))
                                                                                    and vf.tab_tipo_veiculo_id not in (187,188,189,198,234,237)
                                                                                left join cargosol..pessoa proprietario with (nolock) on
                                                                                    proprietario.pessoa_id = vf.Fornecedor_Id						
                                                                                left join cargosol..tab_tipo_veiculo ttv with (nolock) on
                                                                                    ttv.tab_tipo_veiculo_id = vf.tab_tipo_veiculo_id
                                                                                    and ttv.desc_tipo_veiculo not like '%SEMI REBOQUE%'
                                                                                where conferencia.status_id not in ('i')
                                                                                and conferencia.mes = valor_mensal_plano.mes
                                                                                and conferencia.ano = valor_mensal_plano.ano
                                                                                
                                                                                        
                                                            ) DADOS_TOTAL_ITENS
                                                            
                                                            outer apply(
                                                                select
                                                                    count(*) TOTAL
                                                                    from conferencia_plano_agregado conferencia with (nolock)
                                                                                join valor_mensal_plano vmp with (nolock) on
                                                                                    vmp.id = conferencia.valor_mensal_plano_id
                                                                                    and vmp.status_id = 'a'
                                                                                join cargosol..pessoa with (nolock) on
                                                                                    pessoa.pessoa_id = conferencia.Pessoa_Id
                                                                                join linha_aparelho_funcionario laf with (nolock) on
                                                                                    laf.id = conferencia.linha_aparelho_id
                                                                                join celular_covre linha with (nolock) on
                                                                                    linha.id = laf.celular_id
                                                                                join plano_celular plano with (nolock) on
                                                                                    plano.id = conferencia.plano_id
                                                                                left join cargosol..veiculo_fornecedor vf with (nolock) on
                                                                                ((vf.Fornecedor_Id = laf.pessoa_id) or 
                                                                                    vf.Fornecedor_Id = (select Fornecedor_Id
                                                                                                        from cargosol..veiculo_fornecedor with (nolock)
                                                                                                        where Motorista_Id = laf.pessoa_id
                                                                                                        and Tab_Status_Id = 1))
                                                                                    and vf.Tab_Status_Id <> 2
                                                                                    AND (VF.TAB_STATUS_VEICULO_ID IN (1,2,3))
                                                                                    and vf.tab_tipo_veiculo_id not in (187,188,189,198,234,237)
                                                                                left join cargosol..pessoa proprietario with (nolock) on
                                                                                    proprietario.pessoa_id = vf.Fornecedor_Id						
                                                                                left join cargosol..tab_tipo_veiculo ttv with (nolock) on
                                                                                    ttv.tab_tipo_veiculo_id = vf.tab_tipo_veiculo_id
                                                                                    and ttv.desc_tipo_veiculo not like '%SEMI REBOQUE%'
                                                                                where conferencia.status_id not in ('i')
                                                                                and conferencia.mes = valor_mensal_plano.mes
                                                                                and conferencia.ano = valor_mensal_plano.ano	
                                                                                and conferencia.status_id = 'f'
                                                            ) DADOS_ITENS_F
                                                            
                                                            where valor_mensal_plano.status_id = 'a'
                                                            order by valor_mensal_plano.ano, valor_mensal_plano.mes";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='periodo' id='periodo' value='$periodo' onchange='javascript:busca_conferencia(this.value)'><option></option>";
                                            
                                            $color    = '';
                                            $status   = '';
                                            $mensagem = '';

                                            while(odbc_fetch_row($result))
                                            {

                                                $status = odbc_result($result, 5);

                                                if($status == 'finalizado'){
                                                    $color    = 'color:#0055ff;';
                                                    $mensagem = 'VALES GERADOS';
                                                } else if($status == 'pendente'){
                                                    $color    = '';
                                                    $mensagem = 'VALES PENDENTES';
                                                }   
                                                   
                                    
                                                if (odbc_result($result, 1) == $periodo)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";
                                    
                                                 print "<option style='".$color."' value='".odbc_result($result, 1)."'$selected><b>".utf8_encode(odbc_result($result, 2)).' - '.$mensagem."</b></option>";
                                            }
                                            print"</select>";
                                    ?>
							    </span></div></td>
							</tr>   
                       	</tbody>
                    </table>
                </div>
            </div>
            
              <p></p><p></p>
               <div id="exibe_resultado">

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