<?php

/**
 * Write To Log File
 *
 * @param string $message test to write to log
 * 
 * @return bool success
 */
function write_log($message) {
	
	$fh = fopen('SCORMCloud_samples.log', 'a');
	
	fwrite($fh, '['.date("D dS M,Y h:i a").'] - '.$message."\n");
	
	fclose($fh);

	return true;

}

?>