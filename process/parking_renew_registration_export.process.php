<?php

include_once("../classes/include/dbop.class.php");
include_once("../classes/view_member_profile.class.php");

$m_dbConn = new dbop();

$mem_obj = new view_member_profile($m_dbConn);


$mem_obj->exportMemberVehicleReport();







?>