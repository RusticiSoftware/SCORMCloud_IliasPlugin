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
			"(id, is_online, version) VALUES (".
			$ilDB->quote($this->getId(), "integer").",".
			$ilDB->quote(o, "integer").",".
			$ilDB->quote(0, "integer").")"
		);
		
		// Don't allow copy/clone of object.  This is now done by simply overridding the scomrcloudgui create() method
		// and it didn't work for those with Administrator role who see copy/clone regardless
		//
		//global $rbacadmin, $rbacreview;
		//$rbacadmin->deassignOperationFromObject($rbacreview->getTypeId('xscl'), ilRbacReview::_getOperationIdByName("copy"));
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
			" exists_on_cloud = ".$ilDB->quote($this->getExistsOnCloud(), "integer").
			" WHERE id = ".$ilDB->quote($this->getId(), "integer")
			);
	}
	
	/**
	* Delete data from db
	*/
	function doDelete()
	{
		global $ilDB;
		
		$ilDB->manipulate("DELETE FROM rep_robj_xscl_pkg WHERE ".
			" id = ".$ilDB->quote($this->getId(), "integer")
			);
			
		//TODO: Remove package (and regs) from the cloud
		
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
	
	/**
	* Set existsOnCloud
	*
	* @param	boolean		online
	*/
	function setExistsOnCloud($a_val)
	{
		$this->existsOnCloud = $a_val;
	}
	
	/**
	* Get existsOnCloud
	*
	* @return	boolean		online
	*/
	function getExistsOnCloud()
	{
		return $this->existsOnCloud;
	}

}
?>
