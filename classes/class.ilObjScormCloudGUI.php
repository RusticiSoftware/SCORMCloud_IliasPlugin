<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/


include_once("./Services/Repository/classes/class.ilObjectPluginGUI.php");
require_once("class.ilObjectScormCloudReg.php");
require_once("ScormCloudService.php");

/**
* User Interface class for example repository object.
*
* User interface classes process GET and POST parameter and call
* application classes to fulfill certain tasks.
*
* @author Alex Killing <alex.killing@gmx.de>
*
* $Id$
*
* Integration into control structure:
* - The GUI class is called by ilRepositoryGUI
* - GUI classes used by this class are ilPermissionGUI (provides the rbac
*   screens) and ilInfoScreenGUI (handles the info screen).
*
* @ilCtrl_isCalledBy ilObjScormCloudGUI: ilRepositoryGUI, ilAdministrationGUI, ilObjPluginDispatchGUI
* @ilCtrl_Calls ilObjScormCloudGUI: ilPermissionGUI, ilInfoScreenGUI
*
*/
class ilObjScormCloudGUI extends ilObjectPluginGUI
{
	/**
	* Initialisation
	*/
	protected function afterConstructor()
	{
		// anything needed after object has been constructed
		// - example: append my_id GET parameter to each request
		//   $ilCtrl->saveParameter($this, array("my_id"));
	}
	
	/**
	* Get type.
	*/
	final function getType()
	{
		return "xscl";
	}
	
	/**
	* Handles all commmands of this class, centralizes permission checks
	*/
	function performCommand($cmd)
	{
		switch ($cmd)
		{
			case "editProperties":		// list all commands that need write permission here
			case "editPackageProperties":	
			case "showTracking":	
			case "updateProperties":
			case "editMetadata":
			case "showLearningProgress":
			//case "...":
				$this->checkPermission("write");
				$this->$cmd();
				
			case "showContent":			// list all commands that need read permission here
			//case "...":
			//case "...":
				$this->checkPermission("read");
				$this->$cmd();
				break;
		}
	}

	/**
	* After object has been created -> jump to this command
	*/
	function getAfterCreationCmd()
	{
		return "editProperties";
	}

	/**
	* Get standard command
	*/
	function getStandardCmd()
	{
		return "showContent";
	}
	
	
	function showLearningProgress() {
		
		// global $ilCtrl;
		// 
		// include_once './Services/Tracking/classes/class.ilLearningProgressGUI.php';
		// 
		// $new_gui =& new ilLearningProgressGUI(LP_MODE_REPOSITORY,$this->object->getRefId());
		// $ilCtrl->forwardCommand($new_gui);
		
	}

//
// DISPLAY TABS
//
	
	/**
	* Set tabs
	*/
	function setTabs()
	{
		global $ilTabs, $ilCtrl, $ilAccess;
		
		// tab for the "show content" command
		if ($ilAccess->checkAccess("read", "", $this->object->getRefId()))
		{
			$ilTabs->addTab("content", $this->txt("content"), $ilCtrl->getLinkTarget($this, "showContent"));
		}

		// standard info screen tab
		$this->addInfoTab();

		// a "properties" tab
		if ($ilAccess->checkAccess("write", "", $this->object->getRefId()))
		{
			$ilTabs->addTab("properties", $this->txt("properties"), $ilCtrl->getLinkTarget($this, "editProperties"));
		}
		
		// a "properties" tab
		if ($ilAccess->checkAccess("write", "", $this->object->getRefId()))
		{
			$ilTabs->addTab("package_properties", $this->txt("package_properties"), $ilCtrl->getLinkTarget($this, "editPackageProperties"));
		}
		
		// if ($ilAccess->checkAccess("write", "", $this->object->getRefId()))
		// {		
		// 	$ilTabs->addTab("meta_data", $this->txt("meta_data"), $ilCtrl->getLinkTarget($this, "editMetadata"));
		// }
		// 
		// if ($ilAccess->checkAccess("write", "", $this->object->getRefId()))
		// {		
		// 	$ilTabs->addTab("learning_progress", $this->txt("learning_progress"), $ilCtrl->getLinkTarget($this, "showLearningProgress"));
		// }
		
		// a "tracking" tab
		if ($ilAccess->checkAccess("write", "", $this->object->getRefId()))
		{
			$ilTabs->addTab("tracking", $this->txt("tracking"), $ilCtrl->getLinkTarget($this, "showTracking"));
		}
		

		// standard epermission tab
		$this->addPermissionTab();
	}
	

// THE FOLLOWING METHODS IMPLEMENT SOME EXAMPLE COMMANDS WITH COMMON FEATURES
// YOU MAY REMOVE THEM COMPLETELY AND REPLACE THEM WITH YOUR OWN METHODS.

