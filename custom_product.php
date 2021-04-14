<?php 
define("CLIENTAREA", true);
use WHMCS\Product\Product;
require(__DIR__ . "/init.php");
require(ROOTDIR . "/includes/configoptionsfunctions.php");
require(ROOTDIR . "/includes/customfieldfunctions.php");
global $CONFIG;
global $_LANG;
$ca = new WHMCS\ClientArea();
$ca->initPage();
$whmcs = WHMCS\Application::getInstance();
$ajax = (int)$whmcs->get_req_var("ajax");
$pid = (int)$whmcs->get_req_var("pid");
$userid = (isset($_SESSION["uid"]) ? $_SESSION["uid"] : "");
$currencyid = (isset($_SESSION["currency"]) ? $_SESSION["currency"] : "");
$currency = getCurrency($userid, $currencyid);

$cycle = $whmcs->get_req_var("billingcycle") ? $whmcs->get_req_var("billingcycle") : "monthly";
if( !function_exists("getBillingCycleMonths") ) 
{
    require(ROOTDIR . "/includes/invoicefunctions.php");
}
$cyclemonths = getBillingCycleMonths($cycle);
$pricing = getPricingInfo($pid);
$customfields=[];
$customfields = getCustomFields("product", $pid, "", "", "on", $customfields);
$configoptions = array(  );
$where = array( "pid" => $pid );
$result2 = select_query("tblproductconfigoptions", "", $where, "order` ASC,`id", "ASC", "", "tblproductconfiglinks ON tblproductconfiglinks.gid=tblproductconfigoptions.gid");
while( $data2 = mysql_fetch_array($result2) ) 
{
    $optionid = $data2["id"];
    $optionname = translateOptionName($data2["optionname"]);
    $optiontype = $data2["optiontype"];
    $optionhidden = $data2["hidden"];
    $qtyminimum = $data2["qtyminimum"];
    $qtymaximum = $data2["qtymaximum"];
    if( strpos($optionname, "|") ) 
    {
        $optionname = explode("|", $optionname);
        $optionname = trim($optionname[1]);
    }
    $options = array(  );
    $selname = $selectedoption = $selsetup = $selrecurring = "";
    $selectedqty = 0;
    $foundPreselectedValue = false;
    $selected_configoptions = array(  );
    $selected_configoption = $whmcs->get_req_var("configoption");
    if( $selected_configoption ) 
    {
        $configOpsReturn = validateAndSanitizeQuantityConfigOptions($selected_configoption);
        $selected_configoptions = $configOpsReturn["validOptions"];
    }
    $selvalue = (isset($selected_configoptions[$optionid]) ? $selected_configoptions[$optionid] : "");
    if($optiontype != 3 || $selvalue)
    {
    $result3 = select_query("tblproductconfigoptionssub", "tblpricing.*,tblproductconfigoptionssub.*", array( "tblproductconfigoptionssub.configid" => $optionid, "tblpricing.type" => "configoptions", "tblpricing.currency" => $currency["id"] ), "tblproductconfigoptionssub`.`sortorder` ASC,`tblproductconfigoptionssub`.`id", "ASC", "", "tblpricing ON tblpricing.relid=tblproductconfigoptionssub.id");
    while( $data3 = mysql_fetch_array($result3) ) 
    {
        $opid = $data3["id"];
        $ophidden = $data3["hidden"];
        $setup = $data3[substr($cycle, 0, 1) . "setupfee"];
        $price = $fullprice = $data3[$cycle];
        if( $orderform && $CONFIG["ProductMonthlyPricingBreakdown"] ) 
        {
            $price = $price / $cyclemonths;
        }
        $setupvalue = (0 < $setup ? " + " . formatCurrency($setup) . " " . $_LANG["ordersetupfee"] : "");
        $rawName = $required = $opname = $data3["optionname"];
        if( strpos($opname, "|") ) 
        {
            $opnameArr = explode("|", $opname);
            $opname = trim($opnameArr[1]);
            $required = trim($opnameArr[0]);
        }
        $opnameonly = $opname;
        if( !$ophidden || $opid == $selvalue ) 
        {
            $options[] = array( "id" => $opid, "name" => $opname . $setupvalue, "rawName" => $rawName, "required" => $required, "nameonly" => $opnameonly, "nameandprice" => $opname, "setup" => $setup, "fullprice" => $fullprice, "recurring" => $price, "hidden" => $ophidden );
        }
        if( $opid == $selvalue || !$selvalue && !$ophidden ) 
        {
            $selname = $opnameonly;
            $selectedoption = $opname;
            $selsetup = $setup;
            $selrecurring = $fullprice;
            $selvalue = $opid;
            $foundPreselectedValue = true;
        }
    }
    if( !$foundPreselectedValue && 0 < count($options) ) 
    {
        $selname = $options[0]["nameonly"];
        $selectedoption = $options[0]["nameandprice"];
        $selsetup = $options[0]["setup"];
        $selrecurring = $options[0]["fullprice"];
        $selvalue = $options[0]["id"];
    }
    }
    
    if($optiontype == 3)
    {
        $selname = $selname ? $_LANG["yes"] : $_LANG["no"];
    }
    $configoptions[] = array( "id" => $optionid, "hidden" => $optionhidden, "optionname" => $optionname, "optiontype" => $optiontype, "selectedvalue" => $selvalue, "selectedqty" => $selectedqty, "selectedname" => $selname, "selectedoption" => $selectedoption, "selectedsetup" => $selsetup, "selectedrecurring" => $selrecurring, "qtyminimum" => $qtyminimum, "qtymaximum" => $qtymaximum, "options" => $options );
}
function getPricingInfo($pid, $inclconfigops = false, $upgrade = false)
    {
        global $CONFIG;
        global $_LANG;
        global $currency;
        $result = select_query("tblproducts", "", array( "id" => $pid ));
        $data = mysql_fetch_array($result);
        $paytype = $data["paytype"];
        $freedomain = $data["freedomain"];
        $freedomainpaymentterms = $data["freedomainpaymentterms"];
        if( !isset($currency["id"]) ) 
        {
            $currency = getCurrency();
        }
        $result = select_query("tblpricing", "", array( "type" => "product", "currency" => $currency["id"], "relid" => $pid ));
        $data = mysql_fetch_array($result);
        $msetupfee = $data["msetupfee"];
        $qsetupfee = $data["qsetupfee"];
        $ssetupfee = $data["ssetupfee"];
        $asetupfee = $data["asetupfee"];
        $bsetupfee = $data["bsetupfee"];
        $tsetupfee = $data["tsetupfee"];
        $monthly = $data["monthly"];
        $quarterly = $data["quarterly"];
        $semiannually = $data["semiannually"];
        $annually = $data["annually"];
        $biennially = $data["biennially"];
        $triennially = $data["triennially"];
        $configoptions = new WHMCS\Product\ConfigOptions();
        $freedomainpaymentterms = explode(",", $freedomainpaymentterms);
        $monthlypricingbreakdown = $CONFIG["ProductMonthlyPricingBreakdown"];
        $minprice = 0;
        $setupFee = 0;
        $mincycle = "";
        $hasconfigoptions = false;
        if( $paytype == "free" ) 
        {
            $pricing["type"] = $mincycle = "free";
        }
        else
        {
            if( $paytype == "onetime" ) 
            {
                if( $inclconfigops ) 
                {
                    $msetupfee += $configoptions->getBasePrice($pid, "msetupfee");
                    $monthly += $configoptions->getBasePrice($pid, "monthly");
                }
                $minprice = $monthly;
                $setupFee = $msetupfee;
                $pricing["type"] = $mincycle = "onetime";
                $pricing["onetime"] = new WHMCS\View\Formatter\Price($monthly, $currency);
                if( $msetupfee != "0.00" ) 
                {
                    $pricing["onetime"] .= " + " . new WHMCS\View\Formatter\Price($msetupfee, $currency) . " " . $_LANG["ordersetupfee"];
                }
                if( in_array("onetime", $freedomainpaymentterms) && $freedomain && !$upgrade ) 
                {
                    $pricing["onetime"] .= " (" . $_LANG["orderfreedomainonly"] . ")";
                }
            }
            else
            {
                if( $paytype == "recurring" ) 
                {
                    $pricing["type"] = "recurring";
                    if( 0 <= $monthly ) 
                    {
                        if( $inclconfigops ) 
                        {
                            $msetupfee += $configoptions->getBasePrice($pid, "msetupfee");
                            $monthly += $configoptions->getBasePrice($pid, "monthly");
                        }
                        if( !$mincycle ) 
                        {
                            $minprice = $monthly;
                            $setupFee = $msetupfee;
                            $mincycle = "monthly";
                            $minMonths = 1;
                        }
                        if( $monthlypricingbreakdown ) 
                        {
                            $pricing["monthly"] = $_LANG["orderpaymentterm1month"] . " - " . new WHMCS\View\Formatter\Price($monthly, $currency);
                        }
                        else
                        {
                            $pricing["monthly"] = new WHMCS\View\Formatter\Price($monthly, $currency) . " " . $_LANG["orderpaymenttermmonthly"];
                        }
                        if( $msetupfee != "0.00" ) 
                        {
                            $pricing["monthly"] .= " + " . new WHMCS\View\Formatter\Price($msetupfee, $currency) . " " . $_LANG["ordersetupfee"];
                        }
                        if( in_array("monthly", $freedomainpaymentterms) && $freedomain && !$upgrade ) 
                        {
                            $pricing["monthly"] .= " (" . $_LANG["orderfreedomainonly"] . ")";
                        }
                    }
                    if( 0 <= $quarterly ) 
                    {
                        if( $inclconfigops ) 
                        {
                            $qsetupfee += $configoptions->getBasePrice($pid, "qsetupfee");
                            $quarterly += $configoptions->getBasePrice($pid, "quarterly");
                        }
                        if( !$mincycle ) 
                        {
                            $minprice = ($monthlypricingbreakdown ? $quarterly / 3 : $quarterly);
                            $setupFee = $qsetupfee;
                            $mincycle = "quarterly";
                            $minMonths = 3;
                        }
                        if( $monthlypricingbreakdown ) 
                        {
                            $pricing["quarterly"] = $_LANG["orderpaymentterm3month"] . " - " . new WHMCS\View\Formatter\Price($quarterly / 3, $currency);
                        }
                        else
                        {
                            $pricing["quarterly"] = new WHMCS\View\Formatter\Price($quarterly, $currency) . " " . $_LANG["orderpaymenttermquarterly"];
                        }
                        if( $qsetupfee != "0.00" ) 
                        {
                            $pricing["quarterly"] .= " + " . new WHMCS\View\Formatter\Price($qsetupfee, $currency) . " " . $_LANG["ordersetupfee"];
                        }
                        if( in_array("quarterly", $freedomainpaymentterms) && $freedomain && !$upgrade ) 
                        {
                            $pricing["quarterly"] .= " (" . $_LANG["orderfreedomainonly"] . ")";
                        }
                    }
                    if( 0 <= $semiannually ) 
                    {
                        if( $inclconfigops ) 
                        {
                            $ssetupfee += $configoptions->getBasePrice($pid, "ssetupfee");
                            $semiannually += $configoptions->getBasePrice($pid, "semiannually");
                        }
                        if( !$mincycle ) 
                        {
                            $minprice = ($monthlypricingbreakdown ? $semiannually / 6 : $semiannually);
                            $setupFee = $ssetupfee;
                            $mincycle = "semiannually";
                            $minMonths = 6;
                        }
                        if( $monthlypricingbreakdown ) 
                        {
                            $pricing["semiannually"] = $_LANG["orderpaymentterm6month"] . " - " . new WHMCS\View\Formatter\Price($semiannually / 6, $currency);
                        }
                        else
                        {
                            $pricing["semiannually"] = new WHMCS\View\Formatter\Price($semiannually, $currency) . " " . $_LANG["orderpaymenttermsemiannually"];
                        }
                        if( $ssetupfee != "0.00" ) 
                        {
                            $pricing["semiannually"] .= " + " . new WHMCS\View\Formatter\Price($ssetupfee, $currency) . " " . $_LANG["ordersetupfee"];
                        }
                        if( in_array("semiannually", $freedomainpaymentterms) && $freedomain && !$upgrade ) 
                        {
                            $pricing["semiannually"] .= " (" . $_LANG["orderfreedomainonly"] . ")";
                        }
                    }
                    if( 0 <= $annually ) 
                    {
                        if( $inclconfigops ) 
                        {
                            $asetupfee += $configoptions->getBasePrice($pid, "asetupfee");
                            $annually += $configoptions->getBasePrice($pid, "annually");
                        }
                        if( !$mincycle ) 
                        {
                            $minprice = ($monthlypricingbreakdown ? $annually / 12 : $annually);
                            $setupFee = $asetupfee;
                            $mincycle = "annually";
                            $minMonths = 12;
                        }
                        if( $monthlypricingbreakdown ) 
                        {
                            $pricing["annually"] = $_LANG["orderpaymentterm12month"] . " - " . new WHMCS\View\Formatter\Price($annually / 12, $currency);
                        }
                        else
                        {
                            $pricing["annually"] = new WHMCS\View\Formatter\Price($annually, $currency) . " " . $_LANG["orderpaymenttermannually"];
                        }
                        if( $asetupfee != "0.00" ) 
                        {
                            $pricing["annually"] .= " + " . new WHMCS\View\Formatter\Price($asetupfee, $currency) . " " . $_LANG["ordersetupfee"];
                        }
                        if( in_array("annually", $freedomainpaymentterms) && $freedomain && !$upgrade ) 
                        {
                            $pricing["annually"] .= " (" . $_LANG["orderfreedomainonly"] . ")";
                        }
                    }
                    if( 0 <= $biennially ) 
                    {
                        if( $inclconfigops ) 
                        {
                            $bsetupfee += $configoptions->getBasePrice($pid, "bsetupfee");
                            $biennially += $configoptions->getBasePrice($pid, "biennially");
                        }
                        if( !$mincycle ) 
                        {
                            $minprice = ($monthlypricingbreakdown ? $biennially / 24 : $biennially);
                            $setupFee = $bsetupfee;
                            $mincycle = "biennially";
                            $minMonths = 24;
                        }
                        if( $monthlypricingbreakdown ) 
                        {
                            $pricing["biennially"] = $_LANG["orderpaymentterm24month"] . " - " . new WHMCS\View\Formatter\Price($biennially / 24, $currency);
                        }
                        else
                        {
                            $pricing["biennially"] = new WHMCS\View\Formatter\Price($biennially, $currency) . " " . $_LANG["orderpaymenttermbiennially"];
                        }
                        if( $bsetupfee != "0.00" ) 
                        {
                            $pricing["biennially"] .= " + " . new WHMCS\View\Formatter\Price($bsetupfee, $currency) . " " . $_LANG["ordersetupfee"];
                        }
                        if( in_array("biennially", $freedomainpaymentterms) && $freedomain && !$upgrade ) 
                        {
                            $pricing["biennially"] .= " (" . $_LANG["orderfreedomainonly"] . ")";
                        }
                    }
                    if( 0 <= $triennially ) 
                    {
                        if( $inclconfigops ) 
                        {
                            $tsetupfee += $configoptions->getBasePrice($pid, "tsetupfee");
                            $triennially += $configoptions->getBasePrice($pid, "triennially");
                        }
                        if( !$mincycle ) 
                        {
                            $minprice = ($monthlypricingbreakdown ? $triennially / 36 : $triennially);
                            $setupFee = $tsetupfee;
                            $mincycle = "triennially";
                            $minMonths = 36;
                        }
                        if( $monthlypricingbreakdown ) 
                        {
                            $pricing["triennially"] = $_LANG["orderpaymentterm36month"] . " - " . new WHMCS\View\Formatter\Price($triennially / 36, $currency);
                        }
                        else
                        {
                            $pricing["triennially"] = new WHMCS\View\Formatter\Price($triennially, $currency) . " " . $_LANG["orderpaymenttermtriennially"];
                        }
                        if( $tsetupfee != "0.00" ) 
                        {
                            $pricing["triennially"] .= " + " . new WHMCS\View\Formatter\Price($tsetupfee, $currency) . " " . $_LANG["ordersetupfee"];
                        }
                        if( in_array("triennially", $freedomainpaymentterms) && $freedomain && !$upgrade ) 
                        {
                            $pricing["triennially"] .= " (" . $_LANG["orderfreedomainonly"] . ")";
                        }
                    }
                }
            }
        }
        $pricing["hasconfigoptions"] = $configoptions->hasConfigOptions($pid);
        if( isset($pricing["onetime"]) ) 
        {
            $pricing["cycles"]["onetime"] = $pricing["onetime"];
        }
        if( isset($pricing["monthly"]) ) 
        {
            $pricing["cycles"]["monthly"] = $pricing["monthly"];
        }
        if( isset($pricing["quarterly"]) ) 
        {
            $pricing["cycles"]["quarterly"] = $pricing["quarterly"];
        }
        if( isset($pricing["semiannually"]) ) 
        {
            $pricing["cycles"]["semiannually"] = $pricing["semiannually"];
        }
        if( isset($pricing["annually"]) ) 
        {
            $pricing["cycles"]["annually"] = $pricing["annually"];
        }
        if( isset($pricing["biennially"]) ) 
        {
            $pricing["cycles"]["biennially"] = $pricing["biennially"];
        }
        if( isset($pricing["triennially"]) ) 
        {
            $pricing["cycles"]["triennially"] = $pricing["triennially"];
        }
        $pricing["rawpricing"] = array( "msetupfee" => format_as_currency($msetupfee), "qsetupfee" => format_as_currency($qsetupfee), "ssetupfee" => format_as_currency($ssetupfee), "asetupfee" => format_as_currency($asetupfee), "bsetupfee" => format_as_currency($bsetupfee), "tsetupfee" => format_as_currency($tsetupfee), "monthly" => format_as_currency($monthly), "quarterly" => format_as_currency($quarterly), "semiannually" => format_as_currency($semiannually), "annually" => format_as_currency($annually), "biennially" => format_as_currency($biennially), "triennially" => format_as_currency($triennially) );
        $pricing["minprice"] = array( "price" => new WHMCS\View\Formatter\Price($minprice, $currency), "setupFee" => (0 < $setupFee ? new WHMCS\View\Formatter\Price($setupFee, $currency) : 0), "cycle" => ($monthlypricingbreakdown && $paytype == "recurring" ? "monthly" : $mincycle), "simple" => (new WHMCS\View\Formatter\Price($minprice, $currency))->toPrefixed() );
        if( isset($minMonths) ) 
        {
            switch( $minMonths ) 
            {
                case 3:
                    $langVar = "shoppingCartProductPerMonth";
                    $count = "3 ";
                    break;
                case 6:
                    $langVar = "shoppingCartProductPerMonth";
                    $count = "6 ";
                    break;
                case 12:
                    $langVar = ($monthlypricingbreakdown ? "shoppingCartProductPerMonth" : "shoppingCartProductPerYear");
                    $count = "";
                    break;
                case 24:
                    $langVar = ($monthlypricingbreakdown ? "shoppingCartProductPerMonth" : "shoppingCartProductPerYear");
                    $count = "2 ";
                    break;
                case 36:
                    $langVar = ($monthlypricingbreakdown ? "shoppingCartProductPerMonth" : "shoppingCartProductPerYear");
                    $count = "3 ";
                    break;
                default:
                    $langVar = "shoppingCartProductPerMonth";
                    $count = "";
            }
            $pricing["minprice"]["cycleText"] = Lang::trans($langVar, array( ":count" => $count, ":price" => $pricing["minprice"]["simple"] ));
            $pricing["minprice"]["cycleTextWithCurrency"] = Lang::trans($langVar, array( ":count" => $count, ":price" => $pricing["minprice"]["price"] ));
        }
        
        return $pricing;
    }
