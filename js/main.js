function order_valor_mensal_plano() {

  $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss');    //Formatação com Hora
  $.fn.dataTable.moment('DD/MM/YYYY');    //Formatação sem Hora

  if( $.fn.dataTable.isDataTable($('#myTable3')) ){
    $('#myTable3').DataTable().clear().destroy();
  }

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
        },
        customizeData: function (xlsx) {
         //var sheet = xlsx.xl.worksheets['sheet1.xml'];
         //$('row:first c', sheet).attr('s', '2');

         for (var i = 0; i < xlsx.body.length; i++) {
           // Modificar o valor da coluna 3 (índice 2)
           var t = xlsx.body[i][1];

           xlsx.body[i][1] = t.replace("(","").replace(")","").replace("-","");
       }
      },
    }
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


function order_modelo_aparelho() {

  $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss');    //Formatação com Hora
  $.fn.dataTable.moment('DD/MM/YYYY');    //Formatação sem Hora

  if( $.fn.dataTable.isDataTable($('#myTable3')) ){
    $('#myTable3').DataTable().clear().destroy();
  }

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
        },
        customizeData: function (xlsx) {
         //var sheet = xlsx.xl.worksheets['sheet1.xml'];
         //$('row:first c', sheet).attr('s', '2');

         for (var i = 0; i < xlsx.body.length; i++) {
           // Modificar o valor da coluna 3 (índice 2)
           var t = xlsx.body[i][1];

           xlsx.body[i][1] = t.replace("(","").replace(")","").replace("-","");
       }
      },
    }
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


function order_celular() {

  $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss');    //Formatação com Hora
  $.fn.dataTable.moment('DD/MM/YYYY');    //Formatação sem Hora
  if( $.fn.dataTable.isDataTable($('#myTable3')) ){
    $('#myTable3').DataTable().clear().destroy();
  }
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
        },
        customizeData: function (xlsx) {
         //var sheet = xlsx.xl.worksheets['sheet1.xml'];
         //$('row:first c', sheet).attr('s', '2');

         for (var i = 0; i < xlsx.body.length; i++) {
           // Modificar o valor da coluna 3 (índice 2)
           var t = xlsx.body[i][1];

           xlsx.body[i][1] = t.replace("(","").replace(")","").replace("-","");
       }
      },
    }
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


function order_aparelho_funcionario() {

  $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss');    //Formatação com Hora
  $.fn.dataTable.moment('DD/MM/YYYY');    //Formatação sem Hora
  if( $.fn.dataTable.isDataTable($('#myTable3')) ){
    $('#myTable3').DataTable().clear().destroy();
  }
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
        },
        customizeData: function (xlsx) {
         //var sheet = xlsx.xl.worksheets['sheet1.xml'];
         //$('row:first c', sheet).attr('s', '2');

         for (var i = 0; i < xlsx.body.length; i++) {
           // Modificar o valor da coluna 3 (índice 2)
           var t = xlsx.body[i][1];

           xlsx.body[i][1] = t.replace("(","").replace(")","").replace("-","");
       }
      },
    }
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


function order_aparelho(){
  $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss');    //Formatação com Hora
  $.fn.dataTable.moment('DD/MM/YYYY');    //Formatação sem Hora

  if( $.fn.dataTable.isDataTable($('#myTable3')) ){
    $('#myTable3').DataTable().clear().destroy();
  }

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
        },
        customizeData: function (xlsx) {
         //var sheet = xlsx.xl.worksheets['sheet1.xml'];
         //$('row:first c', sheet).attr('s', '2');

         for (var i = 0; i < xlsx.body.length; i++) {
           // Modificar o valor da coluna 3 (índice 2)
           var t = xlsx.body[i][1];

           xlsx.body[i][1] = t.replace("(","").replace(")","").replace("-","");
       }
      },
    },
      { extend: 'print', text:'<i class="fa fa-files-o">Imprimir</i>' },
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


