<?php
error_reporting(E_ALL); ini_set('display_errors', '1');

//$han=fopen("testfile.txt","a");
$data=file_get_contents("http://www.google.com");
//$data=$data."NEWLY APPENDED NEW\n";
echo $data;
file_put_contents("linksDate.txt",$data);
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