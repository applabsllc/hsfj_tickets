<?php
	
	include("demo_includes/demo_config.php");
	include("demo_includes/demo_header.php");
	

$esPersonal = true;
$esEncargado = true;
$fpath=$file_path;
$filepath = "index.php";

//definir personal
$devs = Array();
$devs[0] = "Andres";
$devs[1] = "Hector";
$devs[2] = "Pedro";

$statuses = Array();
$statuses[0] = "Planeaci&oacute;n";
$statuses[1] = "Disponibles";
$statuses[2] = "En Proceso";
$statuses[3] = "Por Revisar ( PR )";
$statuses[4] = "Aprobado";
$statuses[5] = "Terminado";

//definir prioridades
$prioritys = Array();
$prioritys[25] = "Baja";
$prioritys[50] = "Media";
$prioritys[75] = "Alta";
$prioritys[100] = "Urgente";

//definir deptos
$deptos = Array();

$query = "SELECT id,nombre FROM tickets_adm_deptos ORDER BY nombre";
$res = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($res)){
	
	$deptos[$row["id"]] = $row["nombre"];
	
}




if($action == "saveChanges"){
	
	$query = "SELECT filesrc FROM tickets_adm
	WHERE id='$id' LIMIT 1";
	$res = mysqli_query($conn,$query);
	while($row = mysqli_fetch_assoc($res)){
	
		//$files = json_decode($row['filesrc']);
		$files = ( substr($row["filesrc"],0,1)=="[" ? json_decode($row["filesrc"]) : get_empty_files($row["filesrc"]) );
	}  
	
	if($_FILES)
	foreach($_FILES['upfile']['name'] as $key=>$file){ 
		if($file){ 
			$rand=mt_rand(50, 15000);
			$filesrc=$rand."_".$file;
			$filesrc=str_replace(" ","",$filesrc);
			move_uploaded_file($_FILES['upfile']["tmp_name"][$key], $fpath.$filesrc );
			$files[] = array('src'=>$fpath.$filesrc, 'name'=>$file);
		}
	} 
	
	$files = json_encode($files); 
	$desc = clear_str_acentos($desc);
	$query = "UPDATE tickets_adm SET 
	`title` = '$title' ,
	`type` = '$type' ,
	`priority` = '$priority' ,
	`estimate` = '$estimate' ,
	`progress` = '$progress' ,
	`assigned` = '$assigned' ,
	`filesrc` = '$files',
	`estimatecost` = '$estimatecost',
	`finalcost` = '$finalcost',
	`desc` = '$desc' ,
	`depto` = '$depto' 
	WHERE id='$id' LIMIT 1";
	
	//die(var_dump($query));
	
	$res = mysqli_query($conn, $query);
	
	die("Grabando... <script>window.open('".$filepath."?msg=Cambios Grabados&message=Cambios Grabados','_self');</script>");
	
}

if($action == "changeStatus"){
	//die(var_dump($_GET));
	$val_status = "SELECT `status` FROM tickets_adm WHERE id='$id' LIMIT 1";
	$res_status = mysqli_query($conn, $val_status);
	$row = mysqli_fetch_row($res_status);
	
	//condicion para actualizar ts_planeacion solo si el status es mayor al actual
	if (($status > $row[0]) && $status == '3') {
		$query = "UPDATE tickets_adm SET `status` = '$status', `ts_planeacion` = CURRENT_TIMESTAMP, `ts_progreso` = CURRENT_TIMESTAMP WHERE id='$id' LIMIT 1 ";
		//die($query);
		$res = mysqli_query($conn,$query);
	}else{
		$query = "UPDATE tickets_adm SET `status` = '$status' WHERE id='$id' LIMIT 1 ";
		//die($query);
		$res = mysqli_query($conn,$query);
	}
	
	
	if(intval($status) >= 5){
		
		$query = "UPDATE tickets_adm SET `completed` = '1', `ts_completed` = CURRENT_TIMESTAMP WHERE id='$id' LIMIT 1 ";
	//die($query);
	$res = mysqli_query($conn,$query);
		
	}
	
	die("Grabando... <script>window.open('".$filepath."','_self');</script>");
	
}