	function editMetadata() 
	{
		global $ilTabs, $ilCtrl, $tpl;

		$ilTabs->activateTab("meta_data");		
		$tpl->setContent("If I can tie into metadata, it'll go HERE");	
	}

//
// Edit properties form
//

	/**
	* Edit Properties. This commands uses the form class to display an input form.
	*/
	function editProperties()
	{
		global $tpl, $ilTabs;
		
		$ilTabs->activateTab("properties");
		$this->initPropertiesForm();
		$this->getPropertiesValues();
		// $tpl->addJavaScript("./Modules/Scorm2004/scripts/questions/jquery.js");
		// $tpl->addJavaScript("./Modules/Scorm2004/scripts/questions/jquery-ui-min.js");
		$tpl->setContent($this->form->getHTML());
	}
	
	
	/**
	* Init  form.
	*
	* @param        int        $a_mode        Edit Mode
	*/
	public function initPropertiesForm()
	{
		global $ilCtrl;
	
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();
	
		// title
		$ti = new ilTextInputGUI($this->txt("title"), "title");
		$ti->setRequired(true);
		$this->form->addItem($ti);
		
		// description
		$ta = new ilTextAreaInputGUI($this->txt("description"), "desc");
		$this->form->addItem($ta);
		
		// online
		$cb = new ilCheckboxInputGUI($this->lng->txt("online"), "online");
		$this->form->addItem($cb);
		
		// version
		$v = new ilNonEditableValueGUI($this->lng->txt("version"), "version");
		$this->form->addItem($v);
		
		// SCORM PIF
		
		if ($this->object->getExistsOnCloud()) 
		{
			$uploadTxt = "Upload New SCORM/AICC .zip";
		}
		else 
		{
			$uploadTxt = "Upload SCORM/AICC .zip";
		}
		
		$sf = new ilCustomInputGUI($uploadTxt, "scormfile");
		$id = $this->object->getId();
		$mode = ""; // "update" if package already exists
		$uploadFormHtml = '<form action="'.$CFG->wwwroot.'/mod/rscloud/uploadhandler.php?id=' . $id . '&mode='.$mode.'" method="post" '.
		'enctype="multipart/form-data">'.
		'<label for="file">Filename:</label>'.
		'<input type="file" name="file" id="file" /> '.
		'<br />'.
		'<input type="submit" name="submit" value="Submit" />'.
		'</form>';
		$uploadInputItem = '<input type="file" name="scormcloudfile" id="scormcloudfile" /> ';
		$sf->setHtml($uploadInputItem);
		$this->form->addItem($sf);
		$this->form->setMultipart(true);
		
		$this->form->addCommandButton("updateProperties", $this->txt("save"));
	                
		$this->form->setTitle($this->txt("edit_properties"));
		$this->form->setFormAction($ilCtrl->getFormAction($this));
	}
	
	/**
	* Get values for edit properties form
	*/
	function getPropertiesValues()
	{
		$values["title"] = $this->object->getTitle();
		$values["desc"] = $this->object->getDescription();
		$values["online"] = $this->object->getOnline();
		$values["version"] = $this->object->getVersion();
		
		$this->form->setValuesByArray($values);
	}
	
