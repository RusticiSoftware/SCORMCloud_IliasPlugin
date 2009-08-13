<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>Registration List Sample</title>
	
</head>

<body>
<a href="CourseListSample.php">Course List</a>
<br/><br/>
<?php
require_once('../ScormEngineService.php');
require_once('../ServiceRequest.php');
require_once('../CourseData.php');
require_once('config.php');
global $CFG;

$ServiceUrl = $CFG->scormcloudurl;
$AppId = $CFG->scormcloudappid;
$SecretKey = $CFG->scormcloudsecretkey;

$courseid = $_GET['courseid'];

$ScormService = new ScormEngineService($ServiceUrl,$AppId,$SecretKey);

$regService = $ScormService->getRegistrationService();

if(isset($courseid)){
	$allResults = $regService->GetRegistrationList(null,$courseid);
}else{
	$allResults = $regService->GetRegistrationList(null,null);
}
//include jquery for our use
echo '<script type="text/javascript" ';
echo "src=\"scripts/jquery-1.3.2.min.js\"></script>\n";
//include jquery.thickbox for our use
echo '<script type="text/javascript" ';
echo "src=\"scripts/thickbox-compressed.js\"></script>\n";
echo '<link rel="stylesheet" ';
echo "href=\"scripts/thickbox.css\" type=\"text/css\" media=\"screen\" />\n";

echo '<a href="CreateRegistrationSample.php?courseid='.$courseid.'" target="_blank">new registration</a>';
echo '<table border="1" cellpadding="5">';
echo '<tr><td></td><td>Registration Id</td><td>Course Id</td><td>completion</td><td>success</td><td>total time</td><td>score</td><td></td></tr>';
foreach($allResults as $result)
{
	echo '<tr><td>';
	echo '<a class="thickbox" href="launch.php?regid='.$result->getRegistrationId().'&TB_iframe=true&height=500&width=700" target="_blank" >launch</a>';
	echo '</td><td>';
	echo $result->getRegistrationId();
	echo '</td><td>';
	echo $result->getCourseId();
	echo '</td><td>';
	$regResults = $regService->GetRegistrationResult($result->getRegistrationId(),0,'xml');
	//echo $regResults;
	$xmlResults = simplexml_load_string($regResults);
	echo $xmlResults->registrationreport->complete;
	echo '</td><td>';
	echo $xmlResults->registrationreport->success;
	echo '</td><td>';
	echo $xmlResults->registrationreport->totaltime;
	echo '</td><td>';
	echo $xmlResults->registrationreport->score;
	echo '</td><td>';
	echo '<a href="LaunchHistorySample.php?regid='.$result->getRegistrationId().'">Launch History</a>';
	echo '</td></tr>';
}
echo '</table>';
?>
</body>
</html>