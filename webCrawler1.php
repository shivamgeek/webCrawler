<?php
//error_reporting(E_ALL); ini_set('display_errors', '1');
include "webCrawler.php";

$allLinksFileHandle=createLocalFile($allLinksFile,"a+");
$finalLinksFileHandle=createLocalFile($finalLinksFile,"a+");

//**************************FOR INITIALIZATION OF SOURCE FILE WITH SERVER***********************

writeSourceToLocal(getSourceCode($server,$server));
extractAnchorAndWrite($allLinksFileHandle,$finalLinksFileHandle,$sourceFile);

//************************** SEPERATION OF 1st DEPTH LINKS FROM OTHERS***************************
//$allLinksDelemeter=fopen($allLinksFile,"a");
//fwrite($allLinksDelemeter,"##########\n");
//fclose($allLinksDelemeter);
writeLinksToLocal($allLinksFile,"##########\n");

//***************************** FETCH LINKS FOR SECOND LEVEL************************************

$allLinksFileHandleR=createLocalFile($allLinksFile,"r");

if($allLinksFileHandleR){
	while(!feof($allLinksFileHandleR)){
		$linkAndTitle=fgets($allLinksFileHandleR);
		if($linkAndTitle=="##########\n"){
			break;
		}
		$newLink=substr($linkAndTitle,0,strrpos($linkAndTitle,">^<"));
		if($newLink!=""){
		$title=substr($linkAndTitle,strrpos($linkAndTitle,"<")+1,strlen($linkAndTitle)-strlen($newLink)-3);
		//echo 'Link is-->  <a href="'.$newLink.'">'.$title.'</a><br>';
		//flush();
		//ob_flush();
		//echo "Title is ".$title."<br>";
		echo "Searching on ".$newLink."<br>";
		writeSourceToLocal(getSourceCode($newLink,$title));
		$allLinksFileHandleA=createLocalFile($allLinksFile,"a");
		extractAnchorAndWrite($allLinksFileHandleA,$finalLinksFileHandle,$sourceFile);
		fclose($allLinksFileHandleA);
		}
}

}else{
	echo "failed";
}

fclose($allLinksFileHandleR);

//**************FILTER ALL LINKS FILE AND ADD LINKS TO ALLLINKSDATE FILE***************

$allLinksFileHandleR=createLocalFile($allLinksFile,"r");

if($allLinksFileHandleR){
	$flag=true;
	while(!feof($allLinksFileHandleR)){
		
		$linkAndTitle=fgets($allLinksFileHandleR);
		if(flag){
			if($linkAndTitle=="##########\n"){
				$flag=false;
			}
			continue;
		}
		$newLink=substr($linkAndTitle,0,strrpos($linkAndTitle,">^<"));
		$title=substr($linkAndTitle,strrpos($linkAndTitle,"<")+1,strlen($linkAndTitle)-strlen($newLink)-3);
		echo "Checking link- ".$newLink." for Date<br>";
		getSourceCode($newLink,$title);

	}

}else{
	echo "failed";
}

fclose($allLinksFileHandleR);





//**************FILTER FINAL LINKS FILE AND ADD LINKS TO ALLLINKSDATE FILE***************

$allLinksFileHandleR=createLocalFile($finalLinksFile,"r");

if($allLinksFileHandleR){
	while(!feof($allLinksFileHandleR)){
		$linkAndTitle=fgets($allLinksFileHandleR);
		$newLink=substr($linkAndTitle,0,strrpos($linkAndTitle,">^<"));
		$title=substr($linkAndTitle,strrpos($linkAndTitle,"<")+1,strlen($linkAndTitle)-strlen($newLink)-3);
		echo "Checking link- ".$newLink." for Date<br>";
	getSourceCode($newLink,$title);
	
	}

}else{
	echo "failed";
}

fclose($allLinksFileHandleR);




















?>