if($action == "filterDepto"){
	// echo($value);
	$tickets = Array();

	$query = "SELECT * FROM tickets_adm WHERE completed = '0' AND deleted='0' AND depto = '$value' ORDER BY priority DESC, id DESC";
	$res = mysqli_query($conn,$query);
	while($row = mysqli_fetch_assoc($res)){
		
		$tickets[$row["status"]][] = $row;
		
	}

	if($value == 'All' || $value == ""){
		
		$query = "SELECT * FROM tickets_adm WHERE completed = '0' AND deleted='0' ORDER BY priority DESC, id DESC ";
		$res = mysqli_query($conn,$query);
		while($row = mysqli_fetch_assoc($res)){
		
			$tickets[$row["status"]][] = $row;
			// var_dump($row);
		}
			
	}

}elseif($action == "filterPers"){
	// echo($value);
	$tickets = Array();

	$query = "SELECT * FROM tickets_adm WHERE completed = '0' AND deleted='0' AND assigned = '$value' ORDER BY priority DESC, id DESC";
	$res = mysqli_query($conn,$query);
	while($row = mysqli_fetch_assoc($res)){
		
		$tickets[$row["status"]][] = $row;
		// var_dump($row);
	}

	if($value == 'All' || $value == ""){
		
		$query = "SELECT * FROM tickets_adm WHERE completed = '0' AND deleted='0' ORDER BY priority DESC, id DESC ";
		$res = mysqli_query($conn,$query);
		while($row = mysqli_fetch_assoc($res)){
		
			$tickets[$row["status"]][] = $row;
			
		}
			
	}
	
}else{
	$tickets = Array();

	$query = "SELECT * FROM tickets_adm WHERE completed = '0' AND deleted='0' ORDER BY priority DESC, id DESC";
	$res = mysqli_query($conn,$query);
	while($row = mysqli_fetch_assoc($res)){
		
		$tickets[$row["status"]][] = $row;
		
	}
}

if($action == "saveNew"){
	 
	$files = Array(); 
	foreach($_FILES['upfile']['name'] as $key=>$file){ 
		if($file){ 
			$rand=mt_rand(50, 15000);
			$filesrc=$rand."_".$file;
			$filesrc=str_replace(" ","",$filesrc);
			move_uploaded_file($_FILES['upfile']["tmp_name"][$key], $fpath.$filesrc );
			$files[] = array('src'=>$fpath.$filesrc, 'name'=>$file);
		}
	}
	
	$files = json_encode($files);
	$desc = clear_str_acentos($desc);
	$query = "INSERT INTO tickets_adm (
		`type` ,
		`title` ,
		`status` ,
		`ts_created` ,
		`owner` ,
		`filesrc` ,
		`desc` ,
		`depto`,
		`estimatecost`,
		`finalcost`,
		`priority`,
		`ts_progreso`

		)
		VALUES (
		'$type', '$title', '0',
		CURRENT_TIMESTAMP , '$Usuario', '$files', '$desc', '$depto', '$estimatecost', '$finalcost', '$priority', CURRENT_TIMESTAMP)";
	
	
	$res = mysqli_query($conn,$query);

	die("Grabando... <script>window.open('".$filepath."?msg=Creado Exitosamente&message=Creado Exitosamente','_self');</script>");
	
}

