<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Inspection
 *
 * @author dheo
 */
class Inspection {
  //put your code here
  public function getList($wsdate) {
    if(empty($wsdate)) {
      $wsdate = date("d-m-Y");
    }
    
    $arr_wsdate = explode("-", $wsdate);
    $crtdt = $arr_wsdate[2].$arr_wsdate[1].$arr_wsdate[0];
    
    $return = array();
    $conn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD);
    $sql = "select rownum as rn, t.* from ( select a.*, b.name1 "
            . "from sapsr3.ZTMM_SCRP_INS_HD@dbsap_iip a "
            . "INNER JOIN sapsr3.zmm_wb_hdr@dbsap_iip b on b.wsnum = a.wsnum "
            . "where a.mandt = '600' AND a.crt_dt = :crtdt ORDER BY a.wsnum desc ) t";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":crtdt", $crtdt, PDO::PARAM_STR);
    if($stmt->execute()) {
      $data = array();
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $crt_yr = substr($row["CRT_DT"], 0, 4);
        $crt_mo = substr($row["CRT_DT"], 4, 2);
        $crt_dy = substr($row["CRT_DT"], 6, 2);
        $row["CRT_DT"] = $crt_dy.".".$crt_mo.".".$crt_yr;
				$row["SRLNO"] = ltrim($row["SRLNO"], "0");
        $data[] = $row;
      }
      $return["data"] = $data;
      $return["status"] = true;
    } else {
      $return["status"] = false;
      $error = $stmt->errorInfo();
      $return["message"] = trim(str_replace("\n", " ", $error[2]));
    }
    $stmt = null;
    $conn = null;
    return $return;
  }
  
  public function getWeighment($budat = null) {
		if($budat == null) {
			$budat = date("Ymd");
		}    
    $return = array();
    $conn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD);
    $sql = "SELECT rownum as rn, a.* from (select a.LIFNR, a.NAME1, a.TRTYP, a.VEHICLE_NO, a.EBELN, a.EBELP, a.BUDAT, a.WSNUM, a.WTMNT_TIME_F, b.wsnum as xnum, b.srlno  
            FROM  sapsr3.ZMM_WB_HDR@dbsap_iip a 
            LEFT JOIN sapsr3.ZTMM_SCRP_INS_HD@dbsap_iip b on b.wsnum = a.wsnum
            WHERE a.mandt = '600' AND a.BUKRS = 'INDO' AND a.TRTYP = 'D' and a.budat = :budat 
            ORDER BY WSNUM ASC) a ";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":budat", $budat, PDO::PARAM_STR);
    if($stmt->execute()) {
      $data = array();
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row["DATE"] = substr($row["BUDAT"], 6, 2)."-".substr($row["BUDAT"], 4, 2)."-".substr($row["BUDAT"], 0, 4);
        $row["TIME"] = substr($row["WTMNT_TIME_F"], 0, 2).":".substr($row["WTMNT_TIME_F"], 2, 2);
				$row["SRLNO"] = ltrim($row["SRLNO"], "0");
        $data[] = $row;
      }
      $return["data"] = $data;
      $return["status"] = true;
    } else {
      $return["status"] = false;
      $error = $stmt->errorInfo();
      $return["message"] = trim(str_replace("\n", " ", $error[2]));
    }
    $stmt = null;
    $conn = null;
    return $return;
  }
  
  public function get_zmm_wb_hdr($bukrs, $wsnum) {
    $return = array();
    $conn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD);
    $sql = "select wsnum, vehicle_no, weight_f from sapsr3.zmm_wb_hdr@dbsap_iip where mandt = '600' AND bukrs = :bukrs AND wsnum = :wsnum";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":bukrs", $bukrs, PDO::PARAM_STR);
    $stmt->bindValue(":wsnum", $wsnum, PDO::PARAM_STR);
    if($stmt->execute()) {
      $data = array();
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {        
        $data = $row;
      }
      $return["data"] = $data;
      $return["status"] = true;
    } else {
      $return["status"] = false;
      $error = $stmt->errorInfo();
      $return["message"] = trim(str_replace("\n", " ", $error[2]));
    }
    $stmt = null;
    $conn = null;
    return $return;
  }
  
  public function getLocation() {    
    $return = array();
    $conn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD);
    $sql = "select * from sapsr3.ZMM_LOCATN_MNTN@dbsap_iip where mandt = '600'";
    $stmt = $conn->prepare($sql);
    if($stmt->execute()) {
      $data = array();
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {        
        $data[] = $row;
      }
      $return["data"] = $data;
      $return["status"] = true;
    } else {
      $return["status"] = false;
      $error = $stmt->errorInfo();
      $return["message"] = trim(str_replace("\n", " ", $error[2]));
    }
    $stmt = null;
    $conn = null;
    return $return;
  }
  
  public function saveData($header = array(), $item = array()) {
    $return = array();
    $conn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD);
		$sql_del = "DELETE FROM sapsr3.ZTMM_SCRP_INS_HD@dbsap_iip WHERE mandt = '".MANDT."' AND bukrs = 'INDO' AND wsnum = '".$header["WSNUM"]."'";
		$conn->exec($sql_del);		
    $sql = "INSERT INTO sapsr3.ZTMM_SCRP_INS_HD@dbsap_iip 
      (MANDT,
      BUKRS,
      WSNUM,
      VEHICLE_NO,
      WEIGHT_F,
      CONT_MRK,
      CONT_NO,
      CONT_SLD,
      SLD_DOC,
      CONT_FULL,
      CONT_WIDTH,
      CONT_HEIGHT,
      CONT_LENGTH,
      MEINS,
      EIR_MRK,
      EIR_DOC,
      WEIGHT_E,
      DENS_E,
      NOTE,
      DED_IND,
      DED_KG,
      DED_PR,
      DED_RMK,
      REJ_MRK,
      REJ_RMK,
      ORIGIN,
      SHORT_SIZE_IND,
      PREM_IND,
      PENALTY,
      PENALTY_RMK,
      SUR_IND,
      SUR_RMK,
      CRT_DT,
      CRT_TM,
      CRT_BY,
			SRLNO,
			DEVICE_ID
			) 
      VALUES 
      ('".MANDT."',
      'INDO',
      :WSNUM,
      :VEHICLE_NO,
      :WEIGHT_F,
      :CONT_MRK,
      :CONT_NO,
      :CONT_SLD,
      :SLD_DOC,
      :CONT_FULL,
      :CONT_WIDTH,
      :CONT_HEIGHT,
      :CONT_LENGTH,
      'KG',
      :EIR_MRK,
      :EIR_DOC,
      :WEIGHT_E,
      :DENS_E,
      :NOTE,
      :DED_IND,
      :DED_KG,
      :DED_PR,
      :DED_RMK,
      :REJ_MRK,
      :REJ_RMK,
      :ORIGIN,
      :SHORT_SIZE_IND,
      :PREM_IND,
      :PENALTY,
      :PENALTY_RMK,
      :SUR_IND,
      :SUR_RMK,
      :CRT_DT,
      :CRT_TM,
      :CRT_BY,
			:SRLNO,
			:DEVICE_ID)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":WSNUM", $header["WSNUM"], PDO::PARAM_STR);
    $stmt->bindValue(":VEHICLE_NO", $header["VEHICLE_NO"], PDO::PARAM_STR);
    $stmt->bindValue(":WEIGHT_F", $header["WEIGHT_F"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_MRK", $header["CONT_MRK"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_NO", $header["CONT_NO"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_SLD", $header["CONT_SLD"], PDO::PARAM_STR);
    $stmt->bindValue(":SLD_DOC", $header["SLD_DOC"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_FULL", $header["CONT_FULL"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_WIDTH", $header["CONT_WIDTH"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_HEIGHT", $header["CONT_HEIGHT"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_LENGTH", $header["CONT_LENGTH"], PDO::PARAM_STR);
    $stmt->bindValue(":EIR_MRK", $header["EIR_MRK"], PDO::PARAM_STR);
    $stmt->bindValue(":EIR_DOC", $header["EIR_DOC"], PDO::PARAM_STR);
    $stmt->bindValue(":WEIGHT_E", $header["WEIGHT_E"], PDO::PARAM_STR);
    $stmt->bindValue(":DENS_E", $header["DENS_E"], PDO::PARAM_STR);
    $stmt->bindValue(":NOTE", $header["NOTE"], PDO::PARAM_STR);
    $stmt->bindValue(":DED_IND", $header["DED_IND"], PDO::PARAM_STR);
    $stmt->bindValue(":DED_KG", $header["DED_KG"], PDO::PARAM_STR);
    $stmt->bindValue(":DED_PR", $header["DED_PR"], PDO::PARAM_STR);
    $stmt->bindValue(":DED_RMK", $header["DED_RMK"], PDO::PARAM_STR);
    $stmt->bindValue(":REJ_MRK", $header["REJ_MRK"], PDO::PARAM_STR);
    $stmt->bindValue(":REJ_RMK", $header["REJ_RMK"], PDO::PARAM_STR);
    $stmt->bindValue(":ORIGIN", $header["ORIGIN"], PDO::PARAM_STR);
    $stmt->bindValue(":SHORT_SIZE_IND", $header["SHORT_SIZE_IND"], PDO::PARAM_STR);
    $stmt->bindValue(":PREM_IND", $header["PREM_IND"], PDO::PARAM_STR);
    $stmt->bindValue(":PENALTY", $header["PENALTY"], PDO::PARAM_STR);
    $stmt->bindValue(":PENALTY_RMK", $header["PENALTY_RMK"], PDO::PARAM_STR);
    $stmt->bindValue(":SUR_IND", $header["SUR_IND"], PDO::PARAM_STR);
    $stmt->bindValue(":SUR_RMK", $header["SUR_RMK"], PDO::PARAM_STR);
    $stmt->bindValue(":CRT_DT", $header["CRT_DT"], PDO::PARAM_STR);
    $stmt->bindValue(":CRT_TM", $header["CRT_TM"], PDO::PARAM_STR);
    $stmt->bindValue(":CRT_BY", $header["CRT_BY"], PDO::PARAM_STR);
		$stmt->bindValue(":SRLNO", $header["SRLNO"], PDO::PARAM_STR);		
		$stmt->bindValue(":DEVICE_ID", $header["DEVICE_ID"], PDO::PARAM_STR);
    if($stmt->execute()) {
      $sql = "DELETE FROM sapsr3.ZTMM_SCRP_INS_IT@dbsap_iip WHERE mandt = '".MANDT."' AND bukrs = 'INDO' AND wsnum = :WSNUM";
      $stmt = $conn->prepare($sql);
      $stmt->bindValue(":WSNUM", $header["WSNUM"], PDO::PARAM_STR);
      if($stmt->execute()) {
        $i = 1;
        foreach ($item as $row){
          $sql = "INSERT into sapsr3.ZTMM_SCRP_INS_IT@dbsap_iip (MANDT,BUKRS,WSNUM,CLASS,SIZE_NORM,SIZE_LONG,REMARK) "
                . "values('".MANDT."','INDO', '".$header["WSNUM"]."', '".$row["class"]."', '".$row["short"]."', '".$row["long"]."', '".$row["remark"]."') ";
          $stmt = $conn->prepare($sql)->execute() or die("Insert error: item ".$row["class"]."; info: ".$stmt->errorInfo()."; SQL: ".$sql);
          $return["status"] = true;
        }
      } else {
        $return["status"] = false;
        $error = $stmt->errorInfo();
        $return["message"] = "Delete Item Error : ".trim(str_replace("\n", " ", $error[2]));
      }
    } else {
      $return["status"] = false;
      $error = $stmt->errorInfo();
      $return["message"] = "Insert Header Error : ".trim(str_replace("\n", " ", $error[2]));
    }
    $stmt = null;
    $conn = null;
    return $return;
  }
	
	public function saveHeader($header = array()) {
    $return = array();
    $conn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD);
    $sql = "INSERT INTO sapsr3.ZTMM_SCRP_INS_HD@dbsap_iip 
      (MANDT,
      BUKRS,
      WSNUM,
      VEHICLE_NO,
      WEIGHT_F,
      CONT_MRK,
      CONT_NO,
      CONT_SLD,
      SLD_DOC,
      CONT_FULL,
      CONT_WIDTH,
      CONT_HEIGHT,
      CONT_LENGTH,
      MEINS,
      EIR_MRK,
      EIR_DOC,
      WEIGHT_E,
      DENS_E,
      NOTE,
      DED_IND,
      DED_KG,
      DED_PR,
      DED_RMK,
      REJ_MRK,
      REJ_RMK,
      ORIGIN,
      SHORT_SIZE_IND,
      PREM_IND,
      PENALTY,
      PENALTY_RMK,
      SUR_IND,
      SUR_RMK,
      CRT_DT,
      CRT_TM,
      CRT_BY) 
      VALUES 
      ('".MANDT."',
      'INDO',
      :WSNUM,
      :VEHICLE_NO,
      :WEIGHT_F,
      :CONT_MRK,
      :CONT_NO,
      :CONT_SLD,
      :SLD_DOC,
      :CONT_FULL,
      :CONT_WIDTH,
      :CONT_HEIGHT,
      :CONT_LENGTH,
      'KG',
      :EIR_MRK,
      :EIR_DOC,
      :WEIGHT_E,
      :DENS_E,
      :NOTE,
      :DED_IND,
      :DED_KG,
      :DED_PR,
      :DED_RMK,
      :REJ_MRK,
      :REJ_RMK,
      :ORIGIN,
      :SHORT_SIZE_IND,
      :PREM_IND,
      :PENALTY,
      :PENALTY_RMK,
      :SUR_IND,
      :SUR_RMK,
      :CRT_DT,
      :CRT_TM,
      :CRT_BY)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":WSNUM", $header["WSNUM"], PDO::PARAM_STR);
    $stmt->bindValue(":VEHICLE_NO", $header["VEHICLE_NO"], PDO::PARAM_STR);
    $stmt->bindValue(":WEIGHT_F", $header["WEIGHT_F"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_MRK", $header["CONT_MRK"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_NO", $header["CONT_NO"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_SLD", $header["CONT_SLD"], PDO::PARAM_STR);
    $stmt->bindValue(":SLD_DOC", $header["SLD_DOC"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_FULL", $header["CONT_FULL"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_WIDTH", $header["CONT_WIDTH"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_HEIGHT", $header["CONT_HEIGHT"], PDO::PARAM_STR);
    $stmt->bindValue(":CONT_LENGTH", $header["CONT_LENGTH"], PDO::PARAM_STR);
    $stmt->bindValue(":EIR_MRK", $header["EIR_MRK"], PDO::PARAM_STR);
    $stmt->bindValue(":EIR_DOC", $header["EIR_DOC"], PDO::PARAM_STR);
    $stmt->bindValue(":WEIGHT_E", $header["WEIGHT_E"], PDO::PARAM_STR);
    $stmt->bindValue(":DENS_E", $header["DENS_E"], PDO::PARAM_STR);
    $stmt->bindValue(":NOTE", $header["NOTE"], PDO::PARAM_STR);
    $stmt->bindValue(":DED_IND", $header["DED_IND"], PDO::PARAM_STR);
    $stmt->bindValue(":DED_KG", $header["DED_KG"], PDO::PARAM_STR);
    $stmt->bindValue(":DED_PR", $header["DED_PR"], PDO::PARAM_STR);
    $stmt->bindValue(":DED_RMK", $header["DED_RMK"], PDO::PARAM_STR);
    $stmt->bindValue(":REJ_MRK", $header["REJ_MRK"], PDO::PARAM_STR);
    $stmt->bindValue(":REJ_RMK", $header["REJ_RMK"], PDO::PARAM_STR);
    $stmt->bindValue(":ORIGIN", $header["ORIGIN"], PDO::PARAM_STR);
    $stmt->bindValue(":SHORT_SIZE_IND", $header["SHORT_SIZE_IND"], PDO::PARAM_STR);
    $stmt->bindValue(":PREM_IND", $header["PREM_IND"], PDO::PARAM_STR);
    $stmt->bindValue(":PENALTY", $header["PENALTY"], PDO::PARAM_STR);
    $stmt->bindValue(":PENALTY_RMK", $header["PENALTY_RMK"], PDO::PARAM_STR);
    $stmt->bindValue(":SUR_IND", $header["SUR_IND"], PDO::PARAM_STR);
    $stmt->bindValue(":SUR_RMK", $header["SUR_RMK"], PDO::PARAM_STR);
    $stmt->bindValue(":CRT_DT", $header["CRT_DT"], PDO::PARAM_STR);
    $stmt->bindValue(":CRT_TM", $header["CRT_TM"], PDO::PARAM_STR);
    $stmt->bindValue(":CRT_BY", $header["CRT_BY"], PDO::PARAM_STR);
    if($stmt->execute()) {
			$return["status"] = true;
    } else {
      $return["status"] = false;
      $error = $stmt->errorInfo();
      $return["message"] = "Insert Header Error : ".trim(str_replace("\n", " ", $error[2]));
    }
    $stmt = null;
    $conn = null;
    return $return;
  }
  
  public function isExist($wsnum) {
    $return = array();
    $conn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD);
    $sql = "SELECT count(*) as cnt FROM sapsr3.ZTMM_SCRP_INS_HD@dbsap_iip WHERE mandt = '600' AND wsnum = :wsnum";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":WSNUM", $wsnum, PDO::PARAM_STR);
    if($stmt->execute()) {
      $count = 0;
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count = intval($row["CNT"]);
      }
      if($count > 0) {
        $return["status"] = true;
      } else {
        $return["status"] = false;
        $return["message"] = "Data Exist";
      }
    } else {
      $return["status"] = false;
      $error = $stmt->errorInfo();
      $return["message"] = "Select Header Error : ".trim(str_replace("\n", " ", $error[2]));
    }
    $stmt = null;
    $conn = null;
    return $return;
  }
  
  public function getById($wsnum) {
    $return = array();
    $conn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD);
    $sql = "SELECT * FROM sapsr3.ZTMM_SCRP_INS_HD@dbsap_iip WHERE mandt = '600' AND wsnum = :wsnum";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":WSNUM", $wsnum, PDO::PARAM_STR);
    if($stmt->execute()) {
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row["DENS_E"] = floatval($row["DENS_E"]);
        $row["DED_PR"] = floatval($row["DED_PR"]);
				$row["DED_KG"] = floatval($row["DED_KG"]);
        $crt_yr = substr($row["CRT_DT"], 0, 4);
        $crt_mo = substr($row["CRT_DT"], 4, 2);
        $crt_dy = substr($row["CRT_DT"], 6, 2);
        $row["CRT_DT"] = $crt_dy.".".$crt_mo.".".$crt_yr;
				if($row["SRLNO"] !== " ") {
					$row["SRLNO"] = ltrim($row["SRLNO"], "0");				
				}
        $crt_hr = substr($row["CRT_TM"], 2, 2);
        $crt_mi = substr($row["CRT_TM"], 4, 2);
        $row["CRT_TM"] = $crt_hr.":".$crt_mi;
        $return["HDR"] = $row;
      }
			
      //select item
      $sql = "SELECT * FROM sapsr3.ZTMM_SCRP_INS_IT@dbsap_iip WHERE mandt = '600' AND wsnum = :wsnum";
      $stmt = $conn->prepare($sql);
      $stmt->bindValue(":WSNUM", $wsnum, PDO::PARAM_STR);
      if($stmt->execute()) {
        $data = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $data[] = $row;
        }
        
        $return["ITM"] = $data;
        $return["status"] = true;
      } else {
        $return["status"] = false;
        $error = $stmt->errorInfo();
        $return["message"] = "Select item Error : ".trim(str_replace("\n", " ", $error[2]));
      }
			
      //get image
      $images = array();
      $file_path = "media/inspection/" . $wsnum . "/photo";
      if (file_exists($file_path)) {
        $files = scandir($file_path);
        $i = 0;
        foreach ($files as $file) {
          if ($i >= 2) {
            if ($file == "Thumbs.db") {
              continue;
            } else {
              $images[] = $file;
            }
          }
          $i++;
        }
      }
      $return["IMG"] = $images;
      
      //get document
      $document = array();
      $file_path = "media/inspection/" . $wsnum . "/document";
      if (file_exists($file_path)) {
        $files = scandir($file_path);
        $i = 0;
        foreach ($files as $file) {
          if ($i >= 2) {
            if ($file == "Thumbs.db") {
              continue;
            } else {
              $document[] = $file;
            }
          }
          $i++;
        }
      }
      $return["DOC"] = $document;
    } else {
      $return["status"] = false;
      $error = $stmt->errorInfo();
      $return["message"] = "Select Header Error : ".trim(str_replace("\n", " ", $error[2]));
    }
    $stmt = null;
    $conn = null;
    return $return;
  }
	
	public function getHeaderPure($wsnum) {
    $return = array();
    $conn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD);
    $sql = "SELECT * FROM sapsr3.ZTMM_SCRP_INS_HD@dbsap_iip WHERE mandt = '600' AND wsnum = :wsnum";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":WSNUM", $wsnum, PDO::PARAM_STR);
    if($stmt->execute()) {
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $return = $row;
      }
    } else {
      $return["status"] = false;
      $error = $stmt->errorInfo();
      $return["message"] = "Select Header Error : ".trim(str_replace("\n", " ", $error[2]));
    }
    $stmt = null;
    $conn = null;
    return $return;
  }
}
