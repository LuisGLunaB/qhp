<?php
//Revisar si se está en entorno Local o Remoto
$URL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$URL = explode("/",$URL);
if(in_array("localhost",$URL)){
	$debugging = true;
}else{
	$debugging = false;
}
$debugging = true;

if(!isset($men)){$men = "";}
$sal = "viveriveniversumvivusvici";

//$dbuser = "root";
//$dbpass = ""; 

$dbhost = "mkti.mx";
$dbname = "ventallanta";
$dbuser = "grupollancarsa";
$dbpass = "Grupollancarsa22";

if(!isset($debugging)){$debugging=false;}

if($debugging){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}else{
	error_reporting(0);
	ini_set('display_errors', 0);
}

//echo call_user_func_array("prueba", array(2,7,3));
//echo call_user_func("prueba", 1);

$palabras_neutrales = array(""," ","-",".","Todo","Todos","todo","todos","Toda","Todas","toda","todas",
	"All","All","all","TODO","TODOS","TODA","TODAS","_","*");

function Q_connect(){
	global $dbhost,$dbname,$dbuser,$dbpass,$debugging;
	try {
	  # MS SQL Server and Sybase with PDO_DBLIB
	  //$DBH = new PDO("mssql:host=$host;dbname=$dbname, $user, $pass");
	  //$DBH = new PDO("sybase:host=$host;dbname=$dbname, $user, $pass");
	  # SQLite Database
	  //$DBH = new PDO("sqlite:my/database/path/database.db");
	  //$DBH = null;
	  
	  # MySQL with PDO_MYSQL
	  $con['handler'] = new PDO("mysql:host=$dbhost;dbname=$dbname;", $dbuser, $dbpass,
	  array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));


	  $con['message'] = "";
	  $con['status'] = true;
	  //$con['handler']->exec("set names utf8");
	  
	  if($debugging){
	  	$con['handler']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );//Debugging
	  }else{
		$con['handler']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Final
	  }
	  
	  return $con;
	}
	catch(PDOException $e) {
		$con['handler'] = "";
		$con['message'] = Q_error("* Error al conectar a la base de datos: ". $e->getMessage()."<br />",$debugging);
		$con['status'] = false;
		return $con;
	} 
}

function Q_error($error,$debugging){
	if($debugging){
		return $error;
	}else{
		$split = explode(":",$error);
		return ($split[0]."<br />");
	}
}

function Q_fields($table,$trim=false){
	global $con;
	//echo $table;
	$q = $con['handler']->prepare("DESCRIBE ". $table);
	try{
		$q->execute();
	} catch(Exception $e){
		echo "La tabla no existe.";
	}
	$table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
	if($trim){array_shift($table_fields);}
	return $table_fields;
}

function Q_field1($table){
	$x = Q_fields($table);
	return $x[0];
}

function Q_insert($table, $data, $fields = NULL){
	global $con,$debugging;
	//Ajustar el formato de los campos
	if(is_null($fields)){$fields = Q_fields($table,true);}
	if(!is_array($fields)){$fields = explode(",",$fields);}
	
	//Borrar fields=fActivo
	$fields = array_values(array_diff($fields,array("activo")));
	
	//Convertir texto a Array asociativo
	if(!is_array($data)){
		$dat = array();
		$data = explode(",",$data);
		$c = 0;
		foreach($fields as $f){
			$dat[$f] = $data[$c];
			$c++;
		}$data = $dat; unset($dat);
	}
	
	//Agregar : al inicio de cada variable
	$fields_colon = array();
	foreach($fields as $f){array_push($fields_colon,":".$f);}
	
	//Convertir Arrays a texto
	$fields_e = $fields;
	$fields = implode(", ",$fields);
	$fields_colon = implode(", ",$fields_colon);

	//Ejecutar comando INSERT
	if(!isset($data[0])){$datos[0] = &$data;}else{$datos = &$data;}
	$q['message'] = "";
	$q['last'] = 0;
	$q['status'] = true;
	$q['query'] = "INSERT INTO ".$table." (".$fields.") VALUE (".$fields_colon.")";
	$q['handle'] = $con['handler']->prepare($q['query']);
	$c = 0;
	foreach($datos as $d){
		try{
			foreach($fields_e as $f){
				$q['handle']->bindValue(':'.$f, utf8_encode($d[$f]) );
			}
			//$q['handle']->bindValue(':tabla', $table );
			@$q['handle']->execute();
			$q['last'] = $con['handler']->lastInsertId();
		}catch(PDOException $e){
			$q['status'] = false;
			$q['last'] = 0;
			$q['message'].= Q_error("* Error al insertar[".$c."]: ".$e->getMessage()."<br />",$debugging);
		}$c++;
	}
	return $q;	
}