	/**
	* Update properties
	*/
	public function updateProperties()
	{
		global $tpl, $lng, $ilCtrl, $ScormCloudService;
	
//	echo "<script>alert('" . "NAME: " . $_FILES["scormcloudfile"]["name"] . "');</script>";
	
		if ($_FILES["scormcloudfile"]["name"])
		{
			// First, process SCORM Cloud upload
			if ($_FILES["scormcloudfile"]["error"] > 0)
			{
				error_log("Error: " . $_FILES["scormcloudfile"]["error"]);
			}
			else
			{
				//echo "Upload: " . $_FILES["file"]["name"] . "<br />";
				//echo "Type: " . $_FILES["file"]["type"] . "<br />";
				//echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
				//echo "Stored in: " . $_FILES["file"]["tmp_name"];
			
				$id = $this->object->getId();
				if ($this->isPackageImportedInScormCloud())
				{
					$mode = "update"; 
				}
				else 
				{
					$mode = "new"; 
				}
		
				$courseService = $ScormCloudService->getCourseService();
				$uploadService = $ScormCloudService->getUploadService();
		
				$courseId = $id;
				//echo '$courseId='.$courseId.'<br>';
				// Where the file is going to be placed 
				$target_path = "uploads/";
		
				/* Add the original filename to our target path.  
				Result is "uploads/filename.extension" */
		
				//if ($pifdir = make_upload_directory("$courseid/$CFG->moddata/rscloud")) {
				//echo $pifdir;
				$target_path = $_FILES["scormcloudfile"]["tmp_name"] . '.zip'; 
				//echo $target_path;
				$tempFile = $_FILES["scormcloudfile"]["tmp_name"];
		
				move_uploaded_file($_FILES['scormcloudfile']['tmp_name'], $target_path);
		
				$absoluteFilePathToZip = $target_path;
		
				//now upload the file and save the resulting location
				$location = $uploadService->UploadFile($absoluteFilePathToZip,null);
		
				if($mode == 'update')
				{
					//version the uploaded course
					$ir = $courseService->VersionUploadedCourse($courseId, $location, null);
		
				}else{
					//import the uploaded course
					$ir = $courseService->ImportUploadedCourse($courseId, $location, null);				
		
				}
				
				//TODO: Expose and view import result object
			
				// if ($ir->getWasSuccessful())
				// {
				// 	$this->object->setTitle($ir->getTitle());
				// 	$this->object->update();					
				// 	
				// }
			

				// Don't have $ir now... so by virtue of it existing in this next call we'll call it good
				if ($this->isPackageImportedInScormCloud())
				{
					$allResults = $courseService->GetCourseList();

					$xmlstring = '';
					$courseTitle = '';

					foreach($allResults as $course)
					{
						if($course->getCourseId() == $this->object->getId())
						{
							$xmlstring = $courseService->GetMetadata($courseid, 0, 1, null,'xml');
							$courseTitle = $course->getTitle();
							$versionCount = $course->getNumberOfVersions();
							
							$this->object->setTitle($courseTitle);
							$this->object->setExistsOnCloud(true);
							$this->object->setVersion($versionCount);
							$this->object->update();
							
							//$this->object->refreshMetaData();
							
							break;
						}
					}
				}
				


			
				// Process results
			
				//this is in an iframe, so refresh the parent window
				//echo '<script>window.parent.location=window.parent.location;</script>';
		
				//}
			}
		
		// Finished with SCORM Cloud import...
		}
	
	
		$this->initPropertiesForm();
		if ($this->form->checkInput())
		{
			//$this->object->setTitle($this->form->getInput("title"));
			$this->object->setDescription($this->form->getInput("desc"));
			$this->object->setOnline($this->form->getInput("online"));
			$this->object->update();		
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "editProperties");
		}

