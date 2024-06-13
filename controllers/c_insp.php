<?php
if($action === "ajax_get_insp_list") {
  $wsdate = date("d-m-Y");
  if(isset($_POST["wsdate"])) {
    $wsdate = $_POST["wsdate"];
  }
  $inspection = new Inspection();
  $result = $inspection->getList($wsdate);
  echo json_encode($result);
}

if($action == "ajax_get_wm") {
	$budat = $_REQUEST["budat"];
	if(!empty($budat)) {
		$arr_wsdate = explode("-", $budat);
		$budat = $arr_wsdate[2].$arr_wsdate[1].$arr_wsdate[0];
	} else {
		$budat = date("Ymd");
	}
  $inspection = new Inspection();
  $result = $inspection->getWeighment($budat);
  echo json_encode($result);
}

if($action == "ajax_get_wb") {
  $wsnum = $_POST["wsnum"];
  $inspection = new Inspection();
  $result = $inspection->get_zmm_wb_hdr('INDO', $wsnum);
  echo json_encode($result);
}

if($action == "ajax_get_loc") {
  $inspection = new Inspection();
  $result = $inspection->getLocation();
  echo json_encode($result);
}

if($action == "ajax_upload_photo") {
  $type = $_GET["type"];
  $wsnum = $_GET["wsnum"];
  $new_image_name = urldecode($_FILES["file"]["name"]);
  if($type == "P") {
    $image_path = "media/inspection/".$wsnum."/photo";
  } else if($type == "D") {
    $image_path = "media/inspection/".$wsnum."/document";
  }
  
  if (!file_exists($image_path)) {
    mkdir($image_path, 0777, true);
  }
  $target_file = $image_path."/".$new_image_name;
  if (file_exists($target_file)) {
    unlink($target_file);
  }
  move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
}