function Q_update($table,$data,$id = NULL,$fields = NULL){
	global $con,$debugging;
	//Ajustar el formato de los campos
	if(is_null($fields)){$fields = Q_fields($table,true);}
	if(!is_array($fields)){$fields = explode(",",$fields);}
	$fields = array_values(array_diff($fields,array("activo")));
	
	//Conseguir nombre del campo ID
	$id_field = Q_field1($table);

	//Convertir texto a Array asociativo
	if(!is_array($data)){
		$dat = array();
		$data = explode(",",$data);
		$c = 0;
		foreach($fields as $f){
			$dat[$f] = $data[$c];
			$c++;
		}$data = $dat;
	}

	//Conseguir id
	if(is_null($id)){
		$id = (int) $data[$id_field];
	}else{
		$id = (int) $id;
	}
	
	//Comparar
	$sel = Q_select_id($table,$id,$fields,false);
	if($sel["status"] && isset($sel["data"][0]) ){
		$com = $sel['data'][0];
		$final = array();
		foreach($fields as $f){
			if($data[$f]!=$com[$f]){array_push($final,$f);}
		}$fields = $final; unset($final);
	}
	
	//Armar set
	$set = array();
	foreach($fields as $f){array_push($set,"".$f." = :".$f."");}
	$set = implode(", ",$set);
	
	//Ejecutar comando UPDATE
	$q['message'] = "";
	$q['last'] = $id;
	$q['status'] = true;
	$q['query'] = "UPDATE ".$table." SET ".$set." WHERE ".$id_field." = :id ";
	$q['handle'] = NULL;
	
	if(sizeof($fields)!=0){	
		$q['handle'] = $con['handler']->prepare($q['query']);
		try{
			$q['handle']->bindParam(':id', $id );
			foreach($fields as $f){$var = utf8_encode($data[$f]); $q['handle']->bindValue(':'.$f, $var );}
			//$q['handle']->bindValue(':table', $table );
			$q['handle']->execute();
		}catch(PDOException $e){
			$q['status'] = false;
			$q['last'] = 0;
			$q['message'].= Q_error("* Error al actulizar: ".$e->getMessage()."<br />",$debugging);
		}
	}
	
	return $q;	
}

function Q_select_id($table,$id,$fields = NULL,$activo = true){
	$id_field = Q_fields($table); $id_field = $id_field[0];
	$where[$id_field] = $id;
	return Q_select($table,$fields,$where,"",NULL,$activo);
}

