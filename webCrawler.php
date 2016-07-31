<?php
//error_reporting(E_ALL); ini_set('display_errors', '1');
//error_reporting(E_NOTICE);
$allLinksFile="allLinks.txt";
$linksWithDateFile="linksDate.txt";
$sourceFile="source.txt";

$date = $_GET["date"];
$year = date('Y', strtotime($date));
$month =substr(date('F', strtotime($date)),0,3);
$day = date('d', strtotime($date));
if(substr($day,0,1)=="0"){
	$day=substr($day,1,1);
}

//$day="30";
//$month="July";
//$year="2016";
//$server="http://www.niecdelhi.ac.in";  //not to end with a "/".
//$server="http://www.timesofindia.indiatimes.com";
//$server="http://www.theverge.com";
//$server="https://www.youtube.com";
//$server="toCrawl.html";
//$server="http://www.hindustantimes.com";
//$server="https://en.wikipedia.org";
//$server="http://stackoverflow.com";
$server=$_GET["link"];
if(substr($server,0,3)=="www"){
	$server="http://".$_GET["link"];
}
if(substr($server,strlen($server)-1)=="/"){
	$server=substr($server,0,strlen($server)-1);
}

$previousLink=$server;
$countAllLinks=0;
$countFinalLinks=0;
$finalLinksFile="finalLinks.txt";
//echo "testing";
$testlink=0;
$sourceNo=0;
 $q=fopen("allLinks.txt","w");
 fwrite($q,"");
 $r=fopen("linksDate.txt","w");
 fwrite($r,"");
$w=fopen("finalLinks.txt","w");
fwrite($w,"");
$e=fopen("source.txt","w");
fwrite($e,"");
fclose($q);fclose($w);fclose($e);fclose($r);



function createLocalFile($filename,$mode){   //$filename is name of local file to be created
	$handle=fopen($filename,$mode);
	//echo "Local file created ".$filename."<br>";
	return $handle ;
}

function getSourceCode($server,$title){   //$server is name of remote whose source code is to be fetched
	$handle =fopen($server,"r");
	$source=file_get_contents($server);
	fclose($handle);
	//echo "<br>Source Code Retrived from ".$server."<br>";
	if(filterLinkWithDate($GLOBALS["day"], $GLOBALS["month"], $GLOBALS["year"], $source)){
	//	$dateLink=fopen("linksDate.txt","a");
		//$dateLink=$dateLink.$server;
		$dateLink=file_get_contents($GLOBALS["linksWithDateFile"]);
		file_put_contents($GLOBALS["linksWithDateFile"],$dateLink.$server.">^<".$title."\n");
		//fwrite($dateLink,$server."\n");
		//fclose($dateLink);
	 }
	$GLOBALS["sourceNo"]++;
	return $GLOBALS["sourceNo"].$source;
}

function writeLinksToLocal($fileName,$source){  
		//$data=file_get_contents($filename);							//$handle is handle of local file to which $source is to be written and $source is the string 
	   file_put_contents($fileName,$source,FILE_APPEND);                                     //to be written in to the file 
	//fwrite($localHandle, $source);
	//echo substr($source,0,100)." ... and more content written to file<br>";	
}


function writeSourceToLocal($source){
	file_put_contents($GLOBALS["sourceFile"], $source);
}

function filterLinkWithDate($day,$month,$year,$source){

	//$date1="(".$day."(.{2})?(.{1-2})?.".$month.".(.{1-2})?".$year.")i";
	$date1="(".$day.".{0,25}".$month.".{0,25}".$year.")i";
	$date2="(".$month.".{0,25}".$day.".{0,25}".$year.")i";
	$date3="(".$year.".{0,25}".$month.".{0,25}".$day.".{0,25})i";

	if(preg_match_all($date1,$source)||preg_match_all($date2,$source)||preg_match_all($date3,$source)){
		return true;
	}
	return false;

}



function filterHyperlink($link){
	$link=trim($link," ");
	$server=$GLOBALS["server"];
	$flag=false;
	$modserver=substr($server,11);
	//str_replace("http://www", $replace, $subject)
	
	if($link==$server){  // $link= $server Cross links
		//echo "Link discarded SERVER = LINK: ".$link."<br>";
		return "";
	}
	else if(strpos($link,$modserver)){  //if link is absolute or not
		$flag=true;
	}
	else if($link[0]=='/'){  //if link is relative or not , if YES, make it absolute
		$link=$server.$link;
		$flag=true;
	}else{  //  link is NOT under the same domain(SPAM)
		//echo "Link discarded SPAM : ".$link."<br>";
		return "";
	}

	$previousLink=$GLOBALS["previousLink"];
	//similar_text($link, $previousLink,$percent);
	if($flag ){//&& $percent<90){   // if link is under the same domain
		$previousLink=$link; 
		return $link;

	}else{
		//echo "Link discarded FLAG NOT TRUE: ".$link."<br>";
		return "";
	}

}

 function extractAnchorAndWrite($handleAll,$handleFinal,$sourceFileName){   // $sourceFilename is NAME of local file where complete website source code is located.
	                                          
	$html=new DOMDocument();
	$html->loadHTMLFile($sourceFileName);
	$anchor=$html->getElementsByTagName('a');
	
	foreach($anchor as $link){
		$GLOBALS["testlink"]++;
		$hyper1=$link->getAttribute('href');
		$title=$link->nodeValue;
	//	echo "Title for anchor tag is ".$link->nodeValue();
		//echo "Original URL  - $hyper1<br>";
		$hyper=filterHyperlink($hyper1);
		$len=strlen($hyper); //>^<
	//	echo "Length is $len - $hyper<br><br>";
		if($len>6){
			
		if($len-6<=strrpos($hyper,".")){// || strpos($hyper,"?")){  //for links with specific extensions
			writeLinksToLocal($GLOBALS["finalLinksFile"],$hyper.">^<".$title."\n"); 
			echo 'Link is-->  <a href="'.$hyper.'">'.$title.'</a><br>';
			
			$GLOBALS["countFinalLinks"]++;
			
		}else{
			writeLinksToLocal($GLOBALS["allLinksFile"],$hyper.">^<".$title."\n");  //for general links, cateogrization
			echo 'Link is-->  <a href="'.$hyper.'">'.$title.'</a><br>';
			$GLOBALS["countAllLinks"]++;
		}
		flush();
		ob_flush();
		
	}
	}
	
//	writeToLocal($GLOBALS["allLinksFileHandle"],"\ntotal links are ".$GLOBALS["countAllLinks"]);
	//writeToLocal($GLOBALS["finalLinksFileHandle"],"\ntotal links are ".$GLOBALS["countFinalLinks"]);
} 
 
//*****************************FINAL CALLING************************************

/*

if($allLinksFileHandleR){
	while(!feof($allLinksFileHandleR)){
		
		$linkAndTitle=fgets($allLinksFileHandleR);
		$newLink=substr($linkAndTitle,0,strrpos($linkAndTitle,">^<"));
		echo "New Link is ".$newLink."<br>";
		echo "Title is ".substr($linkAndTitle,strrpos($linkAndTitle,">^<"));
	writeSourceToLocal(getSourceCode($newLink));
	$allLinksFileHandleA=createLocalFile($allLinksFile,"a");
	extractAnchorAndWrite($allLinksFileHandleA,$finalLinksFileHandle,$sourceFile);
	fclose($allLinksFileHandleA);
	}

}else{
	echo "failed";
}  */
 

















 




?>