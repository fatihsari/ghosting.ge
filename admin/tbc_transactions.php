<?php 
define("ADMINAREA", true);
require("../init.php");
use WHMCS\Database\Capsule;
$aInt = new WHMCS\Admin("List Clients");
$aInt->title = ucfirst('Transactions');
$aInt->sidebar = "clients";
$aInt->icon = "clients";

if(isset($_POST['ref_amount']) && isset($_POST['ref_trans_id']) && !empty($_POST['ref_trans_id']) && floatval($_POST['ref_amount']))
{
    $ref_trans_id = $_POST['ref_trans_id'];
    $ref_amount = floatval($_POST['ref_amount']) * 100.0;
    
    $url = 'https://ecommerce.ufc.ge:18443/ecomm2/MerchantHandler';
    $params = "command=k&trans_id=".urlencode($ref_trans_id)."&amount=$ref_amount";
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
	curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
	$resp = curl_exec($ch);
	preg_match("/RESULT\s*:\s*([^\n]+)/", $resp, $m);
	$result = trim($m[1]);
	curl_close($ch);
	if($result == 'OK')
	{
	    mysql_query("UPDATE transaction_info SET status=100 WHERE transaction_id='$ref_trans_id'");
		$ref_result_msg = 'The refund operation was successful.';
	}
	else
		$ref_result_msg = 'An error was reported during the refund operation.';
}

ob_start();
?>
<script>
    var EXPANDED = 0;
    function expand(id)
    {
        var p = document.getElementById('t'+EXPANDED);
        var n = document.getElementById('t'+id);
        if(p)
            p.style.display = "none";
        if(n)
            n.style.display = "table-row";
            EXPANDED=id;
    }
    <? if(isset($ref_result_msg) && !empty($ref_result_msg)) { ?>
        $(function() { alert('<?=$ref_result_msg?>'); });
    <? } ?>
</script>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Date</th>
      <th scope="col">Invoice#</th>
      <th scope="col">Client</th>
      <th scope="col">Phone</th>
      <th scope="col">Product</th>
      <th scope="col">Status</th>
    </tr>
  </thead>
  <tbody>
      <?
            $res=mysql_query("SELECT * FROM transaction_info GROUP BY invoice_id ORDER BY date DESC");
            while($invoice = mysql_fetch_object($res))
            {
                $res2 = mysql_query("SELECT * FROM tblinvoiceitems WHERE invoiceid='".$invoice->invoice_id."' LIMIT 1");
                $invoice_item_data = mysql_fetch_object($res2);
                $res2 = mysql_query("SELECT * FROM tblinvoices WHERE id='".$invoice->invoice_id."' LIMIT 1");
                $invoice_data = mysql_fetch_object($res2);
                if(!$invoice_data) continue;
                $res2 = mysql_query("SELECT * FROM tblclients WHERE id='".$invoice_data->userid."' LIMIT 1");
                $client_data = mysql_fetch_object($res2);
                
      ?>
    <tr onclick="expand(<?=$invoice->invoice_id?>)" style="cursor:pointer;<? if($invoice_data->status=='Paid') echo 'background-color:green; color:white'; else if($invoice_data->status=='Unpaid') echo 'background-color:yellow'; else  echo 'background-color:red; color:white'?>">
      <td><?=$invoice_data->date?></td>
      <td><?=$invoice->invoice_id?></td>
      <td><?=$client_data->firstname.' '.$client_data->lastname.' ('.$client_data->companyname.')';?></td>
      <td><?=$client_data->phonenumber?></td>
      <td><?=$invoice_item_data->description?></td>
      <td><?=$invoice_data->status;?></td>
    </tr>
    <tr id="t<?=$invoice->invoice_id?>" style="display:none">
      <td colspan="6" style="padding:20px" align="center">
          
         <table class="table" align="center">
  <thead>
    <tr>
      <th scope="col">Date</th>
      <th scope="col">Transaction#</th>
      <th scope="col">Check</th>
      <th scope="col">Refund</th>
      <th scope="col">Status</th>
    </tr>
  </thead>
  <tbody>
      <?
        $res3=mysql_query("SELECT * FROM transaction_info WHERE invoice_id='".$invoice->invoice_id."' ORDER BY date DESC");
            while($trans = mysql_fetch_object($res3))
            {
      ?>
      <tr style="<? if($trans->status==1) echo 'background-color:green;color:white'; else if($trans->status==100) echo 'background-color:blue;color:white'; else if($trans->status==2) echo 'background-color:red;color:white';?>">
      <td scope="col"><?=date("Y-m-d",$trans->date)?></td>
      <td scope="col"><?=$trans->transaction_id?></td>
      <td scope="col" width="240">
          <a href="https://ghosting.ge/tbc.php?trans_id=<?=urlencode($trans->transaction_id)?>&check" target="_blank"> 
          <input style="width: 100px;float: left;" type="button" class="form-control" value="Check"/>
          </a>
          <a href="https://ghosting.ge/tbc.php?trans_id=<?=urlencode($trans->transaction_id)?>" target="_blank"> 
          <input style="width: 100px;float: left;margin-left: 10px;" type="button" class="form-control" value="Retry"/>
          </a>
          </td>
          <td scope="col">
              <? if($trans->status==1) { ?>
              <form action"" method="post" onsubmit="return confirm('Do you really want to make a refund?');">
                  <input type="text" name="ref_amount" value="<?=$invoice_data->total?>" style="width:80px; color:black"/>
                  <input type="hidden" name="ref_trans_id" value="<?=$trans->transaction_id?>"/>
                  <input style="background:blue; color:white; width: 140px; display: inline-block;" type="submit" class="form-control" value="Refund"/>
              </form>
          <?}?>
              </td>
      <td scope="col">
          <?
          if($trans->status==1) echo 'Completed';
          else if($trans->status==2) echo 'Abotred';
          else if($trans->status==100) echo 'Refunded';
          else echo 'Incompleted';
          ?>
          </td>
    </tr>
    <?
            }
    ?>
      </tbody>
      </table>
          
          
          </td>
    </tr>
    <?
    }
    ?>
  </tbody>
</table>
<?php
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();