function grava_registro_aparelho_funcionario(dia_ini, dia_fim){
  //alert(dia_ini);

  var id					= document.getElementById('id').value;
  var pessoa_id			= document.getElementById('pessoa_id').value;
  var corp_pessoa_id			= document.getElementById('corp_pessoa_id').value;
  var celular_covre_id	= document.getElementById('celular_covre_id').value;
  var aparelho_id			= document.getElementById('aparelho_id').value;
  var data_ini			= document.getElementById('data_ini').value;
  var cod_ccusto			= document.getElementById('cod_ccusto').value;
  var observacao 	 		= document.getElementById('observacao').value;
  var dia_data = data_ini.substring(0, 2);

  if (pessoa_id == '' && corp_pessoa_id == '')
  {
    $('#msg').html('Favor informar um responsável válido');
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
          if (document.form1.id_antigo) document.form1.id_antigo.value = '';
          if (document.form1.data_fim_antigo) document.form1.data_fim_antigo.value = '';
          document.form1.responsavel.value		= '';
          document.form1.responsavel.readOnly 	= false;
          document.form1.pessoa_id.value			= '';
          document.form1.corp_pessoa_id.value			= '';
          //busca_agregados();
          busca_celulares();
          busca_aparelhos();
          document.form1.celular_covre_id.value	= '';
          document.form1.aparelho_id.value		= '';
          document.form1.data_ini.value			= '';
          document.form1.data_fim.value			= '';
          document.form1.cod_ccusto.value			= '';
          document.form1.observacao.value			= '';

          $('#trocar_resp').hide();
          $('#trocar_resp_label').hide();

          exibe_celular();
        }


      }
    });

  }
}

function grava_registro_aparelho(){
  //alert();

  var id			 	 	= document.getElementById('id') ? document.getElementById('id').value : "";
  var modelo_id			= document.getElementById('modelo_id') ? document.getElementById('modelo_id').value : "";
  var serie		 	 	= document.getElementById('serie') ? document.getElementById('serie').value : "";
  var imei1		 	 	= document.getElementById('imei1') ? document.getElementById('imei1').value : "";
  var imei2	 			= document.getElementById('imei2') ? document.getElementById('imei2').value : "";
  var observacao 			= document.getElementById('observacao') ? document.getElementById('observacao').value : "";
  var status_id 			= document.getElementById('status_id') ? document.getElementById('status_id').value : "";


  if (modelo_id == '' && document.getElementById('modelo_id'))
  {
    $('#msg').html('Favor inserir a Marca/Modelo');
    $('#myModal_alert').modal('show');
  }
  else
  if (serie == '')
  {
    $('#msg').html('Favor inserir o Número de Série');
    $('#myModal_alert').modal('show');
  }
  else
  if ((imei1 == '') && (imei2 == ''))
  {
    $('#msg').html('Favor inserir pelo menos 1 número IMEI');
    $('#myModal_alert').modal('show');
  }
  else
  {

    let dados = $("form").serialize();

    $.ajax({
      type: "POST", //define o metуdo de passagem de parametros
      url: "includes/grava_dados_aparelho.php", //chama uma pagina
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

          document.getElementById('id').value = '';
          document.getElementById('modelo_id').value = '';
          document.getElementById('serie').value = '';
          document.getElementById('imei1').value = '';
          document.getElementById('imei2').value = '';
          document.getElementById('observacao').value = '';
          document.getElementById('status_id').value = 'a';

          exibe_dados_aparelho();
        }


      }
    });

  }

}


function grava_registro_mensal_plano(){
  //alert();

  var plano_id			= document.getElementById('plano_id').value;
  var valor			 	= document.getElementById('valor').value;
  var mes		 			= document.getElementById('mes').value;
  var ano		 			= document.getElementById('ano').value;


  if (plano_id == '')
  {
    $('#msg').html('Favor informar o plano');
    $('#myModal_alert').modal('show');
  }
  else
  if (valor == '')
  {
    $('#msg').html('Favor informar o valor');
    $('#myModal_alert').modal('show');
  }
  else
  if (mes == '')
  {
    $('#msg').html('Favor informar o mês');
    $('#myModal_alert').modal('show');
  }
  else
  if (ano == '')
  {
    $('#msg').html('Favor informar o ano');
    $('#myModal_alert').modal('show');
  }
  else
  {

    let dados = $("form").serialize();

    $.ajax({
      type: "POST", //define o metуdo de passagem de parametros
      url: "includes/grava_valor_mensal.php", //chama uma pagina
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

          document.getElementById('plano_id').value = '';
          document.getElementById('valor').value = '';
          document.getElementById('mes').value = '';
          document.getElementById('ano').value = '';


          exibe_valor_mensal();
        }


      }
    });

  }

}