function Q_select($table,$fields = NULL,$where = NULL,$extra = "",$funcion = NULL,$activo = true){
	global $con,$debugging;
	//Ajustar el formato de los campos
	$all = false;
	if(is_null($fields)){$fields = Q_fields($table);$all = true;}
	if(!is_array($fields)){$fields = explode(",",$fields);}
	
	//Armar WHERE
	$where_string = "";
	if(!is_null($where)){
		$where = Q_where($where, Q_fields($table) );

		$where_string = array();
		foreach($where as $key => $value){
			if(!is_array($value)){
				array_push($where_string,"".$key." = :".$key);
			}else{
				array_push($where_string,"".$key." ".$value[1]." :".$key);
			}
		}
		
		if(sizeof($where_string)==0){
			$where_string = "";
			if($activo){$where_string .= "activo = 1 ";}
		}else{
			$where_string = implode(" AND ",$where_string)." ";
			if($activo){$where_string .= "AND activo = 1 ";}
		}

	}else{
		if($activo){$where_string .= "activo = 1 ";}
	}if($where_string != ""){$where_string = " WHERE ".$where_string;}
	
	//Preprar SELECT
	if($all){$selection = "*";}else{$selection = implode(", ",$fields);}
	$q['status'] = 1;
	$q['message'] = "";
	$q['size'] = 0;
	$q['time'] = 0;
	$q['memory'] = 0;
	$q['query'] = 'SELECT '.$selection.' FROM '.$table.$where_string." ".$extra;
	try{
		$startT = microtime(true);
		$startM = memory_get_usage();

		$q['handle'] = $con['handler']->prepare($q['query']);
		if(!is_null($where)){
			foreach($where as $key => $value){
				if(!is_array($value)){
					$q['handle']->bindValue(':'.$key, $value);
				}else{
					$q['handle']->bindValue(':'.$key, $value[0]);
				}
			}
		}
		//$q['handle']->bindValue(':table', $table);
		$q['handle']->setFetchMode(PDO::FETCH_ASSOC);
		$q['handle']->execute();
		
		$c = 0;
		$q['data'] = array();
		while($row = $q['handle']->fetch()) {
			if(is_null($funcion)){
				foreach($fields as $f){
					$q['data'][$c][$f] = utf8_decode($row[$f]);
				}$c++;
			}else{
				$q['data'] = -1;
				echo call_user_func($funcion,mutf8_decode($row));
			}
		}
		
		$q['size'] = sizeof($q['data']);
		$q['time'] = microtime(true) - $startT;
		$q['memory'] = (memory_get_usage() - $startM)/10000000;
		
	}catch (Exception $e){
		$q['status'] = 0;
		$q['message'].= Q_error("* Error al seleccionar: ".$e->getMessage()."<br />",$debugging);
	}
	return $q;
}

function mutf8_decode($A){
	foreach($A as $key => $value){
		$A[$key] = utf8_decode($value);
	}
	return $A;
}

function mutf8_encode($A){
	foreach($A as $key => $value){
		$A[$key] = utf8_encode($value);
	}
	return $A;
}

function Q_where($where,$fields){
	//$where = $POST;
	global $palabras_neutrales;
	$x = array();
	foreach ($where as $key => $value){
		if(in_array($key,$fields)){
			if(!in_array($value,$palabras_neutrales) && !is_null($value)){
			$x[$key] = $value;
			}
		}
	}
	return $x;
}

function Q_where_join($where,$fields){
	//$where = $POST;
	global $palabras_neutrales;
	$x = array();
	foreach ($where as $key => $value){
		foreach($fields as $t => $f){
			if(@in_array($key,$f)){
				if(!in_array($value,$palabras_neutrales) && !is_null($value)){
						$x[$t.".".$key] = $value;			 
				}
			}
		}
	}
	return $x;
}

function Q_plus($table,$id,$field,$plus=true){
	global $con,$debugging;
	$q['status'] = true;
	$q['query'] = "";
	$q['message'] = "";
	$id = (int) $id;
	if($plus){$plus = "+";}else{$plus = "-";}
	
	$fields = Q_fields($table);
	$id_field = $fields[0];
	if(in_array($field,$fields)){
		try{
			$q['query'] = "UPDATE ".$table." SET ".$field." = ".$field." ".$plus." 1 WHERE ".$id_field." = ".$id." ";
			$q['handle'] = $con['handler']->prepare($q['query']);
			//$q['handle']->bindValue(':table', $table);
			$q['handle']->execute();
		}catch(Exception $e){
			$q['message'].= Q_error("* Error al cambiar conteo: ".$e->getMessage()."<br />",$debugging);
			$q['status'] = false;
		}
	}
	return $q;
}

function Q_minus($table,$id,$field,$plus=false){
	return Q_plus($table,$id,$field,$plus);
}

