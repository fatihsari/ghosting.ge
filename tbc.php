<?php
    include(__DIR__.'/init.php');
    require_once __DIR__ . '/includes/gatewayfunctions.php';
    require_once __DIR__ . '/includes/invoicefunctions.php';
    function showError($message)
    {
        exit('<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Payment processing error!</title><link href="https://fonts.googleapis.com/css?family=Maven+Pro:400,900" rel="stylesheet"><style>*{-webkit-box-sizing: border-box; box-sizing: border-box;}body{padding: 0; margin: 0;}#notfound{position: relative; height: 100vh;}#notfound .notfound{position: absolute; left: 50%; top: 50%; -webkit-transform: translate(-50%, -50%); -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);}.notfound{max-width: 920px; width: 100%; line-height: 1.4; text-align: center; padding-left: 15px; padding-right: 15px;}.notfound .notfound-404{position: absolute; height: 100px; top: 0; left: 50%; -webkit-transform: translateX(-50%); -ms-transform: translateX(-50%); transform: translateX(-50%); z-index: -1;}.notfound .notfound-404 h1{font-family: \'Maven Pro\', sans-serif; color: #ececec; font-weight: 900; font-size: 276px; margin: 0px; position: absolute; left: 50%; top: 50%; -webkit-transform: translate(-50%, -50%); -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);}.notfound h2{font-family: \'Maven Pro\', sans-serif; font-size: 46px; color: #000; font-weight: 900; text-transform: uppercase; margin: 0px;}.notfound p{font-family: \'Maven Pro\', sans-serif; font-size: 16px; color: #000; font-weight: 400; text-transform: uppercase; margin-top: 15px;}.notfound a{font-family: \'Maven Pro\', sans-serif; font-size: 14px; text-decoration: none; text-transform: uppercase; background: #189cf0; display: inline-block; padding: 16px 38px; border: 2px solid transparent; border-radius: 40px; color: #fff; font-weight: 400; -webkit-transition: 0.2s all; transition: 0.2s all;}.notfound a:hover{background-color: #fff; border-color: #189cf0; color: #189cf0;}@media only screen and (max-width: 480px){.notfound .notfound-404 h1{font-size: 162px;}.notfound h2{font-size: 26px;}}</style><!--[if lt IE 9]> <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script> <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]--></head><body><div id="notfound"><div class="notfound"><div class="notfound-404"><h1>ERROR</h1></div><h2>WE ARE SORRY, PAYMENT PROCESSING ERROR!</h2><p>'.$message.'</p><a href="https://ghosting.ge">Back To Homepage</a></div></div></body></html>');
    }
    function curlPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_VERBOSE, '1');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSLCERT, TBC_CERT);
        curl_setopt($ch, CURLOPT_SSLKEY, TBC_CERT_KEY);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, TBC_CERT_PASSWORD);
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, TBC_CERT_PASSWORD);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $resp = curl_exec($ch);
        if (curl_errno($ch))
            $error_msg = curl_error($ch);
        curl_close($ch);
        if (isset($error_msg))
            showError($error_msg);
        return $resp;
    }
    if(isset($_REQUEST["trans_id"]))
    {
        $gatewayModuleName = 'tbc';
        $gatewayParams = getGatewayVariables($gatewayModuleName);
        if (!$gatewayParams['type'])
            showError("Module Not Activated");
        $transactionId = '';
        $success = false;   
        if(!empty($_REQUEST["trans_id"]))
        {
            $transactionId = str_replace(' ','+',$_REQUEST["trans_id"]);
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $url = 'https://ecommerce.ufc.ge:18443/ecomm2/MerchantHandler';
        	$params = "command=c&trans_id=".urlencode($transactionId)."&client_ip_addr=$ip_address";
        	$resp = curlPost($url, $params);
        	preg_match("/RESULT\s*:\s*([^\n]+)/", $resp, $m);
        	$result = trim($m[1]);
        	if(isset($_GET['check']))
        		exit($resp);
        	if($result == 'OK')
        		$success = true;
        	else
        		$success = false;
        }
        if(!$transactionId)
            showError("Invalid Transaction: $transactionId");
        $res = mysql_query("SELECT invoice_id FROM transaction_info WHERE transaction_id='$transactionId' ORDER BY date DESC LIMIT 1");
        $data = mysql_fetch_array($res);
        if(!$data)
            showError("Invalid Transaction: $transactionId");
        $invoiceId = $data['invoice_id'];
        if(!$invoiceId)
            showError("Invalid Transaction: $transactionId");
        $transactionStatus = $success ? 'Success' : 'Failure';
        logTransaction($gatewayParams['name'], array($_POST, $_GET, $result), $transactionStatus);
        if ($success) 
        {
            mysql_query("UPDATE transaction_info SET status = 1 WHERE transaction_id='$transactionId'");
            addInvoicePayment
            (
                $invoiceId,
                $transactionId,
                0,
                0,
                $gatewayModuleName
            );
        }
        else
            mysql_query("UPDATE transaction_info SET status = 2 WHERE transaction_id='$transactionId'");
        header('location: /viewinvoice.php?id='.$invoiceId);
        exit;
    }
    if(isset($_GET['closeday']))
    {
        $url = 'https://ecommerce.ufc.ge:18443/ecomm2/MerchantHandler';
    	$params = "command=b";
    	exit(curlPost($url, $params));
    }
    if(isset($_POST['pay']) && isset($_POST['invoiceid']) && isset($_POST['amount']) && isset($_POST['currency']) && isset($_POST['biller']))
    {
        $url = 'https://ecommerce.ufc.ge:18443/ecomm2/MerchantHandler';
        $invoiceId = $_POST['invoiceid'];
        $description = 'Invoice #'.$invoiceId;
        $amount = intval(floatval($_POST['amount'])*100);
        $currencyCode = $_POST['currency'] == 'GEL' ? '981' : '840';
    	$language=$_POST['currency'] == 'GEL' ? 'GE' : 'EN';
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $biller=$_POST['biller'];
    	$params = 'command=v&amount='.$amount.'&currency='.$currencyCode.'&client_ip_addr='.$ip_address.'&description='.urlencode($description).'&language='.$language.'&msg_type=SMS&biller='.urlencode($biller);
    	$resp = curlPost($url, $params);
    	preg_match("/TRANSACTION_ID\s*:\s*([^\n]+)/", $resp, $m);
    	$trans_id = trim($m[1]);
    	if($trans_id)
    	{
    	    mysql_query("INSERT INTO transaction_info (transaction_id, invoice_id, bank, status, date) VALUES('$trans_id','$invoiceId','TBC','0','".time()."')");
    	}
    	$htmlOutput = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>'.$_LANG['ghosting']['gateways']['redirecting'].'</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous"></head><body onload="document.getElementById(\'payForm\').submit();">';
        $htmlOutput .= '<div class="container" style="margin-top:20px"><h2>'.$_LANG['ghosting']['gateways']['redirecting2'].'</h2><br>'.$_LANG['ghosting']['gateways']['redirecting3'].'<br><form method="post" action="https://ecommerce.ufc.ge/ecomm2/ClientHandler" style="margin-top:10px" id="payForm">';
        $htmlOutput .= '<input type="hidden" name="trans_id" value="'.$trans_id.'">';
        $htmlOutput .= '<input type="submit" class="btn btn-success" value="'.$_LANG['ghosting']['gateways']['redirecting4'].'" />';
        $htmlOutput .= '</form></container></body></html>';
        exit($htmlOutput);
    }
?>