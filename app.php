<?php 
require_once dirname(__FILE__).'/application.php';
header('Content-Type: text/html; charset=UTF-8;');

if($_GET['debug']){
	DB::Debug(true);
}