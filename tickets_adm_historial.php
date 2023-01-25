<?php
	$modulo='administracion';
	include("../../subadmin.php");
	
$devs = Array();


$statuses = Array();
$statuses[0] = "Abiertos";
$statuses[1] = "Planeaci&oacute;n";
$statuses[2] = "En Proceso";
$statuses[3] = "Por Revisar";
$statuses[4] = "Producci&oacute;n";

$deptos = Array();
$deptos[0] = "Prueba1";
$deptos[1] = "Prueba depto 2";

function build_devs_select($devs,$assigned){
	
	$out = "<option value=''></option>";
	
	foreach($devs as $dev){
		$e = "";
		if($dev == $assigned)$e = "SELECTED";
		$out .= "<option value='$dev' $e>$dev</option>";
	}
	
	return $out;
	
}

function build_deptos_select($deptos,$depto){
	
	$out1 = "<option value=''></option>";
	
	foreach($deptos as $dep){
		$d = "";
		if($dep == $depto)$d = "SELECTED";
		$out1 .= "<option value='$dep' $d>$dep</option>";
	}
	
	return $out1;
	
}

/// funciones ////


function get_abrev($name){
	
	$abbr = "";
	$pieces = explode(" ", $name);
	
	$abbr .= $pieces[0][0];
	$abbr .= $pieces[1][0];
	
	return $abbr;
	
}

function get_priority($pri){
	
	$pri = intval($pri);
	$setColor = 1;
	
	$colors = Array();
	$colors[0] = "#5ccaf2";
	$colors[1] = "#8dd929";
	$colors[2] = "#e3735d";
	
	if($pri < -25)$setColor = 0;
	if($pri > 25)$setColor = 2;
	
	return '<div class="circleHolder" style="background:'.$colors[$setColor].';">&nbsp;</div>';
	

}

function getTicketType($typechar){
	
	$imgurl = "";
	
	if($typechar == "f" || !$typechar)$imgurl = "gear.png";
	if($typechar == "b")$imgurl = "bug.png";
		
	return "<img src='imagenes/$imgurl ' class='typeIcon'>";
}

function get_modal_contents($obj = Array()){
	GLOBAL $devs, $statuses, $deptos;
	
	$action  = ($obj["id"]?"<input type='hidden' name='id' value='".$obj["id"]."'><input type='hidden' name='action' value='saveChanges'>":"<input type='hidden' name='action' value='saveNew'>");
	$notes = ($obj["id"]?"<tr><td>Notas:</td><td><textarea name='notes' style='width: 95%;height:100px;' disabled>".$obj["notes"]."</textarea></td></tr>":"");
	$priority = ($obj["id"]?"<tr><td>Prioridad: </td><td><input type='number' name='priority' style='width:50px;text-align:right;border: 1px solid #c0c0c0;' value='".$obj["priority"]."' disabled> (-100 a 100)</td></tr>":"");
	$estimate = ($obj["id"]?"<tr><td>Estimado: </td><td><input type='number' name='estimate' step='.5' style='width:50px;text-align:right;border: 1px solid #c0c0c0;' value='".$obj["estimate"]."' disabled> Dias</td></tr>":"");
	$progress = ($obj["id"]?"<tr><td>Progreso: </td><td><input type='number' name='progress' step='1' max='100' min='0' style='width:50px;text-align:right;border: 1px solid #c0c0c0;' value='".$obj["progress"]."' disabled> %</td></tr>":"");
	$assigned = ($obj["id"]?"<tr><td>Asignado A: </td><td><select name='assigned' disabled>".build_devs_select($devs,$obj["assigned"])."</select></td></tr>":"");
	// $depto = ($obj["id"]?"<tr><td>Departamento: </td><td><select name='dep'>".build_deptos_select($deptos,$obj["depto"])."</select></td></tr>":"");
	$files = ( substr($obj["filesrc"],0,1)=="[" ? json_decode($obj["filesrc"]) : get_empty_files($obj["filesrc"]) );
	$filesrc = ""; 
	
	if(sizeof($files)>0){
		$filesrc .= "<tr><td><b>Archivos:</b></td><td id='fileSpace".$obj["id"]."'>"; 
		foreach($files as $file){
			$file = (array) $file;
			$filesrc .= "<a href='".$file["src"]."' target='_new67'>".$file["name"]."</a><br>";
		}
		$filesrc .= "</td></tr>"; 
	}else{
		$filesrc .= "<tr><td><b>Archivos:</b></td><td id=''><i>Sin Archivos</i></td></tr>"; 
	}
	
	$filesrc .= "<tr><td><b>Cargar Archivos:</b> </td><td> <input type='file' name='upfile[]' id='upfile' size='40' multiple disabled> </td></tr>"; 
	
	
	//create content
	$modalContent = "<form action='tickets_adm.php' method='POST' enctype='multipart/form-data' id='modalForm".$obj["id"]."' style='text-align:left;' >
						<table style='min-width: 550px;'>
						<tr><td>Titulo: </td><td><input type='text' name='title' style='min-width:95%;margin-left:0px !important;' value='".$obj["title"]."' disabled></td></tr>
						<tr><td>Tipo: </td><td>
							<select name='type' disabled>
								<option value='feature'>Feature</option>
								<option value='bug' ".($obj["type"] == 'bug'?"selected":"").">Bug</option>
							</select>
						</td></tr>
						<tr><td>Departamento: </td><td><select name='depto' disabled>".build_deptos_select($deptos,$obj["depto"])."</select></td></tr>
						$priority
						$estimate
						$progress
						$assigned
						$filesrc
						<tr><td>Descripcion: </td><td><textarea name='desc' style='width: 95%;height:160px;' disabled>".$obj["desc"]."</textarea></td></tr>
						$notes
						</table>
						$action
					</form>";
	
	
	return $modalContent;
	}

