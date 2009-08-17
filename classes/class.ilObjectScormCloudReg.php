<?php

require_once('ScormCloudService.php');

class ilObjScormCloudReg
{
	/**
	* Constructor
	*
	* @access	public
	*/
	function __construct($pkgIdPK, $userIdPK)
	{
		$this->setPkgId($pkgIdPK);
		$this->setUserId($userIdPK);
		$this->setCompletion("incomplete");
		$this->setSatisfaction("unknown");
		$this->setScore(0);
		$this->setTotalTime(0);
		$this->setExistsOnCloud(false);
		$this->setAttemptCount(0);
		$this->setVersion(1);
		$this->setLastAccess(null);
	}

	public static function getRegistration($pkgIdPK, $userIdPK) 
	{
		global $ilDB;
		$reg = null;
		
		$set = $ilDB->query("SELECT * FROM rep_robj_xscl_reg ".
			" WHERE pkg_id = ".$ilDB->quote($pkgIdPK, "integer")." AND usr_id = ".$ilDB->quote($userIdPK, "integer")
			);
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$reg = new ilObjScormCloudReg();
			$reg->setPkgId($pkgIdPK);
			$reg->setUserId($rec["usr_id"]);
			$reg->setCompletion($rec["completion"]);
			$reg->setSatisfaction($rec["satisfaction"]);
			$reg->setScore($rec["score"]);
			$reg->setTotalTime($rec["total_time"]);
			$reg->setExistsOnCloud($rec["exists_on_cloud"]);
			$reg->setAttemptCount($rec["attempt_cnt"]);
			$reg->setVersion($rec["version"]);
			$reg->setLastAccess($rec["last_access"]);
			
			if (!$reg->getExistsOnCloud()) {
				if ($reg->isRegistrationCreatedInScormCloud()) {
					$reg->setExistsOnCloud(true);
					$reg->doUpdate();
				}
			}
		}
		

		
		return $reg;
	}
	
	public static function getRegistrationsForPackageId($pkgIdPK) 
	{
		
		global $ilDB;
		$regs = array();
		
		$set = $ilDB->query("SELECT * FROM rep_robj_xscl_reg ".
			" WHERE pkg_id = ".$ilDB->quote($pkgIdPK, "integer")
			);
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$reg = new ilObjScormCloudReg();
			$reg->setPkgId($pkgIdPK);
			$reg->setUserId($rec["usr_id"]);
			$reg->setCompletion($rec["completion"]);
			$reg->setSatisfaction($rec["satisfaction"]);
			$reg->setScore($rec["score"]);
			$reg->setTotalTime($rec["total_time"]);
			$reg->setExistsOnCloud($rec["exists_on_cloud"]);
			$reg->setAttemptCount($rec["attempt_cnt"]);
			$reg->setVersion($rec["version"]);
			$reg->setLastAccess($rec["last_access"]);
			
			$regs[] = $reg;
		}
		
		return $regs;
	}
	

	/**
	* Create object
	*/
	function doCreate()
	{
		global $ilDB;
		
		$ilDB->manipulate("INSERT INTO rep_robj_xscl_reg ".
			"(pkg_id, usr_id) VALUES (".
			$ilDB->quote($this->getPkgId(), "integer").",".
			$ilDB->quote($this->getUserId(), "integer").")"
		);
	}
	
	/**
	* Update data
	*/
	function doUpdate()
	{
		global $ilDB;
		
		$ilDB->manipulate($up = "UPDATE rep_robj_xscl_reg SET ".
			" completion = ".$ilDB->quote($this->getCompletion(), "text").",".
			" satisfaction = ".$ilDB->quote($this->getSatisfaction(), "text").",".
			" score = ".$ilDB->quote($this->getScore(), "integer").",".
			" total_time = ".$ilDB->quote($this->getTotalTime(), "integer").",".
			" version = ".$ilDB->quote($this->getVersion(), "integer").",".
			" attempt_cnt = ".$ilDB->quote($this->getAttemptCount(), "integer").",".
			" last_access = ".$ilDB->quote($this->getLastAccess(), "timestamp").",".
			" exists_on_cloud = ".$ilDB->quote($this->getExistsOnCloud(), "integer").
			" WHERE pkg_id = ".$ilDB->quote($this->getPkgId(), "integer")." AND usr_id = ".$ilDB->quote($this->getUserId(), "integer")
			);
	}
	
	/**
	* Update data
	*/
	function doUpdateExistsOnCloud()
	{
		global $ilDB;
		
		$ilDB->manipulate($up = "UPDATE rep_robj_xscl_reg SET ".
			" exists_on_cloud = ".$ilDB->quote($this->getExistsOnCloud(), "integer").
			" WHERE pkg_id = ".$ilDB->quote($this->getPkgId(), "integer")." AND usr_id = ".$ilDB->quote($this->getUserId(), "integer")
			);
	}
	
	/**
	* Delete data from db
	*/
	function doDelete()
	{
		global $ilDB;
		
		$ilDB->manipulate("DELETE FROM rep_robj_xscl_reg WHERE ".
			" pkg_id = ".$ilDB->quote($this->getPkgId(), "integer")." AND usr_id = ".$ilDB->quote($this->getUserId(), "integer")
			);
		
	}
	
	
	function isRegistrationCreatedInScormCloud()
	{
		global $ScormCloudService;
		
		$registrationService = $ScormCloudService->getRegistrationService();
		$regArray = $registrationService->GetRegistrationList($this->getPK(), $this->getPkgId());

		return count($regArray) > 0;
	}
	

	function getPK()
	{
		return $this->pkgId."-".$this->userId;
	}
	
	//
	// Set/Get Methods for our example properties
	//
	/**
	* Set pkgId
	*
	* @param	boolean		pkgId
	*/
	function setPkgId($a_val)
	{
		$this->pkgId = $a_val;
	}

	/**
	* Get pkgId
	*
	* @return	boolean		pkgId
	*/
	function getPkgId()
	{
		return $this->pkgId;
	}

	/**
	* Set userId
	*
	* @param	boolean		userId
	*/
	function setUserId($a_val)
	{
		$this->userId = $a_val;
	}

	/**
	* Get userId
	*
	* @return	boolean		userId
	*/
	function getUserId()
	{
		return $this->userId;
	}

	/**
	* Set completion
	*
	* @param	boolean		completion
	*/
	function setCompletion($a_val)
	{
		$this->completion = $a_val;
	}
	
	/**
	* Get completion
	*
	* @return	boolean		completion
	*/
	function getCompletion()
	{
		return $this->completion;
	}
	
	/**
	* Set satisfaction
	*
	* @param	boolean		satisfaction
	*/
	function setSatisfaction($a_val)
	{
		$this->satisfaction = $a_val;
	}
	
	/**
	* Get satisfaction
	*
	* @return	boolean		satisfaction
	*/
	function getSatisfaction()
	{
		return $this->satisfaction;
	}

	/**
	* Set score
	*
	* @param	boolean		score
	*/
	function setScore($a_val)
	{
		$this->score = $a_val;
	}
	
	/**
	* Get score
	*
	* @return	boolean		score
	*/
	function getScore()
	{
		return $this->score;
	}
	
	/**
	* Set totalTime
	*
	* @param	boolean		totalTime
	*/
	function setTotalTime($a_val)
	{
		$this->totalTime = $a_val;
	}
	
	/**
	* Get totalTime
	*
	* @return	boolean		totalTime
	*/
	function getTotalTime()
	{
		return $this->totalTime;
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
	
	/**
	* Set Version
	*
	* @param	boolean		online
	*/
	function setVersion($a_val)
	{
		$this->version = $a_val;
	}
	
	/**
	* Get version
	*
	* @return	boolean		online
	*/
	function getVersion()
	{	
		return $this->version;
	}
	
	/**
	* Set lastAccess
	*
	* @param	boolean		online
	*/
	function setLastAccess($a_val)
	{
		$this->lastAccess = $a_val;
	}
	
	/**
	* Get lastAccess
	*
	* @return	boolean		online
	*/
	function getLastAccess()
	{	
		return $this->lastAccess;
	}
	
	/**
	* Set attemptCount
	*
	* @param	boolean		online
	*/
	function setAttemptCount($a_val)
	{
		$this->attemptCount = $a_val;
	}
	
	/**
	* Get attemptCount
	*
	* @return	boolean		online
	*/
	function getAttemptCount()
	{	
		return $this->attemptCount;
	}
}
?>
