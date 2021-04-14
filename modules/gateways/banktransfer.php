<?php

# Bank Transfer Payment Gateway Module

if (!defined("WHMCS")) die("This file cannot be accessed directly");

function banktransfer_config() {

    $configarray = array(
     "FriendlyName" => array(
        "Type" => "System",
        "Value" => "Bank Transfer"
        ),
     "instructions_georgian" => array(
        "FriendlyName" => "Bank Transfer Instructions - Georgian",
        "Type" => "textarea",
        "Rows" => "5",
        "Value" => "Bank Name:\nPayee Name:\nSort Code:\nAccount Number:",
        "Description" => "The instructions you want displaying to customers who choose this payment method - the invoice number will be shown underneath the text entered above",
        ),
     "instructions_english" => array(
        "FriendlyName" => "Bank Transfer Instructions - English",
        "Type" => "textarea",
        "Rows" => "5",
        "Value" => "Bank Name:\nPayee Name:\nSort Code:\nAccount Number:",
        "Description" => "The instructions you want displaying to customers who choose this payment method - the invoice number will be shown underneath the text entered above",
        ),
     "instructions_russian" => array(
        "FriendlyName" => "Bank Transfer Instructions - Russian",
        "Type" => "textarea",
        "Rows" => "5",
        "Value" => "Bank Name:\nPayee Name:\nSort Code:\nAccount Number:",
        "Description" => "The instructions you want displaying to customers who choose this payment method - the invoice number will be shown underneath the text entered above",
        ),
    );

    return $configarray;

}

function banktransfer_link($params) {
    global $_LANG;
    $language = isset($params['clientdetails']['language']) ? $params['clientdetails']['language'] : 'georgian';
    $instructions = isset($params['instructions_'.$language]) ? $params['instructions_'.$language] : $params['instructions_georgian'];
    
    if($params['currency'] == 'GEL' && $language != 'georgian')
        $instructions = str_replace("GE29TB7470536170100001", "GE72TB7470536080100008", $instructions);
    else if($params['currency'] != 'GEL' && $language == 'georgian')
        $instructions = str_replace("GE72TB7470536080100008", "GE29TB7470536170100001", $instructions);
        
    $code = '<p style="text-align:right; margin-top:20px">'.nl2br($instructions).'<br />'.$_LANG['invoicerefnum'].': Invoice #'.$params['invoiceid'].'</p>';

    return $code;

}