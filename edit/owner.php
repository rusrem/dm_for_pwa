<?php
// owner.php
// 
// Domain Manager - A web-based application written in PHP & MySQL used to manage a collection of domain names.
// Copyright (C) 2010 Greg Chetcuti
// 
// Domain Manager is free software; you can redistribute it and/or modify it under the terms of the GNU General
// Public License as published by the Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
// 
// Domain Manager is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
// implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
// for more details.
// 
// You should have received a copy of the GNU General Public License along with Domain Manager. If not, please 
// see http://www.gnu.org/licenses/
?>
<?php
session_start();

include("../_includes/config.inc.php");
include("../_includes/database.inc.php");
include("../_includes/software.inc.php");
include("../_includes/auth/auth-check.inc.php");
include("../_includes/timestamps/current-timestamp.inc.php");

$page_title = "Editting An Owner";
$software_section = "owners";

$oid = $_GET['oid'];

// Form Variables
$new_owner = $_POST['new_owner'];
$new_notes = $_POST['new_notes'];
$new_oid = $_POST['new_oid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($new_owner != "") {

		$sql = "UPDATE owner
				SET name = '" . mysql_real_escape_string($new_owner) . "',
					notes = '" . mysql_real_escape_string($new_notes) . "',
					update_time = '$current_timestamp'
				WHERE id = '$new_oid'";
		$result = mysql_query($sql,$connection) or die(mysql_error());
		
		$new_owner = $new_owner;

		$oid = $new_oid;
		
		$_SESSION['session_result_message'] = "Owner Updated<BR>";

	} else {
	
		$_SESSION['session_result_message'] = "Please Enter The Owner's Name<BR>";

	}

} else {

	$sql = "SELECT name, notes
			FROM owner
			WHERE id = '$oid'";
	$result = mysql_query($sql,$connection);
	
	while ($row = mysql_fetch_object($result)) { 
	
		$new_owner = $row->name;
		$new_notes = $row->notes;
	
	}

}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=$software_title?> :: <?=$page_title?></title>
<?php include("../_includes/head-tags.inc.php"); ?>
</head>
<body>
<?php include("../_includes/header.inc.php"); ?>
<form name="edit_owner_form" method="post" action="<?=$PHP_SELF?>">
<strong>Owner Name:</strong><BR><BR>
<input name="new_owner" type="text" value="<?php if ($new_owner != "") echo $new_owner; ?>
" size="50" maxlength="255">
<BR><BR>
<strong>Notes:</strong><BR><BR>
<textarea name="new_notes" cols="60" rows="5"><?=$new_notes?></textarea>
<BR><BR><BR>
<input type="hidden" name="new_oid" value="<?=$oid?>">
<input type="submit" name="button" value="Update This Owner &raquo;">
</form>
<?php include("../_includes/footer.inc.php"); ?>
</body>
</html>