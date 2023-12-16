<?php
/* Credit to W. S. Toh https://code-boxx.com/simple-drag-and-drop-file-upload/ */
/* MIT License */

require("ismodule.php");
require("modules/$modfolder/functions_lib.php");
require("modules/$modfolder/include_lconfig.php");
$do = $_GET['do'];
if ($do == "upload")
{
	if ($xrf_server_name == "SandstormServer")
		$coverfolder = "/var/covers/";
	else
		$coverfolder = "../books/covers/";
	
	// SOURCE + DESTINATION
	$source = $_FILES["file-upload"]["tmp_name"];
	$destination = $coverfolder . $_FILES["file-upload"]["name"];
	$error = "";
	
	if (isset($_POST["overwrite"])) {
	$overwrite = 1; } else { $overwrite = 0; }

	// CHECK IF FILE ALREADY EXIST
	if ($overwrite == 0 && file_exists($destination)) {
	  $error = $destination . " already exist.";
	}

	// ALLOWED FILE EXTENSIONS
	if ($error == "") {
	  $allowed = ["png"]; // All covers should be PNGs
	  $ext = strtolower(pathinfo($_FILES["file-upload"]["name"], PATHINFO_EXTENSION));
	  if (!in_array($ext, $allowed)) {
		$error = "$ext file type not allowed - " . $_FILES["file-upload"]["name"];
	  }
	}

	// LEGIT IMAGE FILE CHECK
	if ($error == "") {
	  if (getimagesize($_FILES["file-upload"]["tmp_name"]) == false) {
		$error = $_FILES["file-upload"]["name"] . " is not a valid image file.";
	  }
	}

	// FILE SIZE CHECK
	if ($error == "") {
	  // 1,000,000 = 1MB
	  if ($_FILES["file-upload"]["size"] > 250000) { // 244 KB should be enough
		$error = $_FILES["file-upload"]["name"] . " - file size too big!";
	  }
	}

	// ALL CHECKS OK - MOVE FILE
	if ($error == "") {
	  if (!move_uploaded_file($source, $destination)) {
		$error = "Error moving $source to $destination";
	  }
	}

	// ERROR OCCURED OR OK?
	if ($error == "") {
	  echo $_FILES["file-upload"]["name"] . " upload OK";
	} else {
	  echo $error;
	}
}
else
{
	echo "<style>
      #uploader {
        width: 300px; 
        height: 200px; 
        background: #222222;
        padding: 10px;
      }
      #uploader.highlight {
        background:#555555;
      }
      #uploader.disabled {
        background:#aaa;
      }
    </style>
    <script src=\"acp_module_api.php?modfolder=$modfolder&modpanel=uploadcovers.js\"></script>
	
	<b>Upload Covers</b><p>
	
	<!-- DROP ZONE -->
    <div id=\"uploader\">
      <p>Drop Files Here</p>
	  <p>Maximum Size: 244 KB</p>
	  <p>Maximum Dimensions:<br>300px by 300px</p>
    </div>

    <!-- STATUS -->
    <div id=\"upstat\"></div>

    <!-- FALLBACK -->
    <form action=\"acp_module_panel.php?modfolder=$modfolder&modpanel=uploadcovers&do=upload\" method=\"post\" enctype=\"multipart/form-data\">
      <input type=\"file\" name=\"file-upload\" id=\"file-upload\" accept=\"image/*\">
      <input type=\"submit\" value=\"Upload File\" name=\"submit\">
	  <p>
	  <input type=\"checkbox\" name=\"overwrite\" id=\"overwrite\"> Overwrite Existing Files
    </form>";
}
?>