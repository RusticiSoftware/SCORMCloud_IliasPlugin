<?php

require_once('../ScormEngineService.php');
require_once('../ServiceRequest.php');
require_once('../CourseData.php');
require_once('../UploadService.php');
require_once('config.php');
global $CFG;

$ServiceUrl = $CFG->scormcloudurl;
$AppId = $CFG->scormcloudappid;
$SecretKey = $CFG->scormcloudsecretkey;



if ($_FILES["file"]["error"] > 0)
  {
  echo "Error: " . $_FILES["file"]["error"] . "<br />";
  }
else
  {
  echo "Upload: " . $_FILES["file"]["name"] . "<br />";
  echo "Type: " . $_FILES["file"]["type"] . "<br />";
  echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
  echo "Stored in: " . $_FILES["file"]["tmp_name"];

	$ScormService = new ScormEngineService($ServiceUrl,$AppId,$SecretKey);
	$courseService = $ScormService->getCourseService();
	$uploadService = $ScormService->getUploadService();

	$courseId = uniqid();
	echo '$courseId='.$courseId.'<br>';
	// Where the file is going to be placed 
	$target_path = "uploads/";

	/* Add the original filename to our target path.  
	Result is "uploads/filename.extension" */
	$target_path = $target_path . basename( $_FILES['file']['name']); 
	
	$tempFile = $_FILES["file"]["tmp_name"];

	move_uploaded_file($_FILES['file']['tmp_name'], $target_path);
	
	$absoluteFilePathToZip = $target_path;
	
	//now upload the file and save the resulting location
	$location = $uploadService->UploadFile($absoluteFilePathToZip,null);
	//next import the course you just uploaded
	$courseService->ImportUploadedCourse($courseId, $location, null);
  }


?>