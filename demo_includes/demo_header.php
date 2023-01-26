<?php

//includes here


//php wrappers here


function build_modal($id = "exampleModal", $buttonLabel = "boton modal" , $title = "titulo", $content = "content", $buttonAction = "alert('working');" , $extrabutton = false, $preloadAction = false){
	
	?>
	<script>
	
	function modalfunc_<?=$id?>(){
		
		<?=$buttonAction?>
		
	}
	
	function preload_click_<?=$id?>(){
		
		<?=($preloadAction?$preloadAction:"")?>
		
	}
	
	
	</script>
		<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#<?=$id?>" onClick="preload_click_<?=$id?>()">
  <?=$buttonLabel?>
</button>

<!-- Modal -->
<div class="modal fade" id="<?=$id?>" tabindex="-1" role="dialog" aria-labelledby="<?=$id?>Label" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 80% !important;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="<?=$id?>Label"><?=$title?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <?=$content?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
		<?=$extrabutton?>
        <button type="button" class="btn btn-primary" onClick="modalfunc_<?=$id?>()">Grabar</button>
      </div>
    </div>
  </div>
</div>

	<?php
	
}//end build_modal



/*

<link rel="stylesheet" href="http://localhost/hsfjsys/css/pure.css">
<link rel="stylesheet" href="http://localhost/hsfjsys/css/pure_custom.css">
<link rel="stylesheet" type="text/css" href="http://localhost/hsfjsys/style2.css">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" href="http://localhost/hsfjsys/includes/js2020/font-awesome.min.css">
	<link rel="stylesheet" href="http://localhost/hsfjsys/includes/js2020/style.css">
	<link rel="stylesheet" href="http://localhost/hsfjsys/includes/nav4/nav4.css">
	 
	 
	<script src="http://localhost/hsfjsys/includes/nav4/nav4.js"></script>
	 
*/


?><html>
<head>
<link rel="shortcut icon" type="image/png" href="demo_icons/favicon.ico" />
<link rel="icon" href="demo_icons/favicon.ico" type="image/x-icon"/>

<title>##</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">



<script src="demo_includes/jquery/jquery.js" ></script>
  <script src="demo_includes/bootstrap4/popper.js"></script>
<link rel="stylesheet" href="demo_includes/bootstrap4/bootstrap.min.css">
    <script src="demo_includes/bootstrap4/bootstrap.min.js"></script>
	
	</head>
	<body>