if($action == "saveNotes"){
	
	$query = "SELECT * FROM tickets_adm WHERE id='$id' LIMIT 1";
	$res = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($res);
	
	
	$list = json_decode($row["notes"] && substr($row["notes"],0,1)=="[" ? $row["notes"] : "[]");
	
	
	$files = Array(); 
	foreach($_FILES['upfile']['name'] as $key=>$file){ 
		if($file){ 
			$rand=mt_rand(50, 15000);
			$filesrc=$rand."_".$file;
			$filesrc=str_replace(" ","",$filesrc);
			move_uploaded_file($_FILES['upfile']["tmp_name"][$key], $fpath.$filesrc );
			$files[] = array('src'=>$fpath.$filesrc, 'name'=>$file);
		}
	}
	
	$newNote = Array();
	$newNote["note"] = clear_str_acentos($notes);
	$newNote["ts"] = $Usuario." - ".date('Y/m/d H:s:i', time());
	$newNote["files"] = $files;
	
	$list[] = $newNote;
	
	$query = "UPDATE tickets_adm SET `notes` = '".json_encode($list)."' WHERE id='$id' LIMIT 1 ";
	$res = mysqli_query($conn,$query);

	 //die(var_dump($_POST));
	 die("Grabando... <script>window.open('".$filepath."?msg=Nota Grabada&message=Nota Grabada&openModal=$id".($filterDepto ? "&action=filterDepto&value=".$filterDepto : "")."','_self');</script>");
	
}

if($action == "saveDepto"){
	// var_dump($_GET);
	// $query = "UPDATE tickets_adm7_deptos SET `status` = '$status' WHERE id='$id' LIMIT 1 ";
	$query = "INSERT INTO tickets_adm_deptos (
		`nombre` ,
		`padre` ,
		`encargado`
		)
		VALUES (
		'$nombre', 
		'0', 
		'0')";
	
	$res = mysqli_query($conn, $query);
	//die($query);
	die("Grabando... <script>window.open('".$filepath."?msg=Departamento Creado Exitosamente&message=Departamento Creado Exitosamente','_self');</script>");
	
}
 
///end actions////

///// funciones ////

function clear_str_acentos($str){
		
	
	$str = utf8_encode($str);
	
	$str = str_replace(utf8_decode(utf8_encode("Ñ")), "&Ntilde;", $str);
	$str = str_replace(utf8_decode(utf8_encode("ñ")), "&ntilde;", $str);
	
	$str = str_replace(utf8_decode(utf8_encode("á")), "&aacute;", $str);
	$str = str_replace(utf8_decode(utf8_encode("Á")), "&Aacute;", $str);
	
	$str = str_replace(utf8_decode(utf8_encode("é")), "&eacute;", $str);
	$str = str_replace(utf8_decode(utf8_encode("É")), "&Eacute;", $str);
	
	$str = str_replace(utf8_decode(utf8_encode("í")), "&iacute;", $str);
	$str = str_replace(utf8_decode(utf8_encode("Í")), "&Iacute;", $str);
	
	$str = str_replace(utf8_decode(utf8_encode("ó")), "&oacute;", $str);
	$str = str_replace(utf8_decode(utf8_encode("Ó")), "&Oacute;", $str);
	
	$str = str_replace(utf8_decode(utf8_encode("Ú")), "&Uacute;", $str);
	$str = str_replace(utf8_decode(utf8_encode("ú")), "&uacute;", $str);
	
	return $str;
	

	}


		
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

function build_priority_select($prioritys,$priority){
	
	$out2 = "<option value=''></option>";
	foreach($prioritys as $key=>$prior){
		$d = "";
		if($key == $priority)$d = "SELECTED";
			$out2 .= "<option value=$key $d>$prior</option>";
	}
	
	return $out2;
	
}

function deptos_select($deptos,$value){
	// echo($value);
	$d = "";
	$out1 = "<option value='All'>Ver Todos</option>";
	
	foreach($deptos as $dep){
		
		if($dep == $value){
			$d = "SELECTED";
			$out1 .= "<option value='$dep' $d>$dep</option>";
		}else{
			$out1 .= "<option value='$dep' >$dep</option>";
		}

	}
	
	return $out1;
	
}

