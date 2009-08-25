<?php 

/**
* SCORM Cloud Service Helper object definition
*
* @author John Hayden <john.hayden@scorm.com>
* @version $Id$
*
*/

require_once('../SCORM_CLOUD_API/ScormEngineService.php');
$ScormCloudService = new ScormEngineService($scormcloud_url, $scormcloud_app_id, $scormcloud_secret_key);

?>