if($ajax)
{
    function calcPrice($pid, $configurableoptions, $pricing, $cycle)
    {
        global $_LANG;
        global $currency;
        $data = get_query_vals("tblproducts", "tblproducts.*, tblproductgroups.name AS groupname", array( "tblproducts.id" => $pid ), "", "", "", "tblproductgroups ON tblproductgroups.id=tblproducts.gid");
        if( $pricing["type"] == "recurring" ) 
        {
            $billingcycle = $cycle;
            if( !in_array($billingcycle, array( "monthly", "quarterly", "semiannually", "annually", "biennially", "triennially" )) ) 
            {
                $billingcycle = "";
            }
            if( $pricing["rawpricing"][$billingcycle] < 0 ) 
            {
                $billingcycle = "";
            }
            if( !$billingcycle ) 
            {
                if( 0 <= $pricing["rawpricing"]["monthly"] ) 
                {
                    $billingcycle = "monthly";
                }
                else
                {
                    if( 0 <= $pricing["rawpricing"]["quarterly"] ) 
                    {
                        $billingcycle = "quarterly";
                    }
                    else
                    {
                        if( 0 <= $pricing["rawpricing"]["semiannually"] ) 
                        {
                            $billingcycle = "semiannually";
                        }
                        else
                        {
                            if( 0 <= $pricing["rawpricing"]["annually"] ) 
                            {
                                $billingcycle = "annually";
                            }
                            else
                            {
                                if( 0 <= $pricing["rawpricing"]["biennially"] ) 
                                {
                                    $billingcycle = "biennially";
                                }
                                else
                                {
                                    if( 0 <= $pricing["rawpricing"]["triennially"] ) 
                                    {
                                        $billingcycle = "triennially";
                                    }
                                }
                            }
                        }
                    }
                }
            }
         }
         else
         {
             if( $pricing["type"] == "onetime" ) 
             {
                 $billingcycle = "onetime";
             }
             else
             {
                 $billingcycle = "free";
             }
         }
        
        $configoptionsdb = array(  );
        $configoptions = array(  );
        $product_onetime = 0;
        $product_recurring = 0;
        if( $configurableoptions ) 
        {
            foreach( $configurableoptions as $confkey => $value ) 
            {
                $configoptions[] = array( "name" => $value["optionname"], "type" => $value["optiontype"], "option" => $value["selectedoption"], "optionname" => $value["selectedname"], "setup" => (0 < $value["selectedsetup"] ? new WHMCS\View\Formatter\Price($value["selectedsetup"], $currency) : ""), "recurring" => new WHMCS\View\Formatter\Price($value["selectedrecurring"], $currency), "qty" => $value["selectedqty"] );
                $product_onetime += $value["selectedrecurring"];
                $product_recurring += $value["selectedrecurring"];
            }
        }
        $productdata["configoptions"] = $configoptions;
        $productdata["pricing"]["totaltoday"] = new WHMCS\View\Formatter\Price($product_onetime, $currency);
        $productdata["pricing"]["recurring"][$billingcycle] = $product_recurring;
        foreach( $productdata["pricing"]["recurring"] as $cycle => $recurring ) 
        {
            unset($productdata["pricing"]["recurring"][$cycle]);
            if( 0 < $recurring ) 
            {
                $recurringwithtax = $recurring;
                $recurringbeforetax = $recurringwithtax;
                $productdata["pricing"]["recurring"][$_LANG["orderpaymentterm" . $cycle]] = new WHMCS\View\Formatter\Price($recurringwithtax, $currency);
                $productdata["pricing"]["recurringexcltax"][$_LANG["orderpaymentterm" . $cycle]] = new WHMCS\View\Formatter\Price($recurringbeforetax, $currency);
            }
        }
        return $productdata;
    }
    if($whmcs->get_req_var("validate_domain"))
    {
        $domains = $whmcs->get_req_var("domains");
        if(empty($domains) || empty($domains[0]))
            exit($_LANG['cartproductdomaindesc']);
        if (!preg_match('/^ (?: [a-z0-9] (?:[a-z0-9\-]* [a-z0-9])? \.? )[a-z0-9] (?:[a-z0-9\-]* [a-z0-9])?\. [a-z]{2,6} $/ix', $domains[0]))
            exit($_LANG['ordererrordomaininvalid']);
        exit();
    }
    try
    {
        $orderSummaryTemplate = "/templates/" . WHMCS\View\Template::factory()->getName() . "/custom_ordersummary.tpl";
        $totals = calcPrice($pid,$configoptions,$pricing, $cycle);
        echo processSingleTemplate($orderSummaryTemplate, array( "producttotals" => $totals));
    }
    catch( Exception $e ) 
    {
    }
    exit();
}
$product = Product::find($pid);
$smarty->assign("product", $product);
$smarty->assign("pricing", $pricing);
$smarty->assign("configurableoptions", $configoptions);
$smarty->assign("customfields", $customfields);

$ca->setPageTitle($product->name);
$ca->setTemplate('custom_product');
$ca->output();