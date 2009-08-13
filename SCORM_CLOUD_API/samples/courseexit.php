<?php

require_once("config.php");
require_once("logger.php");
require_once('../ScormEngineService.php');
require_once('../ServiceRequest.php');
require_once('../CourseData.php');

global $CFG;

$regid = $_GET["regid"];

if(isset($regid)){
	
	write_log('courseexit.php called : regid = ' . $regid);

	//Get the results from the cloud
	$ScormService = new ScormEngineService($CFG->scormcloudurl,$CFG->scormcloudappid,$CFG->scormcloudsecretkey);
	$regService = $ScormService->getRegistrationService();
	$resultsxml = $regService->GetRegistrationResult($regid, 0, 'xml');
	$results = simplexml_load_string($resultsxml);
	
	write_log($resultsxml);
	
	//repopulate the $reg we got above to update it here...
	//$reg->completion = $results->registrationreport->complete;
	//$reg->satisfaction = $results->registrationreport->success;
	//$reg->totaltime = $results->registrationreport->totaltime;
	//$reg->score = $results->registrationreport->score;
			
}
/*
foreach ($_POST as $name => $value)
{
	scormcloud_write_log('Post : ' . $name . ' - ' . $value);
}

foreach ($_GET as $name => $value)
{
	scormcloud_write_log('Get : ' . $name . ' - ' . $value);
}
*/
?>
<script>

window.opener.parent.location.href = window.opener.parent.location.href;
window.close();

</script>