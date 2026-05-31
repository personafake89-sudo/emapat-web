<?php
class USER
{
    function send_mail($email,$message,$subject)
    {
        require_once('mailer/class.phpmailer.php');
		$mail = new PHPMailer();
		$mail -> CharSet = "UTF-8";
		$mail->IsSMTP();
		$mail->SMTPDebug  = 0;
		$mail->SMTPAuth   = true;
		//$mail->SMTPSecure = "ssl";
		$mail->SMTPSecure = 'tls';// Enable TLS encryption, `ssl` also accepted
		$mail->Host       = "confianza.epsmoyobamba.com.pe";  //"smtp.gmail.com";
		$mail->Port       = 587;
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
				));
		// $mail->isHTML(true);// Set email format to HTML
		$mail->AddAddress($email);
		$mail->Username="pagosvisa@epsmoyobamba.com.pe";
		$mail->Password="p4g0sVisa";
		$mail->SetFrom('pagosvisa@epsmoyobamba.com.pe','EPS MOYOBAMBA S.A.');
		$mail->AddReplyTo("pagosvisa@epsmoyobamba.com.pe","EPS MOYOBAMBA S.A.");
		$mail->Subject    = $subject;
		$mail->MsgHTML($message);
		$mail->Send();
	}
}