function grava_registro_modelo_aparelho(){
  //alert();

  var id			 	 	= document.getElementById('id').value;
  var marca_id 	 		= document.getElementById('marca_id').value;
  var modelo	 			= document.getElementById('modelo').value;
  var status_id 			= document.getElementById('status_id').value;


  if (marca_id == '')
  {
    $('#msg').html('Favor selecionar a marca');
    $('#myModal_alert').modal('show');
  }
  else
  if (modelo == '')
  {
    $('#msg').html('Favor informar o modelo');
    $('#myModal_alert').modal('show');
  }

  else
  {

    let dados = $("form").serialize();

    $.ajax({
      type: "POST", //define o metуdo de passagem de parametros
      url: "includes/grava_modelo_aparelho.php", //chama uma pagina
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

          document.getElementById('id').value = '';
          document.getElementById('marca_id').value = '';
          document.getElementById('modelo').value = '';

          exibe_modelo_celular();
        }


      }
    });

  }
}

function grava_registro_celular(){
  //alert();

  var id			 	 	= document.getElementById('id').value;
  var num_celular			= document.getElementById('num_celular').value;
  var operadora_id 	 	= document.getElementById('operadora_id').value;
  var imei	 			= document.getElementById('imei').value;


  if (num_celular == '')
  {
    $('#msg').html('Favor inserir o número do celular');
    $('#myModal_alert').modal('show');
  }
  else
  if (operadora_id == '')
  {
    $('#msg').html('Favor selecionar a operadora');
    $('#myModal_alert').modal('show');
  }
  else
  if (imei == '')
  {
    $('#msg').html('Favor informar o número IMEI');
    $('#myModal_alert').modal('show');
  }
  else
  {

    let dados = $("form").serialize();

    $.ajax({
      type: "POST", //define o metуdo de passagem de parametros
      url: "includes/grava_dados_celular.php", //chama uma pagina
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

          document.getElementById('id').value = '';
          document.getElementById('num_celular').value = '';
          document.getElementById('operadora_id').value = '';
          document.getElementById('imei').value = '';
          document.getElementById('observacao').value = '';

          $("#num_celular").attr({readonly: false});

          exibe_dados_celular();

        }


      }
    });

  }

}

/** Função Página */
function pagina(janela, largura, altura, descricao) {
  tamanho = "height=" + altura + ",width=" + largura + ",scrollbars=yes,resizable=yes";
  window.open(janela, descricao, tamanho);
}

function fechar(){
  open(location, '_self').close();
}


/** PARAMETROS DATEPICKER  */


var todayDate = new Date().getDate();
var dia_inicial = new Date(new Date().setDate(todayDate - 5));
//var dia_final = new Date(new Date().setDate(todayDate + 1));

$('.form_datetime').datetimepicker({
  language: 'pt-BR',
  weekStart: 1,
  maxDate: "+1m",
  todayBtn: 1,
  autoclose: 1,
  todayHighlight: 1,
  startView: 2    ,
  forceParse: 1,
  showMeridian: 1,
  maxView: 4,
  minView: 2
  //startDate: dia_inicial,
  //endDate: dia_final
});


