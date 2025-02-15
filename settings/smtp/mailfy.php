<?php
include('smtp/PHPMailerAutoload.php');

function smtp_mailer($to, $subject, $msg) {
    $mail = new PHPMailer(); 
    $mail->IsSMTP(); 
    $mail->SMTPAuth = true; 
    $mail->SMTPAutoTLS = false;  // ✅ Speeds up TLS handshake
    $mail->SMTPSecure = 'tls'; 
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587; 
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "mercytvfire@gmail.com";
    $mail->Password = "ulnnlnskctuaakax";  // ⚠️ Consider using environment variables instead
    $mail->SetFrom('mercytvfire@gmail.com', 'MercyTV OTT');
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);  // ✅ Use AddAddress instead of AddBCC for better performance
    $mail->SMTPKeepAlive = true;  // ✅ Keeps SMTP connection open for multiple emails
    $mail->XMailer = ' ';  // ✅ Hides PHPMailer signature
    $mail->SMTPOptions = array('ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => false
    ));

    if (!$mail->Send()) {
        return $mail->ErrorInfo;
    } else {
        return true;
    }
}
?>
