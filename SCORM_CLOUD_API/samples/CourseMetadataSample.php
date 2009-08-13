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

$courseService = $ScormService->getCourseService();

$metadata = $courseService->GetMetadata($courseid, null, null, null);

echo $metadata;

?>