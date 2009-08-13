<?php

require_once 'ServiceRequest.php';
require_once 'CourseData.php';
require_once 'Enums.php';
require_once 'UploadToken.php';

/// <summary>
/// Client-side proxy for the "rustici.course.*" Hosted SCORM Engine web
/// service methods.  
/// </summary>
class UploadService{
	
	private $_configuration = null;
	
	public function __construct($configuration) {
		$this->_configuration = $configuration;
		//echo $this->_configuration->getAppId();
	}
	
	public function GetUploadToken()
    {
        $request = new ServiceRequest($this->_configuration);
        $response = $request->CallService("rustici.upload.getUploadToken");
        return new UploadToken($response);
    }


    public function UploadFile($absoluteFilePathToZip, $permissionDomain = null)
    {
        $token = $this->GetUploadToken();

        $request = new ServiceRequest($this->_configuration);
        $request->setFileToPost($absoluteFilePathToZip);

        $serviceUrl = "http://".$token->getServer()."/EngineWebServices";
        
		$mParams = array('token' => $token->getTokenId());
	

        if (isset($permissionDomain)) {
            $mParams["pd"] = $permissionDomain;
        }

		$request->setMethodParams($mParams);

        $response = $request->CallService("rustici.upload.uploadFile", $serviceUrl);

		//echo $response;

		$xml = simplexml_load_string($response);
		if (false === $xml) {
            //handle this error
        }

        $location = $xml->location;

		//echo '<br><br>location : '.$location;
        return $location;
    }
	
}

?>