/*AUTOCOMPLETE RESPONSAVEL*/
function passaParametro1(param){
  if ($("#responsavel").val().length >= 2) {
    $.ajax({
      type: "POST",
      url: "includes/ajax_responsavel.php",
      data:'keyword='+param.value,
      beforeSend: function(){
        $("#busca-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
      },
      success: function(data){
        if ($.trim(data) == 'vazio')
        {
          document.getElementById('pessoa_id').value = '';
          document.getElementById('corp_pessoa_id').value = '';
          $("#suggesstion-box").html('');
        }
        else
        {
          $("#suggesstion-box").show();
          $("#suggesstion-box").html(data);
          $("#busca-box").css("background","#FFF");
        }
      }
    });
  } else {
    $("#suggesstion-box").hide();
  }
}

function selectFiltro(corp_pessoa_id, pessoa_id, val) {
  //alert(1);
  //$("#busca-box").val(val);
  document.getElementById('responsavel').value = val;
  document.getElementById('pessoa_id').value = pessoa_id;
  document.getElementById('corp_pessoa_id').value = corp_pessoa_id;
  document.getElementById('responsavel').focus();
  $("#suggesstion-box").hide();
}
/*FIM AUTOCOMPLETE*/



/*AUTOCOMPLETE RESPONSAVEL PERMISSAO*/
function passaParametro(param){
  if ($("#responsavel_permissao").val().length >= 2) {
    $.ajax({
      type: "POST",
      url: "includes/ajax_responsavel_permissao.php",
      data:'keyword='+param.value,
      beforeSend: function(){
        $("#busca-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
      },
      success: function(data){
        $("#suggesstion-box").show();
        $("#suggesstion-box").html(data);
        $("#busca-box").css("background","#FFF");
      }
    });
  } else {
    $("#suggesstion-box").hide();
  }
}

function selectList(val) {
  //alert(1);
  //$("#busca-box").val(val);
  document.getElementById('responsavel_permissao').value = val;
  document.getElementById('responsavel_permissao').focus();
  $("#suggesstion-box").hide();
}
/*FIM AUTOCOMPLETE*/


$(document).ready(function () {
  $('#num_celular').mask('(00)00000-0000');
  $('#data_ini').mask('00/00/0000');
  $('#imei').mask('00000000000000000000');
  $('#cod_ccusto').mask('9.99.99.999.999');
});


function busca_agregados(){
  //alert();

  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/busca_agregados.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);

    $("#pessoa_id").html(msg);

  }
});


}

function busca_celulares(){
  //alert();

  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/busca_celulares.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);

    $("#celular_covre_id").html(msg);

  }
});

}

function busca_aparelhos(){
  //alert();

  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/busca_aparelhos.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);

    $("#aparelho_id").html(msg);

  }
});

}

/*
function verifica_permissao_cc(elmnt){
//alert();

$.ajax({type: "POST",//define o metódo de passagem de parametros
url: "includes/verifica_permissao_cc.php", //chama uma pagina
data: "ccusto="+elmnt.value,
success: function(msg){  //pega o retorno da pagina chamada
//alert(msg);

if(msg.indexOf("ok") == -1)
{
$('#msg').html('Você não tem permissão para cadastrar este Centro de Custo');
$('#myModal_alert').modal('show');
}

}
});

}
*/

function busca_celulares_despesa(){
  //alert();

  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/busca_celulares_despesa.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);

    $("#celular_covre_id").html(msg);

  }
});


}


function exclui_despesa_extra(id){

  //alert(id);
  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/exclui_despesa_extra.php", //chama uma pagina
  data: "id="+id,
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);

    exibe_despesas_extras();

  }
});

}

function exibe_valor_mensal(){
  //alert();
  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/exibe_valor_mensal.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);
    $('#exibe_resultado').html(msg);
    order_valor_mensal_plano() ;
  }
});

}

function exclui_valor_mensal(id){

  //alert(id);
  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/exclui_valor_mensal.php", //chama uma pagina
  data: "id="+id,
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);

    exibe_valor_mensal();

  }
});

}


function busca_conferencia(elmnt){
  //alert(elmnt.value);


  if (elmnt != '')
  {

    $.ajax({
      type: "POST", //define o metуdo de passagem de parametros
      url: "includes/busca_conferencia.php", //chama uma pagina
      data: "mes_ano="+elmnt, //passa os parametros, se necessбrio
      success: function (msg) {
        //alert(msg);

        $('#exibe_resultado').html(msg);

      }
    });

  }

}



function exclui_conf_mensal_mot(id, periodo){

  //alert(id);
  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/exclui_conf_mensal_mot.php", //chama uma pagina
  data: "id="+id,
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);

    busca_conferencia(periodo);

  }
});

}



function exclui_cc_responsavel(id, responsavel_id){

  //alert(id);
  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/exclui_cc_responsavel.php", //chama uma pagina
  data: "id="+id,
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);

    busca_centro_custo_responsavel(responsavel_id);

  }
});

}


function exibe_celular_motorista(){

  var nome = document.form1.nome_motorista.value;

  if (nome == '')
  {
    $('#msg').html('Favor informar o nome do motorista');
    $('#myModal_alert').modal('show');
  }
  else
  {
    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/exibe_celular_motorista.php", //chama uma pagina
    data: "nome="+nome, //passa os parametros, se necessário
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);
      $('#exibe_resultado').html(msg);
    }
  });
}

}

