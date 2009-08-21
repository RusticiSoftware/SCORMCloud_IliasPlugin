<?php

include_once("./Services/Repository/classes/class.ilRepositoryObjectPlugin.php");
 
/**
* SCORM Cloud repository object plugin
*
* @author John Hayden <john.hayden@scorm.com>
* @version $Id$
*
*/
class ilScormCloudPlugin extends ilRepositoryObjectPlugin
{
	function getPluginName()
	{
		return "ScormCloud";
	}
}
?>
