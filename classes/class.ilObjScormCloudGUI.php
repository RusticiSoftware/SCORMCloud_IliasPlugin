<?php

include_once("./Services/Repository/classes/class.ilObjectPluginGUI.php");
require_once("class.ilObjectScormCloudReg.php");
require_once("ScormCloudService.php");

/**
* User Interface class for SCORM Cloud repository object.
*
* User interface classes process GET and POST parameter and call
* application classes to fulfill certain tasks.
*
* @author John Hayden <john.hayden@scorm.com>
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
	* Overwritten from ancestor class ilObject2GUI.  Only change made was to comment
	* out the cloning code.
	*
	* @access	public
	*/
	function create()
	{
		global $rbacsystem, $tpl;

		$new_type = $_POST["new_type"] ? $_POST["new_type"] : $_GET["new_type"];

		if (!$rbacsystem->checkAccess("create", $_GET["ref_id"], $new_type))
		{
			$this->ilias->raiseError($this->lng->txt("permission_denied"),$this->ilias->error_obj->MESSAGE);
		}
		else
		{
			$this->ctrl->setParameter($this, "new_type", $new_type);
			$this->initEditForm("create", $new_type);
			$tpl->setContent($this->form->getHTML());
			
			// This is the only difference from the original base class create()
			//$clone_html = $this->fillCloneTemplate('', $new_type);
			
			$tpl->setContent($this->form->getHTML().$clone_html);
		}
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
		
		// a "tracking" tab
		if ($ilAccess->checkAccess("write", "", $this->object->getRefId()))
		{
			$ilTabs->addTab("tracking", $this->txt("tracking"), $ilCtrl->getLinkTarget($this, "showTracking"));
		}
		
		// standard epermission tab
		$this->addPermissionTab();
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
		
		// online 
		//$rd = new ilCheckboxInputGUI($this->lng->txt("learners_see_rpt_details"), "learners_see_rpt_details");   
		$rd = new ilCheckboxInputGUI("Learners See Report Details", "learners_see_rpt_details");
		$this->form->addItem($rd);
		
		// version
		$v = new ilNonEditableValueGUI($this->lng->txt("version"), "version");
		$this->form->addItem($v);
		
		// SCORM PIF
		if ($this->object->getExistsOnCloud()) 
		{
			$uploadTxt = $this->txt("upload_new_package_version");
		}
		else 
		{
			$uploadTxt = $this->txt("upload_package");
		}
		
		$sf = new ilCustomInputGUI($uploadTxt, "scormfile");
		$id = $this->object->getId();
		$mode = ""; // "update" if package already exists
		$uploadFormHtml = '<form action="'.$CFG->wwwroot.'/mod/rscloud/uploadhandler.php?id=' . $id . '&mode='.$mode.'" method="post" '.
		'enctype="multipart/form-data">'.
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
		$values["learners_see_rpt_details"] = $this->object->getLearnersSeeRptDetails();
		
		$this->form->setValuesByArray($values);
	}
	
	/**
	* Update properties
	*/
	public function updateProperties()
	{
		global $tpl, $lng, $ilCtrl, $ScormCloudService;
	
		if ($_FILES["scormcloudfile"]["name"])
		{
			// First, process SCORM Cloud upload
			if ($_FILES["scormcloudfile"]["error"] > 0)
			{
				error_log("Error: " . $_FILES["scormcloudfile"]["error"]);
			}
			else
			{
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

				// Where the file is going to be placed 
				$target_path = "uploads/";
		
				$target_path = $_FILES["scormcloudfile"]["tmp_name"] . '.zip'; 
				$tempFile = $_FILES["scormcloudfile"]["tmp_name"];
		
				 
				move_uploaded_file($_FILES['scormcloudfile']['tmp_name'], $target_path);
		
				$absoluteFilePathToZip = $target_path;
		
				try {		
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
				} catch(Exception $e) {
					// unlink deletes file
					unlink($absoluteFilePathToZip);
					throw($e);
				}
				
				// unlink deletes uploaded file
				unlink($absoluteFilePathToZip);
				
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


							$courseTitle = $course->getTitle();
							$versionCount = $course->getNumberOfVersions();

							$xmlstring = $courseService->GetMetadata($courseId, $versionCount-1, 0, 'xml');
								
							error_log("xmlString : ".$xmlstring);
							
							$this->object->setTitle($courseTitle);
							$this->object->setExistsOnCloud(true);
							$this->object->setVersion($versionCount);
							$this->object->update();
							
							//$this->object->refreshMetaData();
							
							break;
						}
					}
					
					// Here's where we set the default permissions.  Here's a spot where we have a good
					// refId so use it to set the initial permissions.
					if ($mode == "new") {
						// Looks like a good spot to modify permissions since the object has been created
						global $rbacadmin, $rbacreview;
						
						$user_role_id = 4;
						$guest_role_id = 5;
						$ref_id = $this->object->getRefId();
						
						$rbacadmin->grantPermission($guest_role_id, ilRbacReview::_getOperationIdsByName(array("visible")), $ref_id);
						$rbacadmin->grantPermission($user_role_id, ilRbacReview::_getOperationIdsByName(array("visible","read")), $ref_id);
					}
				}

			}
		}

		$this->initPropertiesForm();
		if ($this->form->checkInput())
		{
			//$this->object->setTitle($this->form->getInput("title"));
			$this->object->setDescription($this->form->getInput("desc"));
			$this->object->setOnline($this->form->getInput("online"));
			$this->object->setLearnersSeeRptDetails($this->form->getInput("learners_see_rpt_details"));
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
			    
			$usageLink="<button onclick=\"$('#usage').toggle();\" type=\"button\">SCORM Cloud Usage Statistics</button><br><br><div id=\"usage\" style=\"display: none;\"><iframe frameborder=\"0\" src='https://accounts.scorm.com/scorm-cloud-manager/public/usage-meter?appId=".$ScormCloudService->getAppId()."' style='width:100%;height:400px;'></iframe></div>";
			
			$iframe = "<iframe frameborder='0' src='".$pkgPropertyEditorUrl."' style='width:100%;height:600px;'> </iframe>";  
			//$iframe2 = "<iframe frameborder='0' src='https://accounts.scorm.com/scorm-cloud-manager/public/usage-meter?appId=".$ScormCloudService->getAppId()."' style='width:100%;height:600px;'> </iframe>";  
	 
			$tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/jquery.js");   
			$tpl->setContent($iframe.$usageLink);
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
			global $tpl, $ilias, $ScormCloudService;;

			$userId = $ilias->account->getId();
			$pkgId = $this->object->getId();

			$regs = ilObjScormCloudReg::GetRegistrationsForPackageId($pkgId);
			
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
						'<td class="std">'.($r->getScore() == SCORE_UNKNOWN ? "Unknown" : ucfirst($r->getScore())).'</td>'.
						'<td class="std">'.$r->getLastAccess().'</td>'.
						'<td class="std">'.$r->getAttemptCount().'</td>'.
						'<td class="std">'.$r->getVersion().'</td>'.
					'</tr>';
			}					
					
			$trackingTable .= '</table>';   
			                                            
			$tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/jquery.js");   
			$tpl->addJavaScript("http://cloud.scorm.com/Reportage/scripts/reportage.combined.nojquery.js");      
			
			// Reportage Report for all learners taking this course
			$rptService = $ScormCloudService->getReportingService(); 
			$rptAuth = $rptService->GetReportageAuth('FREENAV',true);
			
			$summaryWidgetSettings = new WidgetSettings();
			$summaryWidgetSettings->setShowTitle(true);
			$summaryWidgetSettings->setScriptBased(true);
			$summaryWidgetSettings->setEmbedded(true);
			$summaryWidgetSettings->setExpand(true);
			$summaryWidgetSettings->setDivname('summary');          
			$summaryWidgetSettings->setCourseId($pkgId);            
			
			$learnersWidgetSettings = new WidgetSettings();
			$learnersWidgetSettings->setShowTitle(true);
			$learnersWidgetSettings->setScriptBased(true);
			$learnersWidgetSettings->setEmbedded(true);
			$learnersWidgetSettings->setExpand(true);
			$learnersWidgetSettings->setDivname('learners');          
			$learnersWidgetSettings->setCourseId($pkgId);
			
			$activitiesWidgetSettings = new WidgetSettings();
			$activitiesWidgetSettings->setShowTitle(true);
			$activitiesWidgetSettings->setScriptBased(true);
			$activitiesWidgetSettings->setEmbedded(true);
			$activitiesWidgetSettings->setExpand(true);
			$activitiesWidgetSettings->setDivname('activities');          
			$activitiesWidgetSettings->setCourseId($pkgId);  
			
			$commentsWidgetSettings = new WidgetSettings();
			$commentsWidgetSettings->setShowTitle(true);
			$commentsWidgetSettings->setScriptBased(true);
			$commentsWidgetSettings->setEmbedded(true);
			$commentsWidgetSettings->setExpand(true);
			$commentsWidgetSettings->setDivname('comments');          
			$commentsWidgetSettings->setCourseId($pkgId);    
			
			$interactionsWidgetSettings = new WidgetSettings();
			$interactionsWidgetSettings->setShowTitle(true);
			$interactionsWidgetSettings->setScriptBased(true);
			$interactionsWidgetSettings->setEmbedded(true);
			$interactionsWidgetSettings->setExpand(true);
			$interactionsWidgetSettings->setDivname('interactions');          
			$interactionsWidgetSettings->setCourseId($pkgId);
			
			$summaryUrl = $rptService->GetWidgetUrl($rptAuth,'courseSummary',$summaryWidgetSettings); 
			$learnersUrl = $rptService->GetWidgetUrl($rptAuth,'learnerRegistration',$learnersWidgetSettings); 
			$activitiesUrl = $rptService->GetWidgetUrl($rptAuth,'courseActivities',$activitiesWidgetSettings); 
			$commentsUrl = $rptService->GetWidgetUrl($rptAuth,'courseComments',$commentsWidgetSettings); 
			$interactionsUrl = $rptService->GetWidgetUrl($rptAuth,'courseInteractionsShort',$interactionsWidgetSettings);   
		   
		    $reportageRpt = "<table cellspacing=0 cellpadding=0><tr><td colspan=2><div id='summary'>Loading...</div></td></tr>\n";      
			$reportageRpt .= "<tr><td valign='top'><div id='learners'></div</td>\n"; 
			$reportageRpt .= "<td valign='top'><div id='activities'></div></td></tr>\n"; 
			$reportageRpt .= "<tr><td valign='top'><div id='comments'></div></td>\n"; 
			$reportageRpt .= "<td valign='top'><div id='interactions'></div></td></tr></table>\n";  
			$reportageRpt .= '<script type="text/javascript">';
			$reportageRpt .= '$(document).ready(function(){';
			$reportageRpt .= '	loadScript("'.$summaryUrl.'");';        
			$reportageRpt .= '	loadScript("'.$learnersUrl.'");';    
			$reportageRpt .= '	loadScript("'.$activitiesUrl.'");';    
			$reportageRpt .= '	loadScript("'.$commentsUrl.'");';    
		    $reportageRpt .= '	loadScript("'.$interactionsUrl.'");';      
			$reportageRpt .= '});';
			$reportageRpt .= '</script>';     
			
			$stylesheet = $baseUrl."Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/reportage.css";     
			$reportageRpt .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />';
			
			$trackingTable .= $reportageRpt;

			$tpl->setContent($reportageRpt);       
			//$tpl->setContent($trackingTable);  
		}
		
		/**
		* Show tracking
		*/
		function showTrackingDetail($regId)
		{
			global $tpl;

			$tpl->setContent($this->getTrackingReportHtml($regId));
		}
		
		function getTrackingReportHtml($regId, $showDetails = true, $showTitle = true) {
			
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
						
			$regParts = split("-", $regId);
			$userObj = new ilObjUser((int)$regParts[1]);
				
			if ($showTitle) {
				$tableHeader = '<table style="width: 100%" class="fullwidth">'.
						'<tr class="tbltitle">'.
							'<th class="std">Course Report for '.$this->object->getTitle().'</th>'.
							'<th align="right" style="text-align: right" class="std">'.$userObj->getFirstName().' '.$userObj->getLastName().'</th>'.
						'</tr></table>';			
			} else {
				$tableHeader = '';
			}
			// The javascript report will render all activities			
			
			
			
			return $stylesheetLink.$tableHeader.'<div showDetails="'.$showDetails.'" dataUrl="'.$dataUrl.'" id="report"/>';
			
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
		
		$reg = ilObjScormCloudReg::getRegistration($pkgId, $userId);
		
		if ($reg == null) {
			$reg = new ilObjScormCloudReg($pkgId, $userId);
			$reg->doCreate();
		}

		$regService = $ScormCloudService->getRegistrationService();
		
		if($_GET['refreshRegStatus'] == "true") {
			
			$regStatus = $regService->GetRegistrationResult($reg->getPK(),0,'xml');
			$statusXml = simplexml_load_string($regStatus);
			
			$reg->setCompletion($statusXml->registrationreport->complete);
			$reg->setSatisfaction($statusXml->registrationreport->success);
			$reg->setTotalTime($statusXml->registrationreport->totaltime);
			$scoreString = $statusXml->registrationreport->score;
			if ($scoreString == "unknown") {
				$score = SCORE_UNKNOWN;
			} else {
				$score = (float)$scoreString * 100;
			}
			$reg->setScore($score);
			
			$reg->setLastAccess(ilUtil::now());
			$reg->setAttemptCount($reg->getAttemptCount() + 1);
			$reg->setVersion($this->object->getVersion());

			$reg->doUpdate();
		}
		
		$ilTabs->activateTab("content");

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
		
		if ($reg->getSatisfaction() == "unknown") {
			$status = $reg->getCompletion();
		} else {
			$status = $reg->getSatisfaction();
		}

		$currentUrl .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$returnUrl = $currentUrl."&refreshRegStatus=true";
		$launchHref = $regService->GetLaunchUrl($reg->getPK(), $returnUrl);

		$launchButton = "<button style='cursor: hand; font-size: 110%; width: 125px' type='button' onclick='window.location=\"".$launchHref."\";'> Launch </button>";
		$launchString .= "<div style='margin: 10px; width: 100%; text-align: center'".$launchButton."</div>";

		
		$tableHeader = '<table style="width: 100%" class="fullwidth">'.
				// '<tr class="tbltitle">'.
				// 	'<th class="std">'.$this->object->getTitle().'</th>'.
				// 	'<th align="right" style="text-align: right" class="std"><strong>Learner: </strong>'.$ilias->account->getFirstName().' '.$ilias->account->getLastName().'</th>'.
				// '</tr>'.
				'<tr class="tbltitle">'.
					'<td class="std" style="border: none; font-size: 90%">'.$this->object->getDescription()."</td>".	
					'<td align="right" style="border: none">'.$launchButton.'</td>';
				'</tr></table>';
	


		$launchString = "<table cellspacing=0 cellpadding=0 style='width: 100%; margin: 20px; margin-top: 10px'>".
			"<tr><td><strong>Status: </strong>".ucfirst($status).
			"</td><td ><strong>Score</strong>: ". ($reg->getScore() == SCORE_UNKNOWN ? "Unknown" : ucfirst($reg->getScore())).
			"</td><td ><strong>Total Time</strong>: ". $this->formatSeconds($reg->getTotalTime()).
			"</td></tr>".
			"<tr><td><strong>Attempts: </strong>".$reg->getAttemptCount().
			"</td><td><strong>Last Access: </strong>".$reg->getLastAccess().
			"</td><td>&nbsp;</td></tr>".
			"</table>";

		//$this->showHistoryReport($reg->getPK());
		
		
		// '<td class="std">'.$reg->getLastAccess().'</td>'.
		// '<td class="std">'.$reg->getAttemptCount().'</td>'.
		// 
		$tpl->setContent($tableHeader.$launchString.$this->getTrackingReportHtml($reg->getPK(), $this->object->getLearnersSeeRptDetails(), false));
		// $tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/jquery.js");
		// $tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/LaunchHistoryControlResources/scripts/LaunchHistoryReport.js");
		// $tpl->setContent($this->showHistoryReport($reg->getPK()));
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
	
	function showHistoryReport($regId) {
		
		global $ScormCloudService;
		
		$html = '
		<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
		<script type="text/javascript">
		    $(document).ready(function(){

		    });
		    </script>
		<script type="text/javascript">
		        var extConfigurationString = "";
		        var reportsHelperUrl = "Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/LaunchHistoryHelper.php";
		    </script>
		<link rel="Stylesheet" href="Customizing/global/plugins/Services/Repository/RepositoryObject/ScormCloud/tracking/LaunchHistoryControlResources/styles/LaunchHistoryReport.css"  type="text/css"/>
		<style>

		#column_headers {position:relative; font-weight:bold; border-bottom:1px solid #4171B5; padding: 3px 0px; margin-top:20px;}
		.headerLaunchTime {position:relative; width:300px; font-size:110%;}
		.headerExitTime {position:absolute; top:3px; left:200px; font-size:110%;}
		.headerDuration {position:absolute; top:3px; left:400px; font-size:110%;}
		.headerSatisfaction {position:absolute; top:3px; left:525px; font-size:110%;}
		.headerCompletion {position:absolute; top:3px; left:650px; font-size:110%;}


		.activityReportHeader {font-size:150%; position:relative;}
		.launchHistoryLink {position:absolute; right:25px; top:0px;}
		.launchHistoryLink img {margin-right:10px; vertical-align:top;}

		td.launch_headerName {
		color:#0077CC;
		font-size:120%;
		font-weight:bold;
		padding-bottom:0;
		width:154px;
		}
		td.launch_index {width:120px;}

		#historyInfo {margin-top:10px; margin-left:50px;}

		.instance_info_reg_fields_title, .score_fields_title {font-size:90%;}
		.info_label {font-size:90%;}

		</style>';

		$html .= '<div class ="actions"><div align="left" style="float:left;margin-top:2px;" ><strong>'.$_course['title'].' - '.$lp_title.' - '.$name.'</strong></div>';
		$html .=	'<div class="clear"></div></div>';

		$html .= "<div class='activityReportHeader'>".$this->lng->txt('launchHistoryReport');
		//$html .= '<div class="launchHistoryLink"><a href="cloudCourseDetails.php?regid='.$regid.'&course='.$cidReq.'&lp_id='.$lp_id.'&student_id='.$user_id.'"><img src="img/2leftarrow.gif"/>'.$this->lng->txt('cloudCourseDetails').'</a></div></div>';

		$regService = $ScormCloudService->getRegistrationService();

		$resultArray = $regService->GetLaunchHistory($regId);

		$html .= '<div id="historyInfo">';
			$html .= "<table>";
			$html .= "<tr><td class='launch_headerName' colspan='2'>Launch Instances</td>";
			$html .= "<td class='launch_time'>Launch Time</td>";
			$html .= "<td class='launch_duration'>Duration</td></tr></table>";
			$html .= '<div id="historyDetails" class="history_details" runat="server">';

		$html .= "<div class='launch_list'>";
		$idx = 1;
		foreach($resultArray as $result)
		{
		$lid = 	$result->getId();

		$html .= "<div class='LaunchPlaceHolder' id='launch_".$result->getId()."' regid='".$regid."'>";

		$html .= "<div class='hide_show_div' >";
			$html .= "<table>";
			$html .= "<tr><td class='launch_listPrefix'>+</td>";
			$html .= "<td class='launch_index'>".$idx.".</td>";
			$html .= "<td class='launch_time'>".$this->cloud_formatHistoryTime($result->getLaunchTime())."</td>";
			$html .= "<td class='launch_duration'><script>document.write(fmtDuration(".($this->cloud_convertTimeToInt($result->getExitTime()) - $this->cloud_convertTimeToInt($result->getLaunchTime()))* 1000 ."))</script></td>";
			$html .= "</tr></table>";
		$html .= "</div>";

		$html .= "<div class='launch_activity_list'><div id='receiver' class='div_receiver'></div></div>";
		$html .= "</div>";

		$idx++;
		}
		$html .= "  </div>";
		$html .= '</div></div>';
		
		return $html;
		
	}

	function cloud_formatHistoryTime($timestr){
		//2009-08-19T18:41:33.257+0000
		$dt = substr($timestr,5,2).'/'.substr($timestr,8,2).'/'.substr($timestr,0,4);
		$hr = (int)substr($timestr,11,2);
		if ($hr < 12){
			$suf = "AM";
		} else {
			$hr -= 12;
			$suf = "PM";
		}
		$min = substr($timestr,14,2);
		$sec = substr($timestr,17,2);

		return $dt.' '.$hr.':'.$min.':'.$sec.' '.$suf;
	}
	
	//input format 2009-08-11T19:01:50.081+0000 
	function cloud_convertTimeToInt($str){
		//echo 'hour: '.substr($str,11,2).'<br/>';
		//echo 'minute: '.substr($str,14,2).'<br/>';
		return mktime(substr($str,11,2),substr($str,14,2),substr($str,17,2),substr($str,5,2),substr($str,8,2), substr($str,0,4));
	}
	
	
}


?>