$(document).keypress(function(e) {

  if(e.which == 13)
  {
    exibe_celular_motorista();
    e.preventDefault();
    //document.form1.pesquisar.click();
  }
});


function exibe_dados_celular(){

  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/exibe_dados_celular.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);
    $('#exibe_resultado').html(msg);
    order_celular();
  }
});

}

function exibe_dados_aparelho(){
  //alert();
  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/exibe_dados_aparelho.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);
    $('#exibe_resultado').html(msg);
    order_aparelho();

  }
});

}

function exibe_modelo_celular(){

  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/exibe_modelo_celular.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);
    $('#exibe_resultado').html(msg);
    order_modelo_aparelho();

  }
});

}

function exibe_despesas_extras(){

  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/exibe_despesas_extras.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);
    $('#exibe_resultado').html(msg);

  }
});

}



function exibe_celular(){

  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/exibe_celular.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);
    $('#exibe_resultado').html(msg);
    order_celular();
  }
});

}


function exclui_celular(id){
  //alert(id);

  $('#msg_warning').html('Deseja realmente excluir o responsável?');
  $('#myModal_alert_warning').modal('show');



  $("#btn-sim").click(function () {
    //alert();
    if (id != "")
    {
      $.ajax({type: "POST",//define o metódo de passagem de parametros
      url: "includes/exclui_celular.php", //chama uma pagina
      data: "id="+id, //passa os parametros, se necessário
      success: function(msg){  //pega o retorno da pagina chamada
        //alert(msg);
        busca_agregados();
        busca_celulares();
        exibe_celular();

      }
    });
  }
});


$("#btn-nao").click(function () {
  $('#myModal_alert_warning').modal('hide');
});

}

function busca_parametro(){
  //alert();


  //alert();
  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/busca_parametro.php", //chama uma pagina
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);
    var dados = msg.split("|");

    document.form1.valor.value			= dados[1];
    document.form1.dia_ini.value		= dados[2];
    document.form1.dia_fim.value		= dados[3];

  }
});


}


function dados_registro(id)
{
  //alert(id);
  //alert(registro);

  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/dados_registro.php", //chama uma pagina
  data: "id="+id, //passa os parametros, se necessário
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);
    $("#resultado_modal").html(msg);
    $('#myModal_linha').modal('show');
  }
});
}

function dados_aparelho(id)
{
  //alert(id);
  //alert(registro);

  $.ajax({type: "POST",//define o metódo de passagem de parametros
  url: "includes/dados_aparelho.php", //chama uma pagina
  data: "id="+id, //passa os parametros, se necessário
  success: function(msg){  //pega o retorno da pagina chamada
    //alert(msg);
    $("#resultado_modal_aparelho").html(msg);
    $('#myModal_aparelho').modal('show');
  }
});
}



function busca_celular(id){
  //alert();

  if (id != '')
  {
    //alert();
    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/busca_celular.php", //chama uma pagina
    data: "&id=" + id,
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);
      var dados = msg.split("|");

      document.form1.id.value				= dados[1];
      if (document.form1.id_antigo) document.form1.id_antigo.value = '';
      if (document.form1.data_fim_antigo) document.form1.data_fim_antigo.value = '';
      document.form1.pessoa_id.value		= dados[2];
      document.form1.corp_pessoa_id.value		= dados[11];
      document.form1.responsavel.value	= dados[6];
      //$("#pessoa_id").html("<option value='"+dados[2]+"'>"+dados[6]+"</option>");
      $("#celular_covre_id").html("<option value='"+dados[3]+"'>"+dados[7]+"</option>");
      document.form1.data_ini.value		= dados[4];
      document.form1.observacao.value		= dados[5];
      document.form1.cod_ccusto.value		= dados[8];
      $("#aparelho_id").html("<option value='"+dados[9]+"'>"+dados[10]+"</option>");

      document.form1.responsavel.readOnly = true;

      $('#trocar_resp').show();
      $('#trocar_resp_label').show();
    }
  });
}

}

