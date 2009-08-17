<?php 

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/SCORM_CLOUD_API/ScormEngineService.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/plugin.php');

global $ScormCloudService, $scormcloud_url, $scormcloud_app_id, $scormcloud_secret_key;

if (empty($scormcloud_url) || empty($scormcloud_app_id) || empty($scormcloud_secret_key)) {
	$msg = "<h3 style='color: red'>SCORM Cloud Learning Module Not Configured</h3>".
	"<p>To use the SCORM Cloud Learning Module Plug-in you must first configure the url, ".
	"application ID and secret key in the plugin.php configuration file ([ilias root]/Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/plugin.php).".
	"<p>A free, basic-level application id can be requested from <a href='http://www.scorm.com'>www.scorm.com</a> for development/demo purposes.";
	
	print "<h3>".$msg."</h3>";
	
	throw new Exception();
}

$ScormCloudService = new ScormEngineService($scormcloud_url, $scormcloud_app_id, $scormcloud_secret_key);
?>