function pers_select($devs,$value){
	// echo($value);
	$d = "";
	$out1 = "<option value='All'>Ver Todos</option>";
	
	foreach($devs as $dev){
		// echo($value);
		if($dev == $value){
			$d = "SELECTED";
			$out1 .= "<option value='$dev'$d>$dev</option>";
		}else{
			$out1 .= "<option value='$dev'>$dev</option>";
		}

	}
	
	return $out1;
	
}


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
	$colors[0] = "#8DD929"; //verde
	$colors[1] = "#FCFF33"; //amarillo
	$colors[2] = "#E3735D"; //rojo
	$colors[3] = "#FF3333"; //rojo
	
	if($pri <= 25)$setColor = 0;
	if($pri <= 50 && $pri > 25 )$setColor = 1;
	if($pri <= 75 && $pri > 50)$setColor = 2;
	if($pri > 75)$setColor = 3;
	
	return '<div class="circleHolder" style="background:'.$colors[$setColor].';">&nbsp;</div>';
	

}

function getTicketType($typechar){
	
	$imgurl = "";
	
	if($typechar == "f" || !$typechar)$imgurl = "request.png";
	if($typechar == "b")$imgurl = "repair.png";
		
	return "<img src='imagenes/$imgurl ' class='typeIcon'>";
}

function get_empty_files($filesrc, $files = Array()){
	GLOBAL $fpath;
	if(strlen($filesrc))
	$files[] = Array('src'=>$fpath.$filesrc, 'name'=>$filesrc);
	return $files;
}

