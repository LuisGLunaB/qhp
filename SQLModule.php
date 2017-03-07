<?php
$debugging = True;

if($debugging){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}else{
	error_reporting(0);
	ini_set('display_errors', 0);
}

function is_assoc(array $array){
    $keys = array_keys($array);
    return array_keys($keys) !== $keys;
}
function str_has($word,$text){
	if (strpos($text, $word) !== false) {
	  return True;
		}else{
		return False;
	}
}
function EqualReferences(&$first, &$second){
    if($first !== $second){
        return false;
    }
    $value_of_first = $first;
    $first = ($first === true) ? false : true; // modify $first
    $is_ref = ($first === $second); // after modifying $first, $second will not be equal to $first, unless $second and $first points to the same variable.
    $first = $value_of_first; // unmodify $first
    return $is_ref;
}

include_once("./SQLConnector.php");
include_once("./SQLBasicTableManager.php");
include_once("./ErrorManager.php");
