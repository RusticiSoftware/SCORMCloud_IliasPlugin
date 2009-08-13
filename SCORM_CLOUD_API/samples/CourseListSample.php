<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>Course List Sample</title>
	
</head>

<body>
<a href="ImportSample.php">Import New Package</a>
<br/>
<br/>
<?php
require_once('../ScormEngineService.php');
require_once('../ServiceRequest.php');
require_once('../CourseData.php');
require_once('logger.php');
require_once('config.php');
global $CFG;

$ServiceUrl = $CFG->scormcloudurl;
$AppId = $CFG->scormcloudappid;
$SecretKey = $CFG->scormcloudsecretkey;

write_log('Creating ScormEngineService');
$ScormService = new ScormEngineService($ServiceUrl,$AppId,$SecretKey);
write_log('ScormEngineService Created');

write_log('Creating CourseService');
$courseService = $ScormService->getCourseService();
write_log('CourseService Created');

write_log('Getting CourseList');
$allResults = $courseService->GetCourseList();
write_log('CourseList count = '.count($allResults));

echo '<table border="1" cellpadding="5">';
echo '<tr><td>Course Id</td><td>Title</td><td>Versions</td><td>Registrations</td><td>metadata</td></tr>';
foreach($allResults as $course)
{
	echo '<tr><td>';
	echo $course->getCourseId();
	echo '</td><td>';
	echo $course->getTitle();
	echo '</td><td>';
	echo $course->getNumberOfVersions();
	echo '</td><td>';
	echo '<a href="RegistrationListSample.php?courseid='.$course->getCourseId().'">'.$course->getNumberOfRegistrations().'</a>';
	echo '</td><td>';
	echo '<a href="DeletePackageSample.php?id='.$course->getCourseId().'">Delete Package</a>';
	echo '</td><td>';
	echo '<a href="CourseMetadataSample.php?courseid='.$course->getCourseId().'">metadata</a>';
	echo '</td></tr>';
}
echo '</table>';
?>
</body>
</html>