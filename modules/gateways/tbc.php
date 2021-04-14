<?php
if (!defined("WHMCS")) 
{
    die("This file cannot be accessed directly");
}
function toEngTBC($str)
{
    $result = '';
    $chars = array('ა' => 'a', 'ბ' => 'b', 'გ' => 'g', 'დ' => 'd', 'ე' => 'e', 'ვ' => 'v', 'ზ' => 'z', 'თ' => 't','ი' => 'i','კ' => 'k','ლ' => 'l','მ' => 'm','ნ' => 'n','ო' => 'o','პ' => 'p','ჟ' => 'zh','რ' => 'r','ს' => 's','ტ' => 't','უ' => 'u','ფ' => 'f','ქ' => 'q','ღ' => 'gh','ყ' => 'k','შ' => 'sh','ჩ' => 'ch','ც' => 'ts','ძ' => 'dz','წ' => 'ts','ჭ' => 'tch','ხ' => 'kh','ჯ' => 'j','ჰ' => 'h');

    $length = mb_strlen($str);
    for ($i=0; $i<$length; $i++) 
    {
        $c = mb_substr($str, $i, 1);
        $result.= (array_key_exists($c, $chars) ? $chars[$c] : $c);
    }
    return $result;
}
function tbc_MetaData()
{
    return array(
        'DisplayName' => 'TBC Bank Payment Gateway Module',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}
function tbc_config()
{
    return array
    (
        'FriendlyName' => array
        (
            'Type' => 'System',
            'Value' => 'TBC Bank',
        )
    );
}
function tbc_link($params)
{
    global $_LANG;
    $biller=$params['clientdetails']['companyname'];
    if(empty($biller)) $biller=$params['clientdetails']['fullname'];
    $biller = toEngTBC($biller.' #'.$params['invoiceid']);
    $htmlOutput = '<form method="post" action="/tbc.php">';
    $htmlOutput .= '<input type="hidden" name="pay" value="true">';
    $htmlOutput .= '<input type="hidden" name="invoiceid" value="'.$params['invoiceid'].'">';
    $htmlOutput .= '<input type="hidden" name="amount" value="'.$params['amount'].'">';
    $htmlOutput .= '<input type="hidden" name="currency" value="'.$params['currency'].'">';
    $htmlOutput .= '<input type="hidden" name="biller" value="'.$biller.'">';
    $htmlOutput .= '<input type="submit" name="pay" value="'.$_LANG['ghosting']['gateways']['pay'].'" />';
    $htmlOutput .= '</form>';
    return $htmlOutput;
}
function tbc_refund($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'];
    $dropdownField = $params['dropdownField'];
    $radioField = $params['radioField'];
    $textareaField = $params['textareaField'];
    // Transaction Parameters
    $transactionIdToRefund = $params['transid'];
    $refundAmount = $params['amount'];
    $currencyCode = $params['currency'];
    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];
    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];
    // perform API call to initiate refund and interpret result
    return array(
        // 'success' if successful, otherwise 'declined', 'error' for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
        // Unique Transaction ID for the refund transaction
        'transid' => $refundTransactionId,
        // Optional fee amount for the fee value refunded
        'fees' => $feeAmount,
    );
}
function tbc_cancelSubscription($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'];
    $dropdownField = $params['dropdownField'];
    $radioField = $params['radioField'];
    $textareaField = $params['textareaField'];
    // Subscription Parameters
    $subscriptionIdToCancel = $params['subscriptionID'];
    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];
    // perform API call to cancel subscription and interpret result
    return array(
        // 'success' if successful, any other value for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
    );
}