function Q_activate($table,$id,$activo = true){
	global $con,$debugging;
	$q['status'] = true;
	$q['message'] = "";
	$id = (int) $id;
	if($activo){$activo=1;}else{$activo=0;}
	
	$fields = Q_fields($table);$id_field = $fields[0];
	$q['query'] = "UPDATE ".$table." SET activo = ".$activo." WHERE ".$id_field." = ".$id." ";
	
		try{
			$q['handle'] = $con['handler']->prepare($q['query']);
			//$q['handle']->bindValue(':table', $table);
			$q['handle']->execute();
		}catch(Exception $e){
			$q['message'].= 
			Q_error("* Error al actualizar estado: ".$e->getMessage()."<br />",$debugging);
			$q['status'] = false;
		}
	
	return $q;
}

function Q_deactivate($table,$id,$activo = false){
	return Q_activate($table,$id,$activo);
}

function Q_exists($table,$where){
	$fields = array();
	foreach($where as $f => $d){
		array_push($fields,$f);
	}

	$q = Q_select($table,$fields,$where,"",NULL,false);
	
	if($q['status']){
		if(sizeof($q['data'])==0){
			$q = false;
		}else{
			$q = true;
		}
	}else{
		$q = false;
	}
	
	return $q;
}

function Q_print($data){
	if(sizeof($data)!=0){
		$fields = array();
		$keys = array_keys($data[0]);
		foreach($keys as $k){array_push($fields,$k);}
	
		$x = "";
		$x .= '<table width="100%" border="1">';
		$x .= '<tr class=first style="color: white; background-color: black;">';
		foreach($fields as $f){$x .='<td>'.$f.'</td>';}
		$x .= '</tr>';
		
		foreach($data as $d){
			$x .= '<tr>';foreach($fields as $f){$x .='<td>'.Q_clear($d[$f]).'</td>';}$x .= '</tr>';
		}
		
		$x .= '</table>';
		return $x;
	}else{
		return "No hay datos que mostrar.<br />";
	}
}

function Q_print2($data,$separador = "<br />"){
	$fields = array();
	$x = "";
	$keys = array_keys($data[0]);
	foreach($keys as $k){array_push($fields,$k);}
	
	foreach($data as $d){
		foreach($fields as $f){
			if($d[$f]!=""){
			$x .='<strong>'.$f.': </strong>'.Q_clear($d[$f]).'<br />';
			}
		}$x .= $separador;
	}
	
	return $x;
}

function isAssoc(array $array) {
	return count(array_filter(array_keys($array), 'is_string')) > 0;
}

function pDot($x){
	$x = explode(".",$x);
	return $x[1];
}

function Q_join($tables,$fields = NULL,$AS = true,$where = NULL,$links = "",
$extra = "",$funcion = NULL,$activo = true){
	
global $con,$debugging;

//Llenar fields si no se dió
if(is_null($fields)){
	foreach($tables as $t){
		$fields[$t] = Q_fields($t);
	}
}
 
//Convertir campos en Array asociativo
if(!isAssoc($fields)){
	$c = 0;
	$nfields = array();
	foreach($tables as $t){$nfields[$t] = $fields[$c];$c++;}
	$fields = &$nfields;
}

$Query = "SELECT ";
$selection = array();
//Llenar selección de variables
$variables = array();
if(sizeof($fields>1)){
	foreach($tables as $t){
		foreach($fields[$t] as $f){
			if($AS){
				array_push($variables,$t.".".$f." AS ".$f);
				array_push($selection,$f);
			}else{
				array_push($variables,$t.".".$f." AS ".$t."_".$f);
				array_push($selection,$t."_".$f);
			}
		}
	}
}$Query .= implode(", ",$variables)." FROM ".implode(" JOIN ",$tables)." ON ";

//Encontrar vinculaciones entre tablas
if($links==""){
	$vincs = array();
	foreach($tables as $T){
		$id = Q_field1($T);
		//ver si existe el id en la tabla
		foreach($tables as $t){
			if($T!=$t){
				if(in_array($id,Q_fields($t))){
					array_push($vincs,$t.".".$id." = ".$T.".".$id);
				}
			}
		}
	}$links = implode(" AND ",$vincs);
}$Query .= $links;


//Armar WHERE
$where_string = "";
if(!is_null($where)){
	$w = array();
	$where = Q_where_join($where,$fields);
	$v = 1;
	foreach ($where as $key => $value){
		
		if(!is_array($value)){
			array_push($w,$key." = :var".$v);$v++;
		}else{
			array_push($w,$key." ".$value[1]." :var".$v);$v++;
		}
		
	}
	if(sizeof($w)!=0){$where_string = " WHERE ".implode(" AND ", $w)." ";}
}

//Filtrar filas activas (primer tabla)
$activo_string = "";
if($activo){
	if($where_string==""){
		$activo_string = " WHERE ".$tables[0].".activo = 1 ";
	}else{
		$activo_string = "AND ".$tables[0].".activo = 1 ";
	}
}

$Query .= $where_string.$activo_string.$extra;

$q['status'] = 1;
$q['message'] = "";
$q['size'] = 0;
$q['time'] = 0;
$q['memory'] = 0;
$q['query'] = $Query;
  try{
	  $q['handle'] = $con['handler']->prepare($q['query']);
	  
	  $v = 1;
	  foreach($where as $key => $value){
		  if(!is_array($value)){
		 	 $q['handle']->bindValue(':var'.$v, $value);$v++;
		  }else{
			  $q['handle']->bindValue(':var'.$v, $value[0]);$v++;
		  }
	  }
	  
	  $q['handle']->setFetchMode(PDO::FETCH_ASSOC);
	  $q['handle']->execute();
	  
	  $c = 0;
	  $q['data'] = array();
	  while($row = $q['handle']->fetch()) {
		  if(is_null($funcion)){
			  foreach($selection as $f){  
				  $q['data'][$c][$f] = utf8_decode($row[$f]);
			  }$c++;
		  }else{
				$q['data'] = -1;
				echo call_user_func($funcion,mutf8_decode($row));
		  }
	  }
  }catch (Exception $e){
	  $q['status'] = 0;
	  $q['message'].= Q_error("* Error al seleccionar: ".$e->getMessage()."<br />",$debugging);
  }
		
return $q;
}