function trocar_responsavel(){
  var id = document.getElementById('id') ? document.getElementById('id').value : '';
  var data_fim = document.getElementById('data_fim') ? document.getElementById('data_fim').value : '';

  if (id == '') {
    $('#msg').html('Selecione um registro cadastrado para trocar o responsável.');
    $('#myModal_alert').modal('show');
    return;
  }

  if (data_fim == '') {
    $('#msg').html('Favor preencher a Data Fim para encerrar a responsabilidade do responsável atual.');
    $('#myModal_alert').modal('show');
    document.getElementById('data_fim').focus();
    return;
  }

  if (document.getElementById('id_antigo')) document.getElementById('id_antigo').value = id;
  if (document.getElementById('data_fim_antigo')) document.getElementById('data_fim_antigo').value = data_fim;

  document.getElementById('id').value = '';
  document.getElementById('responsavel').value = '';
  document.getElementById('pessoa_id').value = '';
  document.getElementById('corp_pessoa_id').value = '';
  document.getElementById('responsavel').readOnly = false;

  document.getElementById('data_ini').value = data_fim;
  document.getElementById('data_fim').value = '';

  $('#trocar_resp').hide();
  $('#trocar_resp_label').hide();

  $('#msg_success').html('Responsabilidade anterior encerrada em ' + data_fim + '. Informe o novo responsável e clique em Gravar.');
  $('#myModal_alert_sucess').modal('show');

  document.getElementById('responsavel').focus();
}

/*SOMENTE NUMERO*/
jQuery(function($) {
  $(document).on('keypress', 'input.only-number', function(e) {
    var $this = $(this);
    var key = (window.event)?event.keyCode:e.which;
    var dataAcceptDot = $this.data('accept-dot');
    var dataAcceptComma = $this.data('accept-comma');
    var acceptDot = (typeof dataAcceptDot !== 'undefined' && (dataAcceptDot == true || dataAcceptDot == 1)?true:false);
    var acceptComma = (typeof dataAcceptComma !== 'undefined' && (dataAcceptComma == true || dataAcceptComma == 1)?true:false);

    if((key > 47 && key < 58)
    || (key == 46 && acceptDot)
    || (key == 44 && acceptComma)) {
      return true;
    } else {
      return (key == 8 || key == 0)?true:false;
    }
  });
});

/*SOMENTE NUMERO VIRGULA*/
jQuery(function($) {
  $(document).on('keypress', 'input.only-number_virgula', function(e) {
    var $this = $(this);
    var key = (window.event)?event.keyCode:e.which;
    var dataAcceptDot = $this.data('accept-dot');
    var dataAcceptComma = $this.data('accept-comma');
    var acceptDot = (typeof dataAcceptDot !== 'undefined' && (dataAcceptDot == true || dataAcceptDot == 1)?true:false);
    var acceptComma = (typeof dataAcceptComma !== 'undefined' && (dataAcceptComma == true || dataAcceptComma == 1)?true:false);

    if((key > 47 && key < 58) || (key == 44)
    || (key == 46 && acceptDot)
    || (key == 44 && acceptComma)) {
      return true;
    } else {
      return (key == 8 || key == 0)?true:false;
    }
  });
});

/*SOMENTE LETRA E NUMERO*/
jQuery(function($) {
  $(document).on('keypress', 'input.only-number_letra', function(e) {
    var $this = $(this);
    var key = (window.event)?event.keyCode:e.which;
    var dataAcceptDot = $this.data('accept-dot');
    var dataAcceptComma = $this.data('accept-comma');
    var acceptDot = (typeof dataAcceptDot !== 'undefined' && (dataAcceptDot == true || dataAcceptDot == 1)?true:false);
    var acceptComma = (typeof dataAcceptComma !== 'undefined' && (dataAcceptComma == true || dataAcceptComma == 1)?true:false);

    if((key > 47 && key < 58)
    || (key > 64 && key < 91)
    || (key > 96 && key < 123)
    || (key == 46 && acceptDot)
    || (key == 44 && acceptComma)) {
      return true;
    } else {
      return (key == 8 || key == 0)?true:false;
    }
  });
});


function busca_dados_celular(id){
  //alert();

  if (id != '')
  {
    //alert();
    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/busca_dados_celular.php", //chama uma pagina
    data: "&id=" + id,
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);
      var dados = msg.split("|");

      document.form1.id.value				= dados[1];
      document.form1.num_celular.value	= dados[2];
      document.form1.operadora_id.value	= dados[3];
      document.form1.imei.value			= dados[4];
      document.form1.status_id.value		= dados[5];
      document.form1.observacao.value		= dados[6];

      if (dados[2] != '')
      $("#num_celular").attr({readonly: true});

    }
  });
}

}