		$this->form->setValuesByPost();
		$tpl->setContent($this->form->getHtml());
	}

	//
	// Edit SCORM Cloud packaged properties form
	//

		/**
		* Edit Properties. This commands uses the form class to display an input form.
		*/
		function editPackageProperties()
		{
			global $tpl, $ilTabs, $ScormCloudService;

			$ilTabs->activateTab("package_properties");			
			
			if (!empty($_SERVER['HTTPS'])) {
				$currentUrl = "https://";
			} else {
				$currentUrl = "http://";
			}
			$currentUrl .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
			
			$removeMe = strstr($currentUrl, "ilias.php");
			$baseUrl = str_replace($removeMe, "", $currentUrl);
			$relStylesheetUrl = ilUtil::getNewContentStyleSheetLocation();
			$relStylesheetUrl = str_replace("./", "", $relStylesheetUrl);
			$stylesheet = $baseUrl.$relStylesheetUrl;			
			
			if (empty($ScormCloudService)) {
				throw new Exception();
			}
			
			$courseService = $ScormCloudService->getCourseService();
			$pkgPropertyEditorUrl = $courseService->GetPropertyEditorUrl($this->object->getId(), $stylesheet);
			
			$iframe = "<iframe frameborder='0' src='".$pkgPropertyEditorUrl."' style='width:100%;height:600px;'> </iframe>";
			
			$tpl->setContent($iframe);
		}


		function showTracking()
		{
			global $ilTabs;
			$ilTabs->activateTab("tracking");
			
			if(empty($_GET['regId'])) 
			{
				$this->showTrackingMain();
			}
			else 
			{
				$this->showTrackingDetail($_GET['regId']);
			}
		}

		/**
		* Show tracking
		*/
		function showTrackingMain()
		{
			global $tpl, $ilias;

			$userId = $ilias->account->getId();
			$pkgId = $this->object->getId();

			$reg = new ilObjScormCloudReg();
			$regs = $reg->GetRegistrationsForPackageId($pkgId);
			
			if (!empty($_SERVER['HTTPS'])) {
				$currentUrl = "https://";
			} else {
				$currentUrl = "http://";
			}

			$currentUrl .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				
			$trackingTable = '<table class="fullwidth">'.
					'<tr class="tbltitle">'.
						'<th class="std" colspan="8">Tracking Items</th>'.
					'</tr>'.
					'<tr class="tblheader">'.
						'<th class="tblheader,std" nowrap="nowrap">Name</th>'.'<th class="tblheader,std" nowrap="nowrap">Completion</th>'.
						'<th class="tblheader,std" nowrap="nowrap">Satisfaction</th>'.'<th class="tblheader,std" nowrap="nowrap">Total Time</th>'.
						'<th class="tblheader,std" nowrap="nowrap">Score</th>'.'<th class="tblheader,std" nowrap="nowrap">Last Access</th>'.
						'<th class="tblheader,std" nowrap="nowrap">Attempts</th>'.'<th class="tblheader,std" nowrap="nowrap">Version</th>'.
					'</tr>';

			foreach ($regs as $r) {

				$userObj = new ilObjUser($r->getUserId());
				

				$trackingTable .= '<tr class="tblrow1">'.
						'<td class="std"><a href="'.$currentUrl.'&regId='.$r->getPK().'">'.$userObj->getLastName().', '.$userObj->getFirstName().'</a></td>'.
						'<td class="std">'.ucfirst($r->getCompletion()).'</td>'.
						'<td class="std">'.ucfirst($r->getSatisfaction()).'</td>'.
						'<td class="std">'.$this->formatSeconds($r->getTotalTime()).'</td>'.
						'<td class="std">'.$r->getScore().'</td>'.
						'<td class="std">'.$r->getLastAccess().'</td>'.
						'<td class="std">'.$r->getAttemptCount().'</td>'.
						'<td class="std">'.$r->getVersion().'</td>'.
					'</tr>';
			}					
					
			$trackingTable .= '</table>';
			

			

			$tpl->setContent($trackingTable);
		}
		
		/**
		* Show tracking
		*/
		function showTrackingDetail($regId)
		{
			global $tpl, $ScormCloudService;

			$sr = $ScormCloudService->CreateNewRequest();
			$parameterMap = Array('regid' => $regId);
			$parameterMap['resultsformat'] = "full";
			$parameterMap['format'] = "json";
			$parameterMap['jsoncallback'] = "getRegistrationResultCallback";
			$sr->setMethodParams($parameterMap);
			$dataUrl = $sr->ConstructUrl("rustici.registration.getRegistrationResult");

			// Need jquery and the regreport.js 
			$tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/jquery.js");
			//$tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/jquery-ui.js");
			$tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/regreport.js");
			
			$removeMe = strstr($currentUrl, "ilias.php");
			$baseUrl = str_replace($removeMe, "", $currentUrl);
			$stylesheet = $baseUrl."Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/regreport.css";
			//$stylesheet = $baseUrl."Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/css/ui-lightness/jquery-ui-1.7.2.custom.css";
			

			$stylesheetLink = '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />';
			

			$tpl->setContent($stylesheetLink.'<div dataUrl="'.$dataUrl.'" id="report"/>');
		}