///definir forma de modal
function get_modal_contents($obj = Array()){
GLOBAL $devs, $statuses, $deptos, $prioritys;

$action  = ($obj["id"]?"<input type='hidden' name='id' value='".$obj["id"]."'><input type='hidden' name='action' value='saveChanges'>":"<input type='hidden' name='action' value='saveNew'>");

// $priority = ($obj["id"]?"<tr><td>Prioridad: </td><td><input type='number' name='priority' class='espaciado' style='width:50px;text-align:right;border: 1px solid #c0c0c0;' value='".$obj["priority"]."'> (-100 a 100)</td></tr>":"");
$estimate = ($obj["id"]?"<tr><td>Estimado: </td><td><input type='number' name='estimate' class='espaciado' step='.5' style='width:50px;text-align:right;border: 1px solid #c0c0c0;' value='".$obj["estimate"]."'> Dias</td></tr>":"");
$progress = ($obj["id"]?"<tr><td>Progreso: </td><td><input type='number' name='progress' class='espaciado' step='1' max='100' min='0' style='width:50px;text-align:right;border: 1px solid #c0c0c0;' value='".$obj["progress"]."'> %</td></tr>":"");
$assigned = ($obj["id"]?"<tr><td>Asignado A: </td><td><select name='assigned' class='espaciado'>".build_devs_select($devs,$obj["assigned"])."</select></td></tr>":"");
$files = ( substr($obj["filesrc"],0,1)=="[" ? json_decode($obj["filesrc"]) : get_empty_files($obj["filesrc"]) );
$filesrc = ""; 

if(sizeof($files)>0){
	$filesrc .= "<tr><td><b>Archivos:</b></td><td id='fileSpace".$obj["id"]."'>"; 
	foreach($files as $file){
		$file = (array) $file;
		//$filesrc .= "<a href='".$file["src"]."' target='_new67'>".$file["name"]."</a><br>";
		$filesrc .= "<div class='archivo' onClick='window.open(\"".$file["src"]."\",\"_new67\")'>".$file["name"]."</div>";
	}
	$filesrc .= "</td></tr>"; 
}else{
	$filesrc .= "<tr><td><b>Archivos:</b></td><td id=''><i>Sin Archivos</i></td></tr>"; 
}

$filesrc .= "<tr><td><b>Cargar Archivos:</b> </td><td> <input type='file' name='upfile[]' class='espaciado' id='upfile' size='40' multiple> </td></tr>"; 


//create content
$modalContent = "<div style='display:inline-block;width: 45%;'>
					<form action='' method='POST' enctype='multipart/form-data' id='modalForm".$obj["id"]."' style='text-align:left;' >
				
						<table style='width: 95%;'>
						<tr><td>Titulo: </td><td><input type='text' class='espaciado' name='title' style='min-width:95%;margin-left:0px !important;' value='".$obj["title"]."'></td></tr>
						<tr><td>Tipo: </td><td>
							<select name='type' class='espaciado'>
								<option value='feature'>Trabajo Nuevo</option>
								<option value='bug' ".($obj["type"] == 'bug'?"selected":"").">Reparaci&oacute;n</option>
							</select>
						</td></tr>
						<tr><td>Departamento: </td><td><select name='depto' id='new_depto' class='espaciado'>".build_deptos_select($deptos,$obj["depto"])."</select></td></tr>
						<tr><td>Prioridad: </td><td><select name='priority' id='new_priority' class='espaciado'>".build_priority_select($prioritys,$obj["priority"])."</select></td></tr>
						$estimate
						$progress
						$assigned
						<tr><td>Costo Estimado: </td><td><input type='number' name='estimatecost' class='espaciado' step='1' min='0' style='width:100px;text-align:right;border: 1px solid #c0c0c0;' value=".$obj["estimatecost"]."> $</td></tr>
						<tr><td>Costo Final: </td><td><input type='number' name='finalcost' class='espaciado' step='1' min='0' style='width:100px;text-align:right;border: 1px solid #c0c0c0;' value=".$obj["finalcost"]."> $</td></tr>
						$filesrc
						<tr><td>Descripci&oacute;n: </td><td><textarea name='desc' class='espaciado' style='width: 95%;height:160px;'>".$obj["desc"]."</textarea></td></tr>
						
						</table>
					$action
					</form>
					</div>";

if($obj["id"]){
		
	$notesList = (is_array(json_decode($obj["notes"])) ? array_reverse(json_decode($obj["notes"])) : Array() );
	$notesHTML = "";

	foreach($notesList as $list){
		
		$notesHTML .= "<div class='nota'>".$list->note."<div style='margin-top:5px;'>";
		$files = $list->files;
		foreach($files as $file){
			
			$notesHTML .= "<div class='archivo' onClick='window.open(\"".$file->src."\",\"_new67\")'>".$file->name."</div>";
			
		}
		
		$notesHTML .= "</div><div style='text-align:right;'><i style='font-size:80%;opacity:.85;'>".$list->ts."</i></div></div>";
	}

	$modalContent .= "<div style='display:inline-block;width: 45%;vertical-align:top;border-left:1px solid #cfcfcf;padding-left:15px;'>
						<form action='' method='POST' enctype='multipart/form-data' id='' style='text-align:left;' >
						<h6>Notas:</h6>
						<div style='height:270px;overflow:auto;'>
						$notesHTML
						</div>
						<hr>
						<table style='width: 95%;'>
						<tr><td>Agregar Nota:</td><td><textarea name='notes' class='espaciado' style='width: 95%;height:80px;'></textarea></td></tr>
						<tr><td>Archivo(s):</td><td><input type='file' name='upfile[]' class='espaciado' id='upfile' size='40' multiple></td></tr>
						</table>
						<br>
						<center>
						<button type='submit' class='btn btn-primary btn-sm'>Grabar Nota</button>
						</center>
						<input type='hidden' name='action' value='saveNotes'>
						<input type='hidden' name='id' value='".$obj["id"]."'>
						<input type='hidden' name='filterDepto' value='".$_GET["value"]."'>
						</form>
					</div>
				";
}

return $modalContent;
}


