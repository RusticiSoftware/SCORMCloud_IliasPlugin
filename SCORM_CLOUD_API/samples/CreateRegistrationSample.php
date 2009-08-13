<?php
require_once('../ScormEngineService.php');
require_once('../ServiceRequest.php');
require_once('../CourseData.php');
require_once('config.php');
global $CFG;

$ServiceUrl = $CFG->scormcloudurl;
$AppId = $CFG->scormcloudappid;
$SecretKey = $CFG->scormcloudsecretkey;

$ScormService = new ScormEngineService($ServiceUrl,$AppId,$SecretKey);

$regService = $ScormService->getRegistrationService();

$regId = uniqid(rand(), true);
$courseId = $_GET['courseId'] ;
$learnerId = $_GET['learnerId'] ;
$learnerFirstName = $_GET['learnerFirstName'] ;
$learnerLastName = $_GET['learnerLastName'] ;

//echo $regId . '<br>';
//echo $courseId . '<br>';
//echo $learnerId . '<br>';
//echo $learnerFirstName . '<br>';
//echo $learnerLastName . '<br>';

//CreateRegistration($registrationId, $courseId, $learnerId, $learnerFirstName, $learnerLastName)
$regService->CreateRegistration($regId, $courseId, $learnerId, $learnerFirstName, $learnerLastName);

header("Location: RegistrationListSample.php") ;

?>