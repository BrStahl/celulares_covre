<?php
require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";

    $con = new ConexaoSCI;
    
    $keyword = $_POST["keyword"];


	$query = "select nome
			  from usuario with (nolock)
			  where status = 'a'
			  and nome like '$keyword%'
			  order by nome";
	$result = $con->select($query);
    // print_r($result);
    // if(!empty($result)) {
    // ?>
     <ul class='select-list ' id="select-list" style="width:500px">
     <?php
    foreach($result as $autocomplete) {
    // ?>
     <li onClick="selectList('<?php echo $autocomplete["nome"]; ?>');"><?php echo $autocomplete["nome"]; ?></li>
     <?php } ?>
     </ul>