if($action == "ajax_save_insp") {
  $header = array();
  /*foreach($_POST as $key => $value) {
    if($value == null) {
      $_POST[$key] = " ";
    }
  }*/
	$header['SRLNO'] = str_pad($_POST['SRLNO'], 10, '0', STR_PAD_LEFT);
  $header['WSNUM'] = $_POST['WSNUM'];
  $header['VEHICLE_NO'] = $_POST['VEHICLE_NO'];
  $header['WEIGHT_F'] = $_POST['WEIGHT_F'];
  $header['CONT_MRK'] = !empty($_POST['CONT_MRK']) ? $_POST['CONT_MRK'] : " ";
  $header['CONT_NO'] = !empty($_POST['CONT_NO']) ? $_POST['CONT_NO'] : " ";
  $header['CONT_SLD'] = !empty($_POST['CONT_SLD']) ? $_POST['CONT_SLD'] : " ";
  $header['SLD_DOC'] = !empty($_POST['SLD_DOC']) ? $_POST['SLD_DOC'] : " ";
  $header['CONT_FULL'] = !empty($_POST['CONT_FULL']) ? $_POST['CONT_FULL'] : " ";
  $header['CONT_WIDTH'] = !empty($_POST['CONT_WIDTH']) ? $_POST['CONT_WIDTH'] : 0;
  $header['CONT_HEIGHT'] = !empty($_POST['CONT_HEIGHT']) ? $_POST['CONT_HEIGHT'] : 0;
  $header['CONT_LENGTH'] = !empty($_POST['CONT_LENGTH']) ? $_POST['CONT_LENGTH'] : 0;
  $header['EIR_MRK'] = !empty($_POST['EIR_MRK']) ? $_POST['EIR_MRK'] : " ";
  $header['EIR_DOC'] = !empty($_POST['EIR_DOC']) ? $_POST['EIR_DOC'] : " ";
  $header['WEIGHT_E'] = $_POST['WEIGHT_E'];
  $header['DENS_E'] = !empty($_POST['DENS_E']) ? $_POST['DENS_E'] : 0;
  $header['NOTE'] = !empty($_POST['NOTE']) ? $_POST['NOTE'] : " ";
  $header['DED_IND'] = "P";
  if(isset($_POST['DED_IND'])) {
    $header['DED_IND'] = $_POST['DED_IND'];
  }
  $header['DED_KG'] = !empty($_POST['DED_KG']) ? $_POST['DED_KG'] : 0;
  $header['DED_PR'] = !empty($_POST['DED_PR']) ? $_POST['DED_PR'] : 0;
  $header['DED_RMK'] = !empty($_POST['DED_RMK']) ? $_POST['DED_RMK'] : " ";
  $header['REJ_MRK'] = !empty($_POST['REJ_MRK']) ? $_POST['REJ_MRK'] : " ";
  if(!empty($_POST['REJ_FULL'])) {
    //Full Reject
    $header['REJ_MRK'] = "F";
  }
  $header['REJ_RMK'] = !empty($_POST['REJ_RMK']) ? $_POST['REJ_RMK'] : " ";
  $header['ORIGIN'] = !empty($_POST['ORIGIN']) ? $_POST['ORIGIN'] : " ";
  $header['SHORT_SIZE_IND'] = !empty($_POST['SHORT_SIZE_IND']) ? $_POST['SHORT_SIZE_IND'] : " ";
  $header['PREM_IND'] = !empty($_POST['PREM_IND']) ? $_POST['PREM_IND'] : " ";
  $header['PENALTY'] = !empty($_POST['PENALTY']) ? $_POST['PENALTY'] : 0;
  $header['PENALTY_RMK'] = !empty($_POST['PENALTY_RMK']) ? $_POST['PENALTY_RMK'] : " ";
  $header['SUR_IND'] = !empty($_POST['SUR_IND']) ? $_POST['SUR_IND'] : " ";
  $header['SUR_RMK'] = !empty($_POST['SUR_RMK']) ? $_POST['SUR_RMK'] : " ";
  $date = explode("-", $_POST['CRT_DT']);
  $header['CRT_DT'] = $date[2].$date[1].$date[0];
  $time = explode(":",$_POST['CRT_TM']);
  $header['CRT_TM'] = "00".$time[0].$time[1]."00";
  $user = new User();
  $name = $user->getName($_POST['CRT_BY']);
  if($name == false) {
    $header['CRT_BY'] = strtoupper($_POST['CRT_BY']);
  } else {
    $header['CRT_BY'] = $name;
  }
  $header['DEVICE_ID'] = !empty($_POST['DEVICE_ID']) ? $_POST['DEVICE_ID'] : " ";
  
  $item = array();
  
  $item[0]["class"] = "Z001"; 
  $item[0]["short"] = $_POST['Z001A'] == "NaN" ? 0 : $_POST['Z001A']; 
  $item[0]["long"] =  0; 
  $item[0]["remark"] = !empty($_POST['Z001R']) ? $_POST['Z001R'] : " ";
  
  $item[1]["class"] = "Z002"; 
  $item[1]["short"] = $_POST['Z002A'] == "NaN" ? 0 : $_POST['Z002A']; 
  $item[1]["long"] =  0; 
  $item[1]["remark"] = !empty($_POST['Z002R']) ? $_POST['Z002R'] : " ";
  
  $item[2]["class"] = "Z003"; 
  $item[2]["short"] = $_POST['Z003A'] == "NaN" ? 0 : $_POST['Z003A']; 
  $item[2]["long"] = $_POST['Z003B'] == "NaN" ? 0 : $_POST['Z003B']; 
  $item[2]["remark"] = !empty($_POST['Z003R']) ? $_POST['Z003R'] : " ";
  
  $item[3]["class"] = "Z004"; 
  $item[3]["short"] = $_POST['Z004A'] == "NaN" ? 0 : $_POST['Z004A']; 
  $item[3]["long"] = $_POST['Z004B'] == "NaN" ? 0 : $_POST['Z004B']; 
  $item[3]["remark"] = !empty($_POST['Z004R']) ? $_POST['Z004R'] : " ";
  
  $item[4]["class"] = "Z005"; 
  $item[4]["short"] = $_POST['Z005A'] == "NaN" ? 0 : $_POST['Z005A']; 
  $item[4]["long"] = $_POST['Z005B'] == "NaN" ? 0 : $_POST['Z005B']; 
  $item[4]["remark"] = !empty($_POST['Z005R']) ? $_POST['Z005R'] : " ";
  
  $item[5]["class"] = "Z006"; 
  $item[5]["short"] = $_POST['Z006A'] == "NaN" ? 0 : $_POST['Z006A']; 
  $item[5]["long"] = $_POST['Z006B'] == "NaN" ? 0 : $_POST['Z006B']; 
  $item[5]["remark"] = !empty($_POST['Z006R']) ? $_POST['Z006R'] : " ";
  
  $item[6]["class"] = "Z007"; 
  $item[6]["short"] = $_POST['Z007A'] == "NaN" ? 0 : $_POST['Z007A']; 
  $item[6]["long"] =  0; 
  $item[6]["remark"] = !empty($_POST['Z007R']) ? $_POST['Z007R'] : " ";
  
  $item[7]["class"] = "Z008"; 
  $item[7]["short"] = $_POST['Z008A'] == "NaN" ? 0 : $_POST['Z008A']; 
  $item[7]["long"] =  0; 
  $item[7]["remark"] = !empty($_POST['Z008R']) ? $_POST['Z008R'] : " ";
  
  $item[8]["class"] = "Z009"; 
  $item[8]["short"] = $_POST['Z009A'] == "NaN" ? 0 : $_POST['Z009A']; 
  $item[8]["long"] = $_POST['Z009B'] == "NaN" ? 0 : $_POST['Z009B']; 
  $item[8]["remark"] = !empty($_POST['Z009R']) ? $_POST['Z009R'] : " ";
  
  $item[9]["class"] = "Z010"; 
  $item[9]["short"] = $_POST['Z010A'] == "NaN" ? 0 : $_POST['Z010A']; 
  $item[9]["long"] = 0;
  $item[9]["remark"] = !empty($_POST['Z010R']) ? $_POST['Z010R'] : " ";
  
  $item[10]["class"] = "Z011"; 
  $item[10]["short"] = $_POST['Z011A'] == "NaN" ? 0 : $_POST['Z011A']; 
  $item[10]["long"] = 0;
  $item[10]["remark"] = !empty($_POST['Z011R']) ? $_POST['Z011R'] : " ";
	
	$item[11]["class"] = "Z012"; 
  $item[11]["short"] = $_POST['Z012A'] == "NaN" ? 0 : $_POST['Z012A']; 
  $item[11]["long"] = $_POST['Z012B'] == "NaN" ? 0 : $_POST['Z012B']; 
  $item[11]["remark"] = !empty($_POST['Z012R']) ? $_POST['Z012R'] : " ";
	
	$item[12]["class"] = "Z013"; 
  $item[12]["short"] = $_POST['Z013A'] == "NaN" ? 0 : $_POST['Z013A']; 
  $item[12]["long"] = $_POST['Z013B'] == "NaN" ? 0 : $_POST['Z013B']; 
  $item[12]["remark"] = !empty($_POST['Z013R']) ? $_POST['Z013R'] : " ";
	
	$result = array();
	
	//check device information
	if(!empty($_POST['DEVICE_ID'])) {
		$inspection = new Inspection();
		$result = $inspection->saveData($header, $item);
		$email = new SendMail();
		$recipient = array();
		$recipient[0] = "dheo.pratama@mittalsteel.com";
		$recipient[1] = "Tutuk.w@mittalsteel.com";
		$recipient[2] = "mikail@mittalsteel.com";
		$recipient[3] = "rao.vv@mittalsteel.com";
		$recipient[4] = "Abhijit.Das@mittalsteel.com";
		$recipient[5] = "anil.malusare@mittalsteel.com";
		$recipient[6] = "Kaustav.Gupta@mittalsteel.com";
		$subject = "Inspection number ".$header['WSNUM']." has been saved";
		$body = "<html>"
						. "<head>"
						. "<style>"
						. "body {font-family: 'Calibri', arial, sans-serif;}"
						. "pre {font-family: 'Calibri', arial, sans-serif;}"
						. "</head>"
						. "<body>";
		$body .= "<p>Inspection number ".$header['WSNUM']." has been saved<br>"
						. "Please check and/or revise if there any error and/or change(s).</p>"
						. "<p>To view detail please please click this <a href='http://intranet.ispatindo.com/intranet/index.php?action=scrap_inspection&wsnum=".$header['WSNUM']."'>link</a> <br>"
						. "or visit http://intranet.ispatindo.com/intranet/index.php?action=scrap_inspection&wsnum=".$header['WSNUM']."</p>"
						. "<p><i>This email is automatically generated by system, please do not reply.</i></p>";
		$body .= "</body>"
					. "</html>";
		$email->sendTheMail($subject, $body, $recipient);
	} else {
		$result["status"] = false;
		$result["message"] = "Hardware Information Empty, Please update yout App!!!";
	}
	
  echo json_encode($result);  
		
}

