<?php
    define('CURRENT_DIR', str_replace('marketing', '', __DIR__));
    define('HOST', 'mail.ghosting.ge');
    define('PORT', 587);
    define('USER', 'marketing@ghosting.ge');
    define('PASSWORD', 'TAB11151985');
    define('FROM_EMAIL', 'marketing@ghosting.ge');
    define('FROM_NAME', 'ghosting.ge');
    define('INTERVAL', 10);
    include CURRENT_DIR.'init.php';
    set_time_limit(0);
    ignore_user_abort (true);
    function sendMail($email, $subject, $template_path)
    {
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->IsSMTP();
        $mail->Host = HOST;
        $mail->Port = PORT;
        $mail->SMTPAuth = true;
        $mail->Username = USER;
        $mail->Password = PASSWORD;
        $mail->SMTPSecure = '';
        $mail->IsHTML(true);
        $mail->From = FROM_EMAIL;
        $mail->FromName = FROM_NAME;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->Subject = $subject;
        $message = file_get_contents($template_path);
        $message = str_replace('{EMAIL_ID}', base64_encode($email), $message);
        $mail->Body = $message;
        $mail->ClearAllRecipients();
        $mail->AddAddress($email, '');
        if(!$mail->Send())
           return '['.date("Y-m-d H:i:s").']=>'.$email.'('.$mail->ErrorInfo.')';
        return '['.date("Y-m-d H:i:s").']=>'.$email.'(OK)';
    }
    if((php_sapi_name() !== 'cli') && isset($_GET['remove']))
    {
        $email = base64_decode($_GET['remove']);
        $res = mysql_query("SELECT * FROM marketing WHERE email='".mysql_real_escape_string($email)."' LIMIT 1");
        if($data = mysql_fetch_assoc($res))
        {
            if($data['language'] == 'georgian')
                $message = 'ოპერაცია წარმატებით შესრულდა!';
            else if($data['language'] == 'russian')
                $message = 'Операция успешно завершена!';
            else
                $message = 'Operation successfully completed!';
            mysql_query("UPDATE marketing SET `ignore` = 1 WHERE email = '".mysql_real_escape_string($email)."'");
            exit('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"><title>gHosting.ge</title><link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous"></head><body><h3 style="margin-top:120px; text-align:center"><i class="fa fa-check" style="color: green;font-size: 28px;"></i>'.$message.'</h3></body></html>');
        }
    }
    if((php_sapi_name() === 'cli'))
    {
        $output = "==========================".date("Y-m-d H:i:s")."==========================\n";
        $res = mysql_query("SELECT * FROM marketing WHERE status = 0 AND `ignore` = 0 ORDER BY id ASC LIMIT 300");
        while($data = mysql_fetch_assoc($res))
        {
            if($data['language'] == 'georgian')
                $subject = "$data[domain] - განათავსეთ თქვენი საიტი GOOGLE-ის ოფიციალურ სერვერებზე";
            else if($data['language'] == 'russian')
                $subject = "$data[domain] - Разместите ваш сайт на официальных серверах GOOGLE";
            else
                $subject = "$data[domain] - Host your website to the official GOOGLE servers";
            $output .= sendMail($data['email'], $subject, CURRENT_DIR.'marketing/template_'.$data['language'].'.html')."\n";
            mysql_query("UPDATE marketing SET status=1 WHERE id='$data[id]' LIMIT 1");
            sleep(INTERVAL);
        }
        file_put_contents(CURRENT_DIR.'marketing/send.log', $output, FILE_APPEND);
        exit();
    }
    header('HTTP/1.0 404 Not Found');
?>