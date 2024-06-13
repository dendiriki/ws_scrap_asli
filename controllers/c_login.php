<?php
if($action == "ajax_login") {
  $userid = $_POST["userid"];
  $userpass = $_POST["userpass"];
  $user = new User();
  $login = $user->login($userid, $userpass);
	$login["scrap_edit"] = $user->checkUserRole($userid, "10");
  echo json_encode($login);
}

if($action == "login") {
  echo "Please visit <a href='http://intranet.ispatindo.com'>intranet</a>";
}
?>