if($action == "ajax_get_insp") {
  $wsnum = $_REQUEST["wsnum"];
  $inspection = new Inspection();
  $data = $inspection->getById($wsnum);
	//var_dump($data);
  echo json_encode($data);
}

if($action == "ajax_is_exist_insp") {
  $wsnum = $_POST["wsnum"];
  $inspection = new Inspection();
  $data = $inspection->isExist($wsnum);
  echo json_encode($data);
}

if($action == "test_mail") {
  $header['WSNUM'] = "XXXX_test_XXXX";
  $email = new SendMail();
  $recipient = array();
  $recipient[0] = "dheo.pratama@mittalsteel.com";
  $recipient[1] = "Tutuk.w@mittalsteel.com";
  $recipient[2] = "mikail@mittalsteel.com";
  $subject = "Inspection number ".$header['WSNUM']." has been saved";
  $body = "<html>"
          . "<head>"
          . "<style>"
          . "body {font-family: 'Calibri', arial, sans-serif;}"
          . "pre {font-family: 'Calibri', arial, sans-serif;}"
          . "</head>"
          . "<body>";
  $body .= "<p>Inspection number ".$header['WSNUM']." has been saved</p>"
          ."<p>To view detail please please click this <a href='http://intranet.ispatindo.com/intranet/index.php?action=scrap_inspection&wsnum=".$header['WSNUM']."'>link</a> <br>"
          . "or visit http://intranet.ispatindo.com/intranet/index.php?action=scrap_inspection&wsnum=".$header['WSNUM']."</p>";
  $body .= "</body>"
        . "</html>";
  $email->sendTheMail($subject, $body, $recipient);
}

if($action == "check_edit_role") {
	$user = new User();
	$userid = $_REQUEST["userid"];
	echo $user->checkUserRole($userid, "10");
}
?>