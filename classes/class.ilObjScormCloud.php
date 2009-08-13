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

include_once("./Services/Repository/classes/class.ilObjectPlugin.php");

/**
* Application class for example repository object.
*
* @author Alex Killing <alex.killing@gmx.de>
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