function busca_dados_aparelho(id){
  //alert(id);

  if (id != '')
  {
    //alert();
    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/busca_dados_aparelho.php", //chama uma pagina
    data: "&id=" + id,
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);
      var dados = msg.split("|");

      document.form1.id.value				= dados[1];
      document.form1.modelo_id.value		= dados[2];
      document.form1.serie.value			= dados[3];
      document.form1.imei1.value			= dados[4];
      document.form1.imei2.value			= dados[5];
      document.form1.observacao.value		= dados[6];
      document.form1.status_id.value		= dados[7];

    }
  });
}

}


function busca_dados_modelo(id){
  //alert();

  if (id != '')
  {
    //alert();
    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/busca_dados_modelo.php", //chama uma pagina
    data: "&id=" + id,
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);
      var dados = msg.split("|");

      document.form1.id.value				= dados[1];
      document.form1.marca_id.value		= dados[2];
      document.form1.modelo.value			= dados[3];
      document.form1.status_id.value		= dados[4];

    }
  });
}

}



function busca_despesas_extras(id){
  //alert();

  if (id != '')
  {
    //alert();
    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/busca_despesas_extras.php", //chama uma pagina
    data: "&id=" + id,
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);
      var dados = msg.split("|");

      document.form1.id.value					= dados[1];
      $("#celular_covre_id").html("<option value='"+dados[2]+"'>"+dados[6]+"</option>");
      document.form1.celular_covre_id.value	= dados[2];
      document.form1.data.value				= dados[3];
      document.form1.valor.value				= dados[4];
      document.form1.observacao.value			= dados[5];

    }
  });
}

}


function busca_centro_custo_responsavel(id){
  //alert(id);

  if (id != '')
  {
    //alert();
    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/busca_centro_custo_responsavel.php", //chama uma pagina
    data: "&id=" + id,
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);
      var dados = msg.split("|");

      $('#exibe_ccusto').html(msg);
    }
  });
}

}



function busca_dados_responsavel(id){
  //alert();

  if (id != '')
  {
    //alert();
    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/busca_dados_responsavel.php", //chama uma pagina
    data: "&id=" + id,
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);
      var dados = msg.split("|");

      document.getElementById('responsavel_id').value			= dados[1];
      document.getElementById('responsavel_permissao').value	= dados[2];
      document.getElementById('usuario_id').value				= dados[3];
      document.getElementById('usuario').value				= dados[4];
      document.getElementById('adm_id').value					= dados[5];
      document.getElementById('status_id').value				= dados[6];
      document.getElementById('estoque_id').value				= dados[7];
      document.getElementById('gerar_vale_id').value			= dados[8];


      busca_centro_custo_responsavel(dados[1]);

      $('#myModal_usuarios').modal('hide');
    }
  });
}

}


function busca_aparelho_estoque(id){
  //alert(id);

  if (id != '')
  {
    //alert();
    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/busca_aparelho_estoque.php", //chama uma pagina
    data: "&id=" + id,
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);
      $('#exibe_resultado').html(msg);
    }
  });
}

}


function aceita_aparelho(id, acao, estoque_id){
  //alert(id);

  if (id != '')
  {

    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/aceita_aparelho.php", //chama uma pagina
    data: "id="+id + "&acao="+acao, //parametros
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);

      if(msg.indexOf("ok") == -1)
      {
        $('#msg').html(msg);
        $('#myModal_alert').modal('show');
      }
      else
      {
        busca_aparelho_estoque(estoque_id);
      }
    }
  });
}
}


function aceita_linha(id, acao, estoque_id){
  //alert(id);

  if (id != '')
  {

    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/aceita_linha.php", //chama uma pagina
    data: "id="+id + "&acao="+acao, //parametros
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);

      if(msg.indexOf("ok") == -1)
      {
        $('#msg').html(msg);
        $('#myModal_alert').modal('show');
      }
      else
      {
        busca_aparelho_estoque(estoque_id);
      }
    }
  });
}
}


