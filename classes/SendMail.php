<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "vendor/PHPMailer/Exception.php" ;
require "vendor/PHPMailer/PHPMailer.php" ;
require 'vendor/PHPMailer/SMTP.php';

/**
 * Description of SendMail
 *
 * @author dheo
 */
class SendMail {
  //put your code here
  const MAIL_HOST = "mail.ispatindo.com";
  const MAIL_PORT = 25;
  const MAIL_SENDER = "intranet@ispatindo.com";
  const MAIL_SENDER_NAME = "Intranet";
  public $errorMessage = null;

  public function __construct() {
    ;
  }
  
  public function sendTheMail($subject, $body, $recipient = array()) {
    $mail = new PHPMailer(true);
    try {
      //Server settings
      $mail->SMTPDebug = 2; //enable verbose mode
      $mail->isSMTP();
      $mail->Host = gethostbyname(self::MAIL_HOST);
      $mail->SMTPAuth = false;
      $mail->Port = self::MAIL_PORT;
      //Recipients
      $mail->setFrom(self::MAIL_SENDER, self::MAIL_SENDER_NAME);
      foreach($recipient as $recp){
        $mail->addAddress($recp);
      }

      //Content
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $body;

      $mail->send();
      return true;
    } catch (Exception $e) {
      $this->errorMessage = $mail->ErrorInfo;
      handleException($mail->ErrorInfo);
      return false;
    }
  }
  
  public function sendAfterSave() {
    
  }
}
