<?php
	require_once('simple_html_dom.php');
	
	$Year = 2013;
	$Month = 11;
	$MessageID=2619;
	$fp = fopen('results.json', 'w');
	
	$fr = fopen('refinedData.xml', 'r');
	$Jsondata = array();
	$index=1 ;
	while(!feof($fr))
	{
		$line = fgets($fr);
		echo $line." $index\n";
		start(trim($line),$fp,$Jsondata);
		$index++;
	}
	fwrite($fp, json_encode($Jsondata));

	fclose($fr);
	fclose($fp);
 
	function start($url,&$fp,&$JsonData)
	{
		//echo "NONONO"."\n";
		$StringMonth = $Month<10?"0".$Month:$Month;
		echo "URL=".$url."\n";
		$handle = curl_init($url);
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
		/* Get the HTML or whatever is linked in $url. */
		$response = curl_exec($handle);
		/* Check for 404 (file not found). */
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if($httpCode == 404) {
			curl_close($handle);
			echo "404"."\n";
		    return false;
		}
		curl_close($handle);
		$html = file_get_html($url);
		if($html==null) return false;
		$info = array();
		if(getMessageFromType1($html,$info)==false)
		{
				return false;
		}
		print_r($info);
		if(!($info['message']==null || $info['message']=='')) 
		{
			$JsonData[] = $info;
		}
		return true;
	}
	
	function getMessageFromType1($html,&$info)
	{
			$div = $html->find('.PostContent',0);
			if($div==null)
			{
				return false;
			} 
			$type = 0;
			if(sizeof($div->getElementsByTagName("strong")[0])==1)
				 $type = 2;
			else
				 $type = 1;
			//echo "type = $type";
			$sendFrom = null;
			$sendTo = null;
			$sendToAgeAndLocation = null;
			$sendFromAgeAndLocation = null;
			$Message=null;
			$eles = $div->getElementsByTagName("p");
			$MessageProperty = strip_tags(html_entity_decode($eles[0]->innertext));
			$sendFrom =  trim(get_string_between($MessageProperty,"Love Message From:","Age & Location:"));
			$sendFromAgeAndLocation =trim(get_string_between($MessageProperty,"Age & Location:","Love Message To:"));
			$sendTo  = trim(get_string_between($MessageProperty,"Love Message To:","Age & Location:"));
			$info['sendFrom'] = $sendFrom;
			$info['sendFromAgeAndLocation'] = $sendFromAgeAndLocation;
			$info['sendTo'] = $sendTo;
			$Messagebody = strip_tags(html_entity_decode($eles[1]->innertext));
			$Message = $type==1?$Messagebody:trim(explode(":", $Messagebody)[1]);
			$info['message'] = trim($Message);
			return true;
		}
	
	function getMessageFromType2($html,&$info)
	{
		$div = $html->find('.PostContent',0);
			//echo $div->getElementsByTagName("p")[0];
			$sendFrom = null;
			$sendTo = null;
			$sendToAgeAndLocation = null;
			$sendFromAgeAndLocation = null;
			$Message=null;
			
			foreach($div->getElementsByTagName("p") as $ele) 
			{
				if($MessageBody!=null) continue;
				//echo $ele->innertext."\n";
				if (strpos($ele->innertext,'Love Message From:') !== false)
				{
					 $MessageProperty = strip_tags(html_entity_decode($ele->innertext));
					 $sendFrom =  trim(get_string_between($MessageProperty,"Love Message From:","Age & Location:"));
					 $sendFromAgeAndLocation =trim(get_string_between($MessageProperty,"Age & Location:","Love Message To:"));
					 //$sendToAgeAndLocation  = trim(get_string_between($MessageProperty,"Love Message To:","Age & Location:"));
					 $sendTo  = trim(get_string_between($MessageProperty,"Love Message To:","Age & Location:"));
					 $info['sendFrom'] = $sendFrom;
					 $info['sendFromAgeAndLocation'] = $sendFromAgeAndLocation;
					 $info['sendTo'] = $sendTo;
					 //echo "From :".$sendFrom." "."Age :".$sendFromAgeAndLocation." "."To:".$sendTo."\n";
				}
				   
				if (strpos($ele->innertext, 'Your Message:') !== false||strpos($ele->innertext, 'Love Message:') !== false)
				{
					$MessageBody = strip_tags(html_entity_decode($ele->innertext));
					$Message = trim(explode(":", $MessageBody)[1]);
					//echo "--->".$MessageBody;
					$info['message'] = trim($Message);
					//echo "Body:".trim($Message);
				}
				    
			}
	}
	

	
	
	// get substring between two entered strings
	function get_string_between($string, $start, $end){
	    $string = " ".$string;
	    $ini = strpos($string,$start);
	    if ($ini == 0) return "";
	    $ini += strlen($start);
	    $len = strpos($string,$end,$ini) - $ini;
	    return substr($string,$ini,$len);
	}
	
	
	
?>