<?php
error_reporting(E_ALL); ini_set('display_errors', '1');

//$han=fopen("testfile.txt","a");
$data=file_get_contents("testfile.txt");
$data="this has to be appended again\n".$data;
file_put_contents("testfile.txt",$data);
/* if($han){
 while(!feof($han)){
 	$source=fgets($han);
	echo "this is source ".$source;
	
 }
}else{
	echo "file cannot be opened<br>";
} 
 */





?>