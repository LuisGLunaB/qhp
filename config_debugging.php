<?php
$debugging = True;

# Report Error depending if we are Debugging or not
if($debugging){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}else{
	error_reporting(0);
	ini_set('display_errors', 0);
}
