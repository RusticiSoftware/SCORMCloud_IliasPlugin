<#1>
<?php

$pgkFields = array(
	'id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'is_online' => array(
		'type' => 'integer',
		'length' => 1,
		'notnull' => false
	),
	'exists_on_cloud' => array(
		'type' => 'integer',
		'length' => 1,
		'notnull' => false
	),
	'version' => array(
		'type' => 'integer',
		'length' => 2,
		'notnull' => false
	)
);

$ilDB->createTable("rep_robj_xscl_pkg", $pgkFields);
$ilDB->addPrimaryKey("rep_robj_xscl_pkg", array("id"));

$regFields = array(
	'pkg_id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'usr_id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'version' => array(
		'type' => 'integer',
		'length' => 2,
		'notnull' => false
	),
	'completion' => array(
		'type' => 'text',
		'length' => 10,
		'notnull' => false
	),
	'satisfaction' => array(
		'type' => 'text',
		'length' => 10,
		'notnull' => false
	),
	'score' => array(
		'type' => 'decimal'
	),
	'total_time' => array(
		'type' => 'integer',
		'length' => 8,
		'notnull' => false
	),
	'last_access' => array(
		'type' => 'timestamp',
		'notnull' => false
	),
	'attempt_cnt' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => false
	),
	'exists_on_cloud' => array(
		'type' => 'integer',
		'length' => 1,
		'notnull' => false
	)
);

$ilDB->createTable("rep_robj_xscl_reg", $regFields);
$ilDB->addPrimaryKey("rep_robj_xscl_reg", array("pkg_id, usr_id"));

?>