function Q_clear($x){
	if(!is_numeric($x)){
		$x = htmlspecialchars($x, ENT_QUOTES, 'ISO-8859-1');
	}
	return $x;
}

function Q_mclear($A){
	foreach($A as $key => $value){
		$A[$key] = Q_clear($value);
	}
	return $A;
}
function Q_1($data){
	//Tomar el primer valor de los datos
	return $data['data'][0];
}

function Q_delete($table,$where){
	global $con,$debugging;
	$q['status'] = true;
	$q['query'] = "";
	$q['message'] = "";

	try{
		$q['query'] = "DELETE FROM ".$table." WHERE";
		$execute = false;
		$v = 1;
		foreach($where as $f => $d){
			if($v > 1){$q['query'] .= "AND";}
			$execute = true;$q['query'] .= " ".$f." = :var".$v." "; $v++;
		}
		$q['handle'] = $con['handler']->prepare($q['query']);
		
		$v = 1;
		foreach($where as $f => $d){
			$q['handle']->bindValue(':var'.$v, $d);$v++;
		}
		
		if($execute){
			$q['handle']->execute();
		}else{
			$q['status'] = false;
			$q['message'].= Q_error("* Error al seleccionar registro. ",$debugging);
		}
	}catch(Exception $e){
		$q['message'].= Q_error("* Error al eliminar registro: ".$e->getMessage()."<br />",$debugging);
		$q['status'] = false;
	}

	return (bool) $q['status'];
}