//
// Show content
//
	/**
	* Show content
	*/
	function showContent()
	{
		global $tpl, $ilTabs, $ScormCloudService;
		
		global $ilias;
		
		$userId = $ilias->account->getId();
		$pkgId = $this->object->getId();
		
		$regClass = new ilObjScormCloudReg();
		$reg = $regClass->getRegistration($pkgId, $userId);
		
		if ($reg == null) {
			$reg = new ilObjScormCloudReg($pkgId, $userId);
			$reg->doCreate();
		}

		$regService = $ScormCloudService->getRegistrationService();
		
		if($_GET['refreshRegStatus'] == "true") {
			
			$regStatus = $regService->GetRegistrationResult($reg->getPK(),0,'xml');
			$statusXml = simplexml_load_string($regStatus);
			
			// echo $statusXml->registrationreport->complete;
			// echo $statusXml->registrationreport->success;
			// echo $statusXml->registrationreport->totalTime;
			// echo $statusXml->registrationreport->score;
			
			$reg->setCompletion($statusXml->registrationreport->complete);
			$reg->setSatisfaction($statusXml->registrationreport->success);
			$reg->setTotalTime($statusXml->registrationreport->totaltime);
			$reg->setScore($statusXml->registrationreport->score * 100);
			
			$reg->setLastAccess(ilUtil::now());
			$reg->setAttemptCount($reg->getAttemptCount() + 1);
			$reg->setVersion($this->object->getVersion());
			
			// format with echo date("m/d/y",time())
			$reg->doUpdate();
		}
		
		$ilTabs->activateTab("content");

		$tpl->setContent("Hello World. My User ID is ".$ilias->account->getId()."Name: ".$ilias->account->getFirstName()." ".$ilias->account->getLastName());
		
		$userId = $ilias->account->getId();
		$pkgId = $this->object->getId();
		
		if (!$reg->getExistsOnCloud()) {
			
			$firstName = $ilias->account->getFirstName();
			$lastName = $ilias->account->getLastName();
			
			if (empty($firstName)) {
				$firstName = "UNKNOWN";
			}
			
			if (empty($lastName)) {
				$lastName = "UNKNOWN";
			}

			$regService->CreateRegistration($reg->getPK(), $pkgId, $userId, $firstName, $lastName);
			
			$reg->setExistsOnCloud(true);
			$reg->doUpdate();
		}
		
		if (!$reg->getExistsOnCloud()) {
			// error!
		}
		
		if (!empty($_SERVER['HTTPS'])) {
			$currentUrl = "https://";
		} else {
			$currentUrl = "http://";
		}
		
		$currentUrl .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$returnUrl = $currentUrl."&refreshRegStatus=true";
		$launchHref = $regService->GetLaunchUrl($reg->getPK(), $returnUrl);
		$launchString = "<a href='".$launchHref."'>Launch</a>";
		$launchString = "<div style='margin-top: 60px; text-align: center'>".
			"<button style='cursor: hand; font-size: 120%; width: 125px' type='button' onclick='window.location=\"".$launchHref."\";'> Launch </button>".
			"<br/><br/><br/>".ucfirst($reg->getCompletion())." / ".ucfirst($reg->getSatisfaction()).
			"<br/><br/>Score: ".ucfirst($reg->getScore()).
			//"<br/><br/>Total Time: ". $reg->getTotalTime()." seconds".
			"<br/><br/>Total Time: ". $this->formatSeconds($reg->getTotalTime()).
			"</div>";
		
		$tpl->setContent($launchString);
	}
	
	
	function isPackageImportedInScormCloud()
	{
		global $ScormCloudService;

		$courseService = $ScormCloudService->getCourseService();

		$allResults = $courseService->GetCourseList();

		$courseExists = false;
		foreach($allResults as $course)
		{
			if($course->getCourseId() == $this->object->getId())
			{
				$courseExists = true;
				break;
			}
		}

		return $courseExists;
	}
	
	function formatSeconds($seconds)
	{
		$time = str_pad(intval(intval($seconds/3600)),2,"0",STR_PAD_LEFT).":"
		. str_pad(intval(($seconds / 60) % 60),2,"0",STR_PAD_LEFT).":"
		. str_pad(intval($seconds % 60),2,"0",STR_PAD_LEFT) ;
		
		return $time;
	}	
	
	
}
?>
