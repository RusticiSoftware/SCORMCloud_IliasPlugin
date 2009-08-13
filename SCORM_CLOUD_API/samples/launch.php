<html>
<head>
<?php

require_once("config.php");
require_once('../ScormEngineService.php');
require_once('../ServiceRequest.php');
require_once('../RegistrationService.php');

global $CFG;

//get the courseid and userid

	$regid = $_GET['regid'];

	$ScormService = new ScormEngineService($CFG->scormcloudurl,$CFG->scormcloudappid,$CFG->scormcloudsecretkey);
	$regService = $ScormService->getRegistrationService();
	
	//echo 'Your course is being launched in a new window. This window will automatically close once you exit the course.';
	echo '<script>window.open("'.$regService->GetLaunchUrl($regid, $CFG->wwwroot . 'courseexit.php?id=' . $regid).'");</script>';

	echo '<script>';
	echo 'function RollupRegistration(regid) {';
	echo PHP_EOL;
	echo 'window.frames[0].document.location.href = "rollupregistration.php?regid="+regid;';
	echo PHP_EOL;
	echo '}';
	echo PHP_EOL;
	echo 'setInterval("RollupRegistration(\"'.$regid.'\")",60000);';
	echo PHP_EOL;
	
	echo '</script>';

?>
</head>
<frameset onunload="" rows="*,0">
<frame id="rollupreg" src="blank.html" />
<frame id="blank" src="blank.html" />
</frameset>
</html>