<?php
 
 /*
==============================================================================
	
	Copyright (c) 2009 Rustici Software
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

==============================================================================
*/
 
$language_file = array ('registration', 'index', 'tracking', 'exercice', 'scorm', 'learnpath');
$language_file[] = 'scorm_cloud';
$cidReset = true;
include ('../inc/global.inc.php');
include_once(api_get_path(LIBRARY_PATH).'course.lib.php');
include_once(api_get_path(LIBRARY_PATH).'usermanager.lib.php');
require_once ('scorm_cloud.lib.php');

$this_section = "session_my_space";

$export_csv = isset($_GET['export']) && $_GET['export'] == 'csv' ? true : false;
if($export_csv)
{
	ob_start();
}
$csv_content = array();


$user_id = intval($_GET['student_id']);

if (isset($_GET['course'])) {
	$cidReq = Security::remove_XSS($_GET['course']);
}

$user_infos = UserManager :: get_user_info_by_id($user_id);
$name = $user_infos['firstname'].' '.$user_infos['lastname'];

if(!api_is_platform_admin(true) && !CourseManager :: is_course_teacher($_user['user_id'], $cidReq) && !Tracking :: is_allowed_to_coach_student($_user['user_id'],$_GET['student_id']) && $user_infos['hr_dept_id']!==$_user['user_id']) {
	Display::display_header('');
	api_not_allowed();
	Display::display_footer();
}

$course_exits = CourseManager::course_exists($cidReq);

if (!empty($course_exits)) {
	$_course = CourseManager :: get_course_information($cidReq);
} else {
	api_not_allowed();
}

$_course['dbNameGlu'] = $_configuration['table_prefix'] . $_course['db_name'] . $_configuration['db_glue'];

$lp_id = intval($_GET['lp_id']);
$lp_view_id = cloud_getLpViewId($cidReq,$lp_id,$user_id);
$regid = cloud_getRegId($cidReq,$lp_view_id);

$interbreadcrumb[] = array ("url" => api_get_path(WEB_COURSE_PATH).$_course['directory'], 'name' => $_course['title']);
$interbreadcrumb[] = array ("url" => "../tracking/courseLog.php?cidReq=".$cidReq.'&studentlist=true&id_session='.$_SESSION['id_session'], "name" => get_lang("Tracking"));
$interbreadcrumb[] = array("url" => "../mySpace/myStudents.php?student=".Security::remove_XSS($_GET['student_id'])."&course=".$cidReq."&details=true&origin=".Security::remove_XSS($_GET['origin']) , "name" => get_lang("DetailsStudentInCourse"));
$interbreadcrumb[] = array("url" => "cloudCourseDetails.php?regid=".$regid."&course=".$cidReq."&lp_id=".$lp_id."&student_id=".$user_id , "name" => "Course Activity Report");
$nameTools = get_lang('launchHistoryReport');

$htmlHeadXtra[] = '
<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

    });
    </script>
<script type="text/javascript">
        var extConfigurationString = "";
    </script>
<link rel="Stylesheet" href="LaunchHistoryControlResources/styles/LaunchHistoryReport.css"  type="text/css"/>
<script type="text/javascript" src="LaunchHistoryControlResources/scripts/LaunchHistoryReport.js"></script>
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

Display :: display_header($nameTools);



$sql = 'SELECT name 
		FROM '.Database::get_course_table(TABLE_LP_MAIN, $_course['db_name']).'
		WHERE id='.Database::escape_string($lp_id);
$rs = api_sql_query($sql, __FILE__, __LINE__);
$lp_title = Database::result($rs, 0, 0);	

echo '<div class ="actions"><div align="left" style="float:left;margin-top:2px;" ><strong>'.$_course['title'].' - '.$lp_title.' - '.$name.'</strong></div>';
echo	'<div class="clear"></div></div>';







echo "<div class='activityReportHeader'>".get_lang('launchHistoryReport');
echo '<div class="launchHistoryLink"><a href="cloudCourseDetails.php?regid='.$regid.'&course='.$cidReq.'&lp_id='.$lp_id.'&student_id='.$user_id.'"><img src="img/2leftarrow.gif"/>'.get_lang('cloudCourseDetails').'</a></div></div>';


$ScormService = cloud_getScormEngineService();
$regService = $ScormService->getRegistrationService();

$resultArray = $regService->GetLaunchHistory($regid);
//echo var_dump($resultArray).'<br/>';


echo '<div id="historyInfo">';
	echo "<table>";
	echo "<tr><td class='launch_headerName' colspan='2'>Launch Instances</td>";
	echo "<td class='launch_time'>Launch Time</td>";
	echo "<td class='launch_duration'>Duration</td></tr></table>";
	echo '<div id="historyDetails" class="history_details" runat="server">';

echo "<div class='launch_list'>";
$idx = 1;
foreach($resultArray as $result)
{
$lid = 	$result->getId();

echo "<div class='LaunchPlaceHolder' id='launch_".$result->getId()."' regid='".$regid."'>";

echo "<div class='hide_show_div' >";
	echo "<table>";
	echo "<tr><td class='launch_listPrefix'>+</td>";
	echo "<td class='launch_index'>".$idx.".</td>";
	echo "<td class='launch_time'>".cloud_formatHistoryTime($result->getLaunchTime())."</td>";
	echo "<td class='launch_duration'><script>document.write(fmtDuration(".(cloud_convertTimeToInt($result->getExitTime()) - cloud_convertTimeToInt($result->getLaunchTime()))* 1000 ."))</script></td>";
	echo "</tr></table>";
echo "</div>";

echo "<div class='launch_activity_list'><div id='receiver' class='div_receiver'></div></div>";
echo "</div>";

$idx++;
}
echo "  </div>";
echo '</div></div>';

//echo $regService->GetLaunchInfo($lid);
	


Display :: display_footer();

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







?>