function Q_update2($table,$data,$fields,$where){
	global $con,$debugging;
	$q['status'] = true;
	$q['query'] = "";
	$q['message'] = "";

	try{
		$q['query'] = "UPDATE ".$table;
		//column1=value1,column2=value2
		$execute = false;
		$q['query'] .= " SET";
		//Armar SET
		$s = 1;
		foreach($data as $f => $d){
			if( in_array($f,$fields) ){
				if($s > 1){$q['query'] .= ",";}
				$execute = true;$q['query'] .= " ".$f." = :set".$s." "; $s++;
			}
		}
		
		//Armar WHERE
		$q['query'] .= " WHERE";
		
		$v = 1;
		foreach($where as $f => $d){
			if($v > 1){$q['query'] .= "AND";}
			$execute = true;$q['query'] .= " ".$f." = :var".$v." "; $v++;
		}
		
		//Ejecturas Query
		$q['handle'] = $con['handler']->prepare($q['query']);
		//Pasar Where
		$v = 1;
		foreach($where as $f => $d){
			$q['handle']->bindValue(':var'.$v, $d);$v++;
		}
		//Pasar SET
		$s = 1;
		foreach($data as $f => $d){
			if( in_array($f,$fields) ){
			$q['handle']->bindValue(':set'.$s, $d);$s++;
			}
		}
		
		if($execute){
			$q['handle']->execute();
		}else{
			$q['status'] = false;
			$q['message'].= Q_error("* Error al actualizar registro. ",$debugging);
		}
	}catch(Exception $e){
		$q['message'].= Q_error("* Error al actualizar registro: ".$e->getMessage()."<br />",$debugging);
		$q['status'] = false;
	}

	return (bool) $q['status'];
}

function utf8_decoder(&$var){
	foreach($var as $f => $d){
		$var[$f] = utf8_decode($d);
	}
	return $var;
}
function print_rr($x,$tabs = 0,
	$txt= ' ----- '){//"\t"
	
	if(is_array($x)){
		if(isAssoc($x)){
			foreach($x as $k => $v){
				echo str_repeat($txt,$tabs);
				if(is_array($v)){
					echo "<strong>$k (array)</strong><br />";
					print_rr($v,$tabs+1);
				}else{
					echo "<strong>$k: </strong>$v <br />";
				}
				
			}
		}else{
			$k = 0;
			foreach($x as $v){
				echo str_repeat($txt,$tabs);
				if(is_array($v)){
					echo "<strong>$k (array)</strong><br />";
					print_rr($v,$tabs+1);
				}else{
					echo "<strong>$k: </strong>$v <br />";
				}$k++;
			}
		}
	}
	echo "<br />";
}

function invisible(){
	return 'style="display:none; visibility: hidden;"';
}

function Q_options($elementos,$actual = ""){
	$html = "";
	foreach($elementos as $campo => $valor){
		$selected = "";
		if($campo == $actual){$selected = 'selected="selected"';}
		$html .= '<option value="'.$campo.'" '.$selected.'>'.$valor.'</option>';
	}
	return $html;
}

function Q_free($query, $data = NULL){
	global $con,$debugging;
	$q['message'] = "";
	$q['status'] = true;
	$q['query'] = $query;
	$q['data'] = array();
	$q['handle'] = $con['handler']->prepare($q['query']);
	
	try{
		if(!is_null($data)){
			foreach($data as $key => $value){
				$q['handle']->bindValue(':'.$key, $value);
				//echo ':'.$key."-". $value;
			}
		}
		if (strpos($q['query'], 'SELECT') !== false) {
			//SELECT
			$q['handle']->setFetchMode(PDO::FETCH_ASSOC);
			@$q['handle']->execute();
			$c = 0;
			while($row = $q['handle']->fetch()) {
				foreach($row as $key => $value){
					$q['data'][$c][$key] = utf8_decode($value);
				}$c++;
			}
		}else{
			//OTRO
			@$q['handle']->execute();
		}

	}catch(PDOException $e){
		$q['status'] = false;
		$q['message'].= Q_error("* Error al ejectuar comando libre: ".$e->getMessage()."<br />",$debugging);
	}
	return $q;
}

function Q_csv($nombre,$data,$delimiter=",",$campos = true){
    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
    $f = fopen('php://temp', 'w');
    /** loop through array  */
	//Conseguir campos
	$fields = array();$keys = array_keys($data[0]);
	foreach($keys as $k){array_push($fields,$k);}
	
	if($campos){fputcsv($f, $fields, $delimiter);}
	
	foreach($data as $x){
		$values = array_values($x);
		fputcsv($f, $values, $delimiter);
    }
    /** rewrind the "file" with the csv lines **/
    fseek($f, 0);
    /** modify header to be downloadable csv file **/
    header('Content-Type: application/csv');
    header('Content-Disposition: attachement; filename="'.$nombre.'";');
    /** Send file to browser for download */
    fpassthru($f);
}

?>