function makeButton($obj){
	GLOBAL $statuses;
	
	$status = intval($obj["status"]);
	$nextStatus = intval($obj["status"])+1;
	$backStatus = intval($obj["status"])-1;
	
	$labelNext = $statuses[$nextStatus];
	$labelBack = $statuses[$backStatus];
	
	return '<div class="btn-group">
			
		  <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		  </button>
		  <div class="dropdown-menu" style="border: 1px solid #cfcfcf !important;">
			<a class="dropdown-item" href="javascript:change_status('.$obj["id"].' , '.(intval($obj["status"])+1).')">Marcar \''.$labelNext.'\'</a>
			'.($status>0?'<a class="dropdown-item" href="javascript:change_status('.$obj["id"].' , '.(intval($obj["status"])-1).')">Regresar A \''.$labelBack.'\'</a>':'').'
			<a class="dropdown-item" href="#">Something else here</a>
			<div class="dropdown-divider"></div>
			<a class="dropdown-item" href="#">Eliminar</a>
		  </div>
		</div>';
		

}

function makeCard_boot4($obj){
	
	ob_start(); 
	$ob = build_modal3("details".$obj["id"], "Ver Mas" , "Detalles de #".$obj["id"], get_modal_contents($obj), "document.getElementById('modalForm".$obj["id"]."').submit();" , false, "() => {}");
	$build_modal_output = ob_get_contents();
	ob_end_clean(); 
	return '<div class="card" style="width: 95%;margin-bottom: 3px;">
			  <div class="card-body">
			  <div style="text-align: left;margin-bottom: 10px;">
				
				
				<table style="width:100%;margin-bottom:5px;"><tr>
				<td>#'.$obj["id"].' - '.$obj["title"].'</b></td>
				</tr></table>
				
				
				<table style="width: 100%;"><tr>
					<td>
					'.getTicketType($obj["type"][0]).'
					</td>
					<td>
						<div class="circleHolder">'.($obj["assigned"]?get_abrev($obj["assigned"]):"N/A").'</div>					
					</td>
					<td>
						'.get_priority($obj["priority"]).'
					</td>
					<td align="right">
						'.$build_modal_output.'
					</td>
					</tr>
				</table>
					
				</div>
				
			  </div>
			</div>';
	
}

//begin first pull

$tickets = Array();

$query = "SELECT * FROM tickets_adm WHERE completed = '1' OR deleted='1' ORDER BY deleted ASC, id DESC";
$res = mysql_query($query);
while($row = mysql_fetch_assoc($res)){
	
	$tickets[] = $row;
	
} 
?>
<style>


.cardWrapper{
	margin: 3px;
	width: 90%;
}

.cardDisplay{
	box-sizing: border-box;
	padding: 10px;
	background: #ffffff;
	border-radius: 12px;
	width: 100%;
	border: 1px solid #efefef;
}

.smallButton{
	border-radius: 8px;
	background: #5761f7;
	color: #ffffff;
	margin: 2px 5px;
}

.cardDesc{
	margin-bottom: 7px;
	text-align: left;
}


.circleHolder{
	text-align:center;
	padding:4px 2px; 
	border-radius: 12px;
	background: #cfcfcf;
	margin-right:5px;
	min-width: 22px;
}

.panelContainer{
	margin-left:10px;
	width: 30%;
	display: flex;
	flex: 1;
	flex-direction: row;
}

.panelColumn{
	box-sizing: border-box;
	border: 3px solid #dfdfdf;
	background: #f5f5f5;
	min-height: 100vh;
	vertical-align: top;
	text-align: center;
	flex: 1;
}

.newButtonHolder{
	
	left: 5%;
	top: 5%;
	position: absolute;
}

.niceButton{
	background: #5761f7;
	border-radius: 50%;
	padding: 0px 20px;
	font-size: 300%;
	color: #ffffff;
}

.newButton{
	margin-bottom:5px;
}

.typeIcon{
		
	width: 22px;
	height: 22px;
}

</style>
<div class="container p-3 my-3 border">
	<h4>Tickets Completados</h4>
		<?php 
			foreach($tickets as $ticket){
				echo '<h6>Terminado:'.$ticket["ts_completed"].'</h6>';
				echo makeCard_boot4($ticket); 
				echo "<br>";
			}
		?>	
</div>

<?php

include("$pathsys/includes/footer.php");

?>