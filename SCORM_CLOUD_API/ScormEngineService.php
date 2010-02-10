<?php

/**
 * @version $Id$
 * @author  Brian Rogers <brian.rogers@scorm.com>
 * @license http://opensource.org/licenses/lgpl-license.php
 *          GNU Lesser General Public License, Version 2.1
 * @package RusticiSoftware.ScormEngine.Cloud
 */

require_once 'Configuration.php';
require_once 'ServiceRequest.php';
require_once 'CourseService.php';
require_once 'RegistrationService.php';
require_once 'UploadService.php';
require_once 'ReportingService.php';

class ScormEngineService{

	private $_configuration = null;
    private $_courseService = null;
    private $_registrationService = null;
    private $_uploadService = null;
    private $_ftpService = null;
	private $_serviceRequest = null;

	public function __construct($scormEngineServiceUrl, $appId, $securityKey) {

		$this->_configuration = new Configuration($scormEngineServiceUrl, $appId, $securityKey);
		$this->_serviceRequest = new ServiceRequest($this->_configuration);
        $this->_courseService = new CourseService($this->_configuration);
        $this->_registrationService = new RegistrationService($this->_configuration);
        $this->_uploadService = new UploadService($this->_configuration);
		$this->_reportingService = new ReportingService($this->_configuration);
        //$_ftpService = new FtpService(configuration);
	}
	
	/**
	* <summary>
    * Contains all SCORM Engine Package-level (i.e., course) functionality.
    * </summary>
	*/
    public function getCourseService()
    {
        return $this->_courseService;
    }

	/**
	* <summary>
    * Contains all SCORM Engine Package-level (i.e., course) functionality.
    * </summary>
	*/
    public function getRegistrationService()
    {
        return $this->_registrationService;
    }

	/**
	* <summary>
    * Contains all SCORM Engine Upload/File Management functionality.
    * </summary>
	*/
    public function getUploadService()
    {
        return $this->_uploadService;
    }

	/**
	* <summary>
    * Contains all SCORM Engine Reportage functionality.
    * </summary>
	*/
    public function getReportingService()
    {
        return $this->_reportingService;
    }

	/**
	* <summary>
    * Contains all SCORM Engine FTP Management functionality.
    * </summary>
	*/
    public function getFtpService()
    {
        return $this->_ftpService;
    }

	/**
	* <summary>
    * The Application ID obtained by registering with the SCORM Engine Service
    * </summary>
	*/
    public function getAppId()
    {
            return $this->_configuration->getAppId();
    }

	/**
	* <summary>
    * The security key (password) linked to the Application ID
    * </summary>
	*/
    public function getSecurityKey()
    {
            return $this->_configuration->getSecurityKey();
    }

	/**
	* <summary>
    * URL to the service, ex: http://services.scorm.com/EngineWebServices
    * </summary>
	*/
    public function getScormEngineServiceUrl()
    {
            return $this->_configuration->getScormEngineServiceUrl();
    }

	/**
	* <summary>
    * CreateNewRequest
    * </summary>
	*/
    public function CreateNewRequest()
    {
        return new ServiceRequest($this->_configuration);
    }
}
?>