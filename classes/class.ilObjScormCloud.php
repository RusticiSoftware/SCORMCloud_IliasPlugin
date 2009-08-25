<?php

include_once("./Services/Repository/classes/class.ilObjectPlugin.php");

/**
* Application class for SCORM Cloud repository object.
*
* @author John Hayden <john.hayden@scorm.com>
*
* $Id$
*/
class ilObjScormCloud extends ilObjectPlugin
{
	/**
	* Constructor
	*
	* @access	public
	*/
	function __construct($a_ref_id = 0)
	{
		parent::__construct($a_ref_id);
	}
	

	/**
	* Get type.
	*/
	final function initType()
	{
		$this->setType("xscl");
	}
	
	/**
	* Create object
	*/
	function doCreate()
	{
		global $ilDB;
		
		$ilDB->manipulate("INSERT INTO rep_robj_xscl_pkg ".
			"(id, is_online, version, learners_see_rpt_details) VALUES (".
			$ilDB->quote($this->getId(), "integer").",".
			$ilDB->quote(o, "integer").",".
			$ilDB->quote(0, "integer").")".
			$ilDB->quote(1, "integer").")"
		);
	}
	
	/**
	* Read data from db
	*/
	function doRead()
	{
		global $ilDB;
		
		$set = $ilDB->query("SELECT * FROM rep_robj_xscl_pkg ".
			" WHERE id = ".$ilDB->quote($this->getId(), "integer")
			);
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$this->setOnline($rec["is_online"]);
			$this->setExistsOnCloud($rec["exists_on_cloud"]);
			$this->setVersion($rec["version"]);
			$this->setLearnersSeeRptDetails($rec["learners_see_rpt_details"]);
			$this->setEstimatedDuration($rec["estimated_duration"]);
		}
	}
	
	/**
	* Update data
	*/
	function doUpdate()
	{
		global $ilDB;
		
		$ilDB->manipulate($up = "UPDATE rep_robj_xscl_pkg SET ".
			" is_online = ".$ilDB->quote($this->getOnline(), "integer").",".
			" version = ".$ilDB->quote($this->getVersion(), "integer").",".
			" estimated_duration = ".$ilDB->quote($this->getEstimatedDuration(), "text").",".
			" learners_see_rpt_details = ".$ilDB->quote($this->getLearnersSeeRptDetails(), "integer").",".
			" exists_on_cloud = ".$ilDB->quote($this->getExistsOnCloud(), "integer").
			" WHERE id = ".$ilDB->quote($this->getId(), "integer")
			);
	}
	
	/**
	* Delete data from db
	*/
	function doDelete()
	{
		//TODO: Isn't being called when deleting objects for some reason, looks like
		// a plug-in bug.  This will leave dormant cloud packages/regs until fixed because
		// the DeleteCourse() call won't be done.
		
		global $ilDB;
		
		// TODO: Delete reg data? Or save it for historical purposes?  Don't delete for now...
		
		$ilDB->manipulate("DELETE FROM rep_robj_xscl_pkg WHERE ".
			" id = ".$ilDB->quote($this->getId(), "integer")
			);

		$ScormCloudService->getCourseService()->DeleteCourse($this->getId());
	}
	
	/**
	* Do Cloning
	*/
	function doClone($a_target_id,$a_copy_id,$new_obj)
	{
		global $ilDB;
		
		$new_obj->setOnline($this->getOnline());
		$new_obj->setVersion($this->getVersion());
		$new_obj->setExistsOnCloud($this->getExistsOnCloud());
		$new_obj->setEstimatedDuration($this->getEstimatedDuration());
		$new_obj->setLearnersSeeRptDetails($this->getLearnersSeeRptDetails());
		$new_obj->update();
	}
	
//
// Set/Get Methods for our example properties
//

	/**
	* Set online
	*
	* @param	boolean		online
	*/
	function setOnline($a_val)
	{
		$this->online = $a_val;
	}
	
	/**
	* Get online
	*
	* @return	boolean		online
	*/
	function getOnline()
	{
		return $this->online;
	}
	
	/**
	* Set version
	*
	* @param	boolean		online
	*/
	function setVersion($a_val)
	{
		$this->version = $a_val;
	}
	
	/**
	* Get online
	*
	* @return	boolean		online
	*/
	function getVersion()
	{
		return $this->version;
	}
	
	function setExistsOnCloud($a_val)
	{
		$this->existsOnCloud = $a_val;
	}

	function getExistsOnCloud()
	{
		return $this->existsOnCloud;
	}
	
	function setEstimatedDuration($a_val)
	{
		$this->estimatedDuration = $a_val;
	}

	function getEstimatedDuration()
	{
		return $this->estimatedDuration;
	}	

	function setLearnersSeeRptDetails($a_val)
	{
		$this->learnersSeeRptDetails = $a_val;
	}
	
	function getLearnersSeeRptDetails()
	{
		return $this->learnersSeeRptDetails;
	}

}
?>
