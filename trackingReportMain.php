<?php

include_once("classes/class.ilObjectScormCloudReg.php");

	$packageId = $_GET['pkgId'];

	$reg = new ilObjScormCloudReg();
	
	$regs = $reg->GetRegistrationsForPackageId($pkgId);
	
	echo count($regs);
?>