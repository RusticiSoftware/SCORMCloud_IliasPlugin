<?php
/**
* Sets up the javascript-driven registration details report
*
* @author John Hayden <john.hayden@scorm.com>
*/

	include_once("SCORM_CLOUD_API/ScormEngineService.php");

	$regId = $_GET['regId'];
	$regId = "200-6";

	$ScormService = new ScormEngineService("http://dev.cloud.scorm.com/EngineWebServices", "john", "32wE8eRYmMKy5Rcl171ZrR3lSIj2a4QyZXbwWZE7");
	$sr = $ScormService->CreateNewRequest();

	$parameterMap = Array('regid' => $regId);
	$parameterMap['resultsformat'] = "full";
	$parameterMap['format'] = "json";
	$parameterMap['jsoncallback'] = "getRegistrationResultCallback";
	$sr->setMethodParams($parameterMap);

	$dataUrl = $sr->ConstructUrl("rustici.registration.getRegistrationResult");
	
?>

<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="regreport.js"></script>

<style type="text/css">
    .activityTitle { color: blue; font-size: 110% }
    .dataValue {font-weight: bold }
    #report li {list-style: none; padding: 1px }
    #report ul { margin-top: 0; margin-bottom: 0px; font-size: 10pt; }
</style>

<div id="report" dataUrl="<? print($dataUrl); ?>"/>