function get_modal_contents_deptos(){
GLOBAL $devs, $statuses, $deptos;

$listaDeptos = "<option></option>";

foreach($deptos as $dep)
$listaDeptos .= "<option value='$dep'>$dep</option>";

$modalContent = "<form action='' method='POST' enctype='multipart/form-data' id='deptoForm' style='text-align:left;' >
					<input type='hidden' name='action' value='saveDepto'>
					<div style=''>
						<table style='width: 95%;'>
						<tr><td>Nombre: </td><td><input type='text' name='nombre' style='min-width:95%;margin-left:0px !important;' value='".$obj["nombre"]."'></td></tr>
						
						</table>
					
					</div>
					
				</form>
				<hr>
				<table style='width: 95%;'>
				<tr><td>
				Eliminar: 
				</td><td>
				<select id='listaEliminaDeptos'>
				$listaDeptos
				</select>
				<button type='button' class='btn btn-primary btn-sm' onClick='elimina_depto(document.getElementById(\"listaEliminaDeptos\").value)'>Eliminar</button>
				</td></tr>
				</table>";


return $modalContent;
}

function makeButton($obj){
	GLOBAL $statuses, $esPersonal, $esEncargado;
	
	$status = intval($obj["status"]);
	$nextStatus = intval($obj["status"])+1;
	$backStatus = intval($obj["status"])-1;
	
	$labelNext = $statuses[$nextStatus];
	$labelBack = $statuses[$backStatus];

	
	return '<div class="btn-group">
			
		  <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		  </button>
		  <div class="dropdown-menu" style="border: 1px solid #cfcfcf !important;">
			<a class="dropdown-item" href="javascript:change_status('.$obj["id"].' , '.(intval($obj["status"])+1).', '.($obj["estimate"] && $obj["assigned"] ? "true" : "false").')">Marcar \''.$labelNext.'\'</a>
			'.($status>0?'<a class="dropdown-item" href="javascript:change_status('.$obj["id"].' , '.(intval($obj["status"])-1).')">Regresar A \''.$labelBack.'\'</a>':'').'
			<!-- <a class="dropdown-item" href="#">Something else here</a> -->
			<div class="dropdown-divider"></div>
			<a class="dropdown-item" href="#">Eliminar</a>
		  </div>
		</div>';
		
}

function makeCard_boot4($obj){
	
	ob_start();
	$ob = build_modal("details".$obj["id"], "Ver Mas" , "<b>".$obj["title"]."</b>", get_modal_contents($obj), "document.getElementById('modalForm".$obj["id"]."').submit();" , false, "() => {}");
	$build_modal_output = ob_get_contents();
	ob_end_clean();

	$start_ts = strtotime($obj["ts_progreso"]);
	$today = strtotime('today');

	if ($start_ts != null) {
		$diff = $today - $start_ts;
		$days_elapsed = round($diff / (60 * 60 * 24));
	} else {
		$days_elapsed = '0';
	}

	if (($days_elapsed < $obj["estimate"]) || $obj["estimate"] == '0') {
		$estimateTime = '<div class="circleHolder">'.($obj["estimate"] ? " <b> " .$days_elapsed. " / " . $obj["estimate"]. "</b> " : "n/a").'</div>';
	} else {
		$estimateTime = '<div class="dayExceeded">'.($obj["estimate"] ? " <b> " .$days_elapsed. " / " . $obj["estimate"]. "</b> " : "n/a").'</div>';
	}
	
	$progress_div = '<div class="progress" style="margin-top:10px;">
					  	<div class="progress-bar  bg-info" role="progressbar" style="width: '.$obj["progress"].'%;" aria-valuenow="'.$obj["progress"].'" aria-valuemin="0" aria-valuemax="100">'.($obj["progress"]?$obj["progress"]."%":"").'</div>
					</div>';
					
	return '<div class="card" style="width: 95%;margin-bottom: 3px;">
			  <div class="card-body" style="padding-bottom:10px !important;">
			  <div style="text-align: left;margin-bottom: 10px;">
				
				
				<table style="width:100%;margin-bottom:5px;"><tr>
				<td>['.$obj["id"].'] '.$obj["title"].'<br>
				<b>'.$obj["depto"].'</b></td>
				<td align="right" valign="top">
				'.makeButton($obj).'
				</td>
				</tr></table>
				
				<table style="width: 100%;"><tr>
					<td>
					'.getTicketType($obj["type"][0]).'
					</td>
					<td>
						<div class="circleHolder">'.($obj["assigned"]?get_abrev($obj["assigned"]):"N/A").'</div>					
					</td>
					<td>
						'.$estimateTime.'
					</td>
					<td>
						'.get_priority($obj["priority"]).'
					</td>
					<td align="right">
						'.$build_modal_output.'
					</td>
					</tr>
				</table>
					
				</div>'.(intval($obj["status"]) > 1 ? $progress_div : '').'
				
			  </div>
			</div>';
	
}

