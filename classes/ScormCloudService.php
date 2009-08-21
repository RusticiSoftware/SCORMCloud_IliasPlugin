<?php 

/**
* SCORM Cloud Service Helper object definition
*
* @author John Hayden <john.hayden@scorm.com>
* @version $Id$
*
*/

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/SCORM_CLOUD_API/ScormEngineService.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/plugin.php');

// CONSTANTS
define("SCORE_UNKNOWN", -999);

global $ScormCloudService, $scormcloud_url, $scormcloud_app_id, $scormcloud_secret_key;

if (empty($scormcloud_url) || empty($scormcloud_app_id) || empty($scormcloud_secret_key)) {
	$msg = "<h3 style='color: red; margin-top: 50px'>SCORM Cloud Learning Module Not Configured</h3>".
	"<p>To use the SCORM Cloud Learning Module Plug-in you must first configure the url, ".
	"application ID and secret key in the plugin.php configuration file ([ilias root]/Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/plugin.php).".
	"<p>A free, basic-level application id can be requested from <a href='http://www.scorm.com'>www.scorm.com</a> for development/demo purposes.";
	
	// back button
	$msg .= '<div style="width: 100%; text-align: center"><button onclick="javascript: history.go(-1)">back</button></div>';
	
	print "<h3>".$msg."</h3>";
	
	// kill processing after we've written the above msg to the browser
	throw new Exception();  
}

$ScormCloudService = new ScormEngineService($scormcloud_url, $scormcloud_app_id, $scormcloud_secret_key);


?>
