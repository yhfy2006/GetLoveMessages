<?php
	$fr = fopen('sitemap.xml', 'r');
	$fw = fopen('refinedData.xml', 'w');
	while(!feof($fr))
	{
		$line = fgets($fr);
	   if (strpos($line,'http') !== false) {
		$line =trim(explode("20%",$line)[0]);
	    	fwrite($fw,$line."\n");
		}

	}
	fclose($fr);
	fclose($fw);
	
	$fr = fopen('refinedData.xml', 'r');
	while(!feof($fr))
	{
		echo $line = fgets($fr);
	}
	
	
?>