function altera_dado(elmnt, conferencia_id, numero_campo){
  //alert(id);

  if (elmnt.value != '')
  {

    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/altera_dado_conferencia.php", //chama uma pagina
    data: "valor="+elmnt.value + "&conferencia_id="+conferencia_id + "&numero_campo="+numero_campo, //parametros
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);

      if(msg.indexOf("ok") == -1)
      {
        $('#msg').html(msg);
        $('#myModal_alert').modal('show');
      }

    }
  });
}
}


function altera_data(elmnt, id, numero_campo){
  //alert(id);

  if (elmnt.value != '')
  {

    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/altera_data.php", //chama uma pagina
    data: "valor="+elmnt.value + "&id="+id + "&numero_campo="+numero_campo, //parametros
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);

      if(msg.indexOf("ok") == -1)
      {
        $('#msg').html(msg);
        $('#myModal_alert').modal('show');
      }

    }
  });
}
}

function conferir_valores(){
  //alert(id);

  var mes_ano = document.form1.periodo.value;

  //alert(mes_ano);

  if (mes_ano != '')
  {

    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/confere_valores.php", //chama uma pagina
    data: "mes_ano="+mes_ano, //parametros
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);

      if(msg.indexOf("ok") == -1)
      {
        $('#msg').html(msg);
        $('#myModal_alert').modal('show');
      }
      else
      {
        $('#msg_success').html('Conferência efetuada com sucesso');
        $('#myModal_alert_sucess').modal('show');

        busca_conferencia(mes_ano);
      }

    }
  });
}
}

function gera_vale_boletim(){
  //alert(id);

  var mes_ano = document.form1.periodo.value;

  if (mes_ano != '')
  {

    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/gera_vale_boletim.php", //chama uma pagina
    data: "mes_ano="+mes_ano, //parametros
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);

      if(msg.indexOf("ok") == -1)
      {
        $('#msg').html(msg);
        $('#myModal_alert').modal('show');
      }
      else
      {
        $('#msg_success').html('Vales gerados com sucesso');
        $('#myModal_alert_sucess').modal('show');

        busca_conferencia(mes_ano);
      }

    }
  });
}
}


function recalcula_valores(){
  //alert(id);

  var mes_ano = document.form1.periodo.value;

  if (mes_ano != '')
  {

    $.ajax({type: "POST",//define o metódo de passagem de parametros
    url: "includes/recalcula_valores.php", //chama uma pagina
    data: "mes_ano="+mes_ano, //parametros
    success: function(msg){  //pega o retorno da pagina chamada
      //alert(msg);

      if(msg.indexOf("ok") == -1)
      {
        $('#msg').html(msg);
        $('#myModal_alert').modal('show');
      }
      else
      {
        $('#msg_success').html('Valores recalculados com sucesso');
        $('#myModal_alert_sucess').modal('show');

        busca_conferencia(mes_ano);
      }

    }
  });
}
}


function seleciona_aparelho(elmnt, aparelho_id){
  //alert(elmnt.checked);

  if (elmnt.checked == true)
  {
    if (document.form1.aparelho_enviado.value == '')
    document.form1.aparelho_enviado.value = aparelho_id;
    else
    {
      var conteudo = document.form1.aparelho_enviado.value;

      document.form1.aparelho_enviado.value = conteudo+","+aparelho_id;
    }
  }
  else
  {
    var conteudo = document.form1.aparelho_enviado.value;

    var conteudo1 = conteudo.replace(","+aparelho_id, "");
    document.form1.aparelho_enviado.value = conteudo1.replace(aparelho_id, "");
  }
}


function seleciona_linha(elmnt, celular){
  //alert(elmnt.checked);

  if (elmnt.checked == true)
  {
    if (document.form1.celular_enviado.value == '')
    document.form1.celular_enviado.value = celular;
    else
    {
      var conteudo = document.form1.celular_enviado.value;

      document.form1.celular_enviado.value = conteudo+","+celular;
    }
  }
  else
  {
    var conteudo = document.form1.celular_enviado.value;

    var conteudo1 = conteudo.replace(","+celular, "");
    document.form1.celular_enviado.value = conteudo1.replace(celular, "");
  }
}



function exibe_cp (id, num_celular) {

  var dados = "<div class='col-xs-12 left-inline'><input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1'  value='"+num_celular+"' size='12' maxlength='14' onblur='javascript:altera_celular(this,"+id+")'></div>";
  //alert();
  console.log(dados);
  $("#"+id).html(dados);

  $("#"+id).show();
}