//end funciones


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

.dayExceeded{
	text-align:center;
	padding:4px 2px; 
	border-radius: 12px;
	background: #FF3333;
	margin-right:5px;
	min-width: 22px;
}

.panelContainer{
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

.div_error{
display:inline-block;
padding:10px 20px;	
background:#f57a49;
border:2px solid #e34720;
border-radius:8px;
margin-bottom:15px;
color:#ffffff;
}
.div_ok{
display:inline-block;
padding:10px 20px;	
background:#8df57a;	
border:2px solid #1aed4b;
border-radius:8px;
margin-bottom:15px;
color:#ffffff;
}

.espaciado{
	margin: 2px 1px;
}

.nota{
		padding:4px 6px;
		  -moz-border-radius: 6px;
		  -webkit-border-radius: 6px;
		  border-radius: 6px;
		  width: 95%;
		  text-align: left;
		  background:#f0f0f0;
		  margin-top: 5px;
		}
		
	.archivo{
		display:inline-block;
		padding:4px 6px;
		  -moz-border-radius: 6px;
		  -webkit-border-radius: 6px;
		  border-radius: 6px;
		  background:#d9d9d9;
		  margin: 1px 3px;
		  cursor: pointer;
	}
	.hidden{
		display: none;
		visibility: hidden;
	}
	
	#toast-container {
		
		z-index: 1055;

	}

#toast-wrapper {
    position: absolute;
    bottom: 0;
    right: 40%;
}

#toast-container > #toast-wrapper > .toast {
    min-width: 150px
}

#toast-container > #toast-wrapper > .toast >.toast-header strong {
    padding-right: 20px
}

</style>
<script>

(function(b){b.toast=function(a,h,g,l,k){b("#toast-container").length||(b("body").prepend('<div id="toast-container" aria-live="polite" aria-atomic="true"></div>'),b("#toast-container").append('<div id="toast-wrapper"></div>'));var c="",d="",e="text-muted",f="",m="object"===typeof a?a.title||"":a||"Notice!";h="object"===typeof a?a.subtitle||"":h||"";g="object"===typeof a?a.content||"":g||"";k="object"===typeof a?a.delay||3E3:k||3E3;switch("object"===typeof a?a.type||"":l||"info"){case "info":c="bg-info";
f=e=d="text-white";break;case "success":c="bg-success";f=e=d="text-white";break;case "warning":case "warn":c="bg-warning";f=e=d="text-white";break;case "error":case "danger":c="bg-danger",f=e=d="text-white"}a='<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="'+k+'">'+('<div class="toast-header '+c+" "+d+'">')+('<strong class="mr-auto">'+m+"</strong>");a+='<small class="'+e+'">'+h+"</small>";a+='<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">';
a+='<span aria-hidden="true" class="'+f+'">&times;</span>';a+="</button>";a+="</div>";""!==g&&(a+='<div class="toast-body">',a+=g,a+="</div>");a+="</div>";b("#toast-wrapper").append(a);b("#toast-wrapper .toast:last").toast("show")}})(jQuery);


