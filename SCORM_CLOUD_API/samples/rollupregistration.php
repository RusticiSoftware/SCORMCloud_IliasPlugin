<?php



require_once("config.php");
require_once("logger.php");
require_once('../ScormEngineService.php');
require_once('../ServiceRequest.php');
require_once('../CourseData.php');

global $CFG;

$regid = $_GET["regid"];

if($regid != null){
	
	write_log('rollupregistration.php called : regid = ' . $regid);

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

?>