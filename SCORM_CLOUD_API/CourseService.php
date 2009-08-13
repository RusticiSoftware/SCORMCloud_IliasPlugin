<?php

require_once 'ServiceRequest.php';
require_once 'CourseData.php';
require_once 'Enums.php';

/// <summary>
/// Client-side proxy for the "rustici.course.*" Hosted SCORM Engine web
/// service methods.  
/// </summary>
class CourseService{
	
	private $_configuration = null;
	
	public function __construct($configuration) {
		$this->_configuration = $configuration;
		//echo $this->_configuration->getAppId();
	}
	
	/// <summary>
    /// Import a SCORM .pif (zip file) from the local filesystem.
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="absoluteFilePathToZip">Full path to the .zip file</param>
    /// <param name="itemIdToImport">ID of manifest item to import. If null, root organization is imported</param>
    /// <param name="permissionDomain">An permission domain to associate this course with, 
    /// for ftp access service (see ftp service below). 
    /// If the domain specified does not exist, the course will be placed in the default permission domain</param>
    /// <returns>List of Import Results</returns>
    public function ImportCourse($courseId, $absoluteFilePathToZip, $itemIdToImport, $permissionDomain)
    {
        //TODO: Can we really offer the multi-import zip of zips via this service
        // since we specify a single courseId?

        $request = new ServiceRequest($this->_configuration);
        
		$mParams = array('courseid' => $courseId);
		$request->setMethodParams($mParams);
        //if (!String.IsNullOrEmpty(itemIdToImport))
        //    request.Parameters.Add("itemid", itemIdToImport);
        //if (!String.IsNullOrEmpty(permissionDomain))
        //    request.Parameters.Add("pd", permissionDomain);

        $request->setFileToPost($absoluteFilePathToZip);
        $response = $request->CallService("rustici.course.importCourse");
        echo $response;
//return ImportResult.ConvertToImportResults($response);
    }
    /// <summary>
    /// Import new version of an existing course from a SCORM .pif (zip file)
    /// on the local filesystem.
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="absoluteFilePathToZip">Full path to the .zip file</param>
    /// <returns>List of Import Results</returns>
    public function VersionCourse($courseId, $absoluteFilePathToZip)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('courseid' => $courseId);
		$request->setMethodParams($params);
        $request->setFileToPost($absoluteFilePathToZip);
        $response = $request->CallService("rustici.course.versionCourse");
        return $response;
    }
     /// <summary>
     /// Import a SCORM .pif (zip file) from an existing .zip file on the
     /// Hosted SCORM Engine server.
     /// </summary>
     /// <param name="courseId">Unique Identifier for this course.</param>
     /// <param name="path">The relative path (rooted at your specific appid's upload area)
     /// where the zip file for importing can be found</param>
     /// <param name="fileName">Name of the file, including extension.</param>
     /// <param name="itemIdToImport">ID of manifest item to import</param>
     /// <param name="permissionDomain">An permission domain to associate this course with, 
     /// for ftp access service (see ftp service below). 
     /// If the domain specified does not exist, the course will be placed in the default permission domain</param>
     /// <returns>List of Import Results</returns>
     public function ImportUploadedCourse($courseId, $path, $permissionDomain = null)
     {

        $request = new ServiceRequest($this->_configuration);
		$params = array('courseid'=>$courseId,
						'path'=>$path);

       // if (!is_null($itemIdToImport))
		//{
//			$params[] = 'itemid' => $itemIdToImport;
		//}
        
         //if (!String.IsNullOrEmpty(permissionDomain))
         //    request.Parameters.Add("pd", permissionDomain);
		$request->setMethodParams($params);
         $response = $request->CallService("rustici.course.importCourse");
         //return ImportResult->ConvertToImportResults($response);
		error_log('rustici.course.importCourse : '.$response);
		return $response;
     }

    /// <summary>
    /// Import new version of an existing course from a SCORM .pif (zip file) from 
    /// an existing .zip file on the Hosted SCORM Engine server.
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="domain">Optional security domain for the file.</param>
    /// <param name="fileName">Name of the file, including extension.</param>
    /// <returns>List of Import Results</returns>
    public function VersionUploadedCourse($courseId, $path, $permissionDomain = null)
    {

        $request = new ServiceRequest($this->_configuration);
       	$params = array('courseid'=>$courseId,
						'path'=>$path);
		$request->setMethodParams($params);
		
       	$response = $request->CallService("rustici.course.versionCourse");
		error_log('rustici.course.versionCourse : '.$response);
        //return ImportResult->ConvertToImportResults($response);
    }

    /// <summary>
    /// Retrieve a list of high-level data about all courses owned by the 
    /// configured appId.
    /// </summary>
 	/// <param name="courseIdFilterRegex">Regular expresion to filter the courses by ID</param>
    /// <returns>List of Course Data objects</returns>
    public function GetCourseList($courseIdFilterRegex = null)
    {
        $request = new ServiceRequest($this->_configuration);

		if(isset($courseIdFilterRegex))
		{
			$params = array('filter'=>$courseIdFilterRegex);
			$request->setMethodParams($params);
		}

        $response = $request->CallService("rustici.course.getCourseList");
		$CourseDataObject = new CourseData(null);
        return $CourseDataObject->ConvertToCourseDataList($response);
    }
   /// <summary>
    /// Delete the specified course
    /// </summary>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <param name="deleteLatestVersionOnly">If false, all versions are deleted</param>
    public function DeleteCourse($courseId, $deleteLatestVersionOnly)
    {
        $request = new ServiceRequest($this->_configuration);
       	$params = array('courseid'=>$courseId);
        if (isset($deleteLatestVersionOnly))
		{ 
            $params['versionid'] = 'latest';
		}
		$request->setMethodParams($params);
        $response = $request->CallService("rustici.course.deleteCourse");
		return $response;
    }

    /// <summary>
    /// Delete the specified version of a course
    /// </summary>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <param name="versionId">Specific version of course to delete</param>
    public function DeleteCourseVersion($courseId, $versionId)
    {
        $request = new ServiceRequest($this->_configuration);
		$params = array('courseid' => $courseId,
						'versionid' => $versionId);
       	$request->setMethodParams($params);
        $response = $request->CallService("rustici.course.deleteCourse");
		return $response;
    }
	 /// <summary>
        /// Get the Course Metadata in XML Format
        /// </summary>
        /// <param name="courseId">Unique Identifier for the course</param>
        /// <param name="versionId">Version of the specified course</param>
        /// <param name="scope">Defines the scope of the data to return: Course or Activity level</param>
        /// <param name="format">Defines the amount of data to return:  Summary or Detailed</param>
        /// <returns>XML string representing the Metadata</returns>
	    public function GetMetadata($courseId, $versionId, $scope, $format)
	    {
			$enum = new Enum();
            $request = new ServiceRequest($this->_configuration);
			$params = array('courseid'=>$courseId);
            
            if ($versionId != 0)
            {
                $params['versionid'] = $versionId;
            }
            $params['scope'] = $enum->getMetadataScope($scope);
            $params['format'] = $enum->getDataFormat($format);
			
			$request->setMethodParams($params);
			
            $response = $request->CallService("rustici.course.getMetadata");
            
            // Return the subset of the xml starting with the top <object>
            return $response;
	    }
	
	    /// <summary>
        /// Get the url that can be opened in a browser and used to preview this course, without
        /// the need for a registration.
        /// </summary>
        /// <param name="courseId">Unique Course Identifier</param>
        /// <param name="versionId">Version Id</param>
        public function GetPreviewUrl($courseId, $redirectOnExitUrl)
        {
            $request = new ServiceRequest($this->_configuration);
            $params = array('courseid' => $courseId);
            if(isset($redirectOnExitUrl))
			{
                $params['redirecturl'] = $redirectOnExitUrl;
			}
			$request->SetMethodParams($params);
				
            return $request->ConstructUrl("rustici.course.preview");
        }

        /// <summary>
        /// Gets the url to view/edit the package properties for this course.  Typically
        /// used within an IFRAME
        /// </summary>
        /// <param name="courseId">Unique Identifier for the course</param>
        /// <returns>Signed URL to package property editor</returns>
        /// <param name="notificationFrameUrl">Tells the property editor to render a sub-iframe
        /// with the provided url as the src.  This can be used to simulate an "onload"
        /// by using a notificationFrameUrl that's the same domain as the host system and
        /// calling parent.parent.method()</param>
        public function GetPropertyEditorUrl($courseId, $stylesheetUrl, $notificationFrameUrl)
        {
            // The local parameter map just contains method methodParameters.  We'll
            // now create a complete parameter map that contains the web-service
            // params as well the actual method params.
			$request = new ServiceRequest($this->_configuration);
            $parameterMap = Array('action' => 'properties.view');
            $parameterMap['package'] = 'AppId|'.$this->_configuration->getAppId().'!PackageId|'.$courseId;
            $parameterMap['appid'] = $this->_configuration->getAppId();
            $parameterMap['ts'] = gmdate("YmdHis");

            if(isset($notificationFrameUrl)){
                $parameterMap['notificationframesrc'] = $notificationFrameUrl;
			}
            if(isset($stylesheetUrl)){
                $parameterMap['stylesheet'] = $stylesheetUrl;
			}
			
            // Construct the url, concatonate all parameters as query string parameters
            $url = $this->_configuration->getScormEngineServiceUrl()."/widget";
			
            //$cnt = 0;
            //foreach ($parameterMap as $key => $value)
            //{
                // Create a query string with URL-encoded values
            //    $url .= ($cnt++ == 0 ? "?" : "&").$key."=".$parameterMap[$key];
            //}
		
            $url .= '?'.$request->signParams($this->_configuration->getSecurityKey(), $parameterMap);

            //if (url.Length > 2000)
            //    throw new ApplicationException("URL > 2000 bytes");
			
            return $url;
        }
}
?>