function elimina_depto(id){
	
	if(id)
	if(window.confirm("Eliminar Depto. '"+id+"' ?"))
	window.open("tickets_adm7.php?action=eliminaDepto&id="+id,"_self");

}

function change_status(id,status, allowed = true){
	
	//if(allowed)
	window.open("tickets_adm7.php?action=changeStatus&status="+status+"&id="+id,"_self");
	//else
	//alert("Faltan datos");
}

function filterDepto(value){
	// console.log(value);
	window.open("tickets_adm7.php?action=filterDepto&value="+value,"_self");
	
}

function filterPers(value){
	// console.log(value);
	window.open("tickets_adm7.php?action=filterPers&value="+value,"_self");
	
}

function replace_file(id){
	
	document.getElementById(id).innerHTML = "<input type='file' name='upfile[]' id='upfile' size='40' multiple>";
	
}

function validate_new_ticket(){
	
	let new_depto = document.getElementById('new_depto').value;
	let new_priority = document.getElementById('new_priority').value;
	
	if(new_depto && new_priority){
		document.getElementById('modalForm').submit();
	}else{
		alert("Favor de completar campos");
	}
}

</script>

	<?php
	if($_GET["message"]){ ?>
	
	<script>jQuery.toast({
	  title: '<?=$_GET["message"]?>',
	  type: 'info',
	  delay: 5000
	});
</script>
	<?php
	}
	?>

<div class="newButton">
		
		<?=build_modal("new", "Nuevo Ticket" , "Crear Nuevo Ticket", get_modal_contents() ,"validate_new_ticket();")?>
	<?php if($esEncargado){ ?>
		<?=build_modal("newDepto", "Nuevo Depto." , "Crear Nuevo Departamento", get_modal_contents_deptos() ,"document.getElementById('deptoForm').submit();")?>
		<a href="tickets_adm7_historial.php" ><button type="button" class="btn btn-primary btn-sm">Historial</button></a>
		

		
	<?php } ?>
	 Depto.: <select name='filtDepto' onChange="filterDepto(this.value)"><?=deptos_select($deptos,$value)?></select>
	 Personal: <select name='filtPers' onChange="filterPers(this.value)"><?=pers_select($devs,$value)?></select>
		 
</div>

<div class="panelContainer hidden" id="panelContainer">
		<div class="panelColumn">
			<center>
				<h4><?=$statuses[0]?></h4>
				<?php
				if(sizeof($tickets[0]))
				foreach($tickets[0] as $ticket)echo makeCard_boot4($ticket);
				?>
			</center>
		</div>
		<div class="panelColumn">
			<h4><?=$statuses[1]?></h4>
			<center>
				<?php
				if(sizeof($tickets[1]))
				foreach($tickets[1] as $ticket)echo makeCard_boot4($ticket);
				?>
			</center>
		</div>
		<div class="panelColumn">
			<h4><?=$statuses[2]?></h4>
			<center>
				<?php
				if(sizeof($tickets[2]))
				foreach($tickets[2] as $ticket)echo makeCard_boot4($ticket);
				?>
			</center>
		</div>
		<div class="panelColumn">
			<h4><?=$statuses[3]?></h4>
			<center>
				<?php
				if(sizeof($tickets[3]))
				foreach($tickets[3] as $ticket)echo makeCard_boot4($ticket);
				?>
			</center>
		</div>
		<div class="panelColumn">
			<h4><?=$statuses[4]?></h4>
			<center>
				<?php
				if(sizeof($tickets[4]))
				foreach($tickets[4] as $ticket)echo makeCard_boot4($ticket);
				?>
			</center>
		</div>
</div>
<script>
	document.getElementById("panelContainer").className = "panelContainer";
	
	let openModal = <?=($openModal?intval($openModal):"null")?>;
	
	if(openModal){
		jQuery("#details"+openModal).modal('show');
		
	}
	
</script>
<?php include "demo_includes/demo_footer.php"; ?>