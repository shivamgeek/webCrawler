<?php
//error_reporting(E_ALL); ini_set('display_errors', '1');
error_reporting(E_NOTICE);
$allLinksFile="allLinks.txt";
$linksWithDateFile="linksDate.txt";
$sourceFile="source.txt";

$day="19";
$month="July";
$year="2016";
//$server="http://www.niecdelhi.ac.in";  //not to end with a "/".
$server="http://www.timesofindia.indiatimes.com";
//$server="https://www.facebook.com";
//$server="https://www.youtube.com";
//$server="toCrawl.html";
//$server="http://www.hindustantimes.com";
//$server="https://en.wikipedia.org";
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

function getSourceCode($server){   //$server is name of remote whose source code is to be fetched
	$handle =fopen($server,"r");
	$source=file_get_contents($server);
	fclose($handle);
	//echo "<br>Source Code Retrived from ".$server."<br>";
	if(filterLinkWithDate($GLOBALS["day"], $GLOBALS["month"], $GLOBALS["year"], $source)){
		$dateLink=fopen("linksDate.txt","a");
		fwrite($dateLink,$server."\n");
		fclose($dateLink);
	 }
	$GLOBALS["sourceNo"]++;
	return $GLOBALS["sourceNo"].$source;
}

function writeToLocal($localHandle,$source){   //$handle is handle of local file to which $source is to be written and $source is the string 
	                                      //to be written in to the file 
	fwrite($localHandle, $source);
	//echo substr($source,0,100)." ... and more content written to file<br>";	
}


function writeSourceToLocal($source){
	file_put_contents($GLOBALS["sourceFile"], $source);
}

function filterLinkWithDate($day,$month,$year,$source){

	//$date1="(".$day."(.{2})?(.{1-2})?.".$month.".(.{1-2})?".$year.")i";
	$date1="(".$day.".{0,4}".$month.".{0,4}".$year.")i";
	$date2="(".$month.".{0,4}".$day.".{0,4}".$year.")i";
	$date3="(".$year.".{0,4}".$month.".{0,4}".$day.".{0,4})i";

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
			writeToLocal($handleFinal,$hyper.">^<".$title."\n"); 
			$GLOBALS["countFinalLinks"]++;
			
		}else{
			writeToLocal($handleAll,$hyper.">^<".$title."\n");  //for general links, cateogrization
			$GLOBALS["countAllLinks"]++;
		}
		
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