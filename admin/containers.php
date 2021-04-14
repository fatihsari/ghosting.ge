<?php 
define("ADMINAREA", true);
require("../init.php");
use WHMCS\Database\Capsule;
$view = isset($_GET['view']) && ($_GET['view']==='containers' || $_GET['view']==='domains') ? $_GET['view'] : 'containers';


$aInt = new WHMCS\Admin("List Clients");
$aInt->title = ucfirst($view);
$aInt->sidebar = "clients";
$aInt->icon = "clients";

function curlPost($url, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt ($ch, CURLOPT_POST, 1);
    $response = curl_exec($ch);
    if($response === false)
    {
        $response = curl_error($ch);
    }
    curl_close($ch);
    return $response;
}
$data = 
[
    'key' => SHARED_HOSTING_KEY,
    'action' => 'get_containers',
    'delete_container_id' => isset($_GET['delete_container_id']) ? $_GET['delete_container_id'] : '',
    'domain' => isset($_GET['domain']) ? $_GET['domain'] : ''
];
$json = curlPost('http://'.SHARED_HOSTING_IP.':4444/ghosting_api.php', $data);
$response = json_decode($json, true);
$response = is_array($response) ? $response : $json;
ob_start();
if(!is_array($response)) 
    echo $response;
else if($response['status'] === 'error')
    echo 'Error : '.$response['message'];
else
{
    $total_cpu = 0;
    $total_cpu_limit_percent = 0;
    $total_ram_limit = 0;
    $total_hdd_limit = 0;
    $total_hdd_used = 0;
    $total_net_limit = 0;
    for($i=0; $i < count($response['containers']); $i++)
    {
        $total_cpu_limit += $response['containers'][$i]['cpu_limit'];
        $total_cpu_limit_percent += $response['containers'][$i]['cpu_limit_percent'];
        $total_ram_limit += $response['containers'][$i]['ram_limit'];
        $total_hdd_limit += $response['containers'][$i]['hdd_limit'];
        $total_hdd_used += $response['containers'][$i]['hdd_used'];
        $total_net_limit += $response['containers'][$i]['net_limit'];
        
        $data = Capsule::table('tblhosting')->where('tblhosting.id', $response['containers'][$i]['service_id'])->leftJoin('tblclients', 'tblhosting.userid', '=', 'tblclients.id')->leftJoin('tblproducts', 'tblhosting.packageid', '=', 'tblproducts.id')->select('tblclients.id as user_id', 'tblclients.firstname', 'tblclients.lastname', 'tblproducts.name as product_name', 'tblhosting.domain as product_domain')->first();
        if($data)
        {
            $response['containers'][$i]['user_name'] = '<a href="clientssummary.php?userid='.$data->user_id.'">'.$data->firstname.' '.$data->lastname.'</a>';
            $response['containers'][$i]['product_name'] = '<a href="clientsservices.php?userid='.$data->user_id.'&id='.$response['containers'][$i]['service_id'].'">'.$data->product_name.' - '.$data->product_domain.'</a>';
            $response['containers'][$i]['status'] = $response['containers'][$i]['status'] ? 'Active' : 'Suspended';
        }
        else if($response['containers'][$i]['service_id'] == -1)
        {
            $response['containers'][$i]['user_name'] = 'Root';
            $response['containers'][$i]['product_name'] = 'Root';
            $response['containers'][$i]['status'] = 'Active';
        }
        else
        {
            $response['containers'][$i]['user_name'] = 'Deleted';
            $response['containers'][$i]['product_name'] = 'Deleted';
            $response['containers'][$i]['status'] = 'Deleted';
        }
        $response['containers'][$i]['hdd_limit_number'] = $response['containers'][$i]['hdd_limit'];
        if(!$response['containers'][$i]['hdd_limit'] || $response['containers'][$i]['hdd_limit'] == '0')
			$response['containers'][$i]['hdd_limit'] = 'Unlimited';
		else
		{
    		$unit = 'MB';
    		if($response['containers'][$i]['hdd_limit'] > 1000)
    		{
    			$response['containers'][$i]['hdd_limit'] = $response['containers'][$i]['hdd_limit'] / 1000;
    			$unit = 'GB';
    		}
    		if($response['containers'][$i]['hdd_limit'] > 1000)
    		{
    			$response['containers'][$i]['hdd_limit'] = $response['containers'][$i]['hdd_limit'] / 1000;
    			$unit = 'TB';
    		}
    		$response['containers'][$i]['hdd_limit'] = round($response['containers'][$i]['hdd_limit'], 2).$unit;
		}
		$response['containers'][$i]['hdd_used_number'] = $response['containers'][$i]['hdd_used'];
		if(!$response['containers'][$i]['hdd_used'] || $response['containers'][$i]['hdd_used'] == '0')
			$response['containers'][$i]['hdd_used'] = 'Unlimited';
		else
		{
    		$unit = 'MB';
    		if($response['containers'][$i]['hdd_used'] > 1000)
    		{
    			$response['containers'][$i]['hdd_used'] = $response['containers'][$i]['hdd_used'] / 1000;
    			$unit = 'GB';
    		}
    		if($response['containers'][$i]['hdd_used'] > 1000)
    		{
    			$response['containers'][$i]['hdd_used'] = $response['containers'][$i]['hdd_used'] / 1000;
    			$unit = 'TB';
    		}
    		$response['containers'][$i]['hdd_used'] = round($response['containers'][$i]['hdd_used'], 2).$unit;
		}
		
		if(!$response['containers'][$i]['net_limit'] || $response['containers'][$i]['net_limit'] == '0')
			$response['containers'][$i]['net_limit'] = 'Unlimited';
		else
		{
    		$unit = 'MB';
    		if($response['containers'][$i]['net_limit'] > 1000)
    		{
    			$response['containers'][$i]['net_limit'] = $response['containers'][$i]['net_limit'] / 1000;
    			$unit = 'GB';
    		}
    		if($response['containers'][$i]['net_limit'] > 1000)
    		{
    			$response['containers'][$i]['net_limit'] = $response['containers'][$i]['net_limit'] / 1000;
    			$unit = 'TB';
    		}
    		$response['containers'][$i]['net_limit'] = round($response['containers'][$i]['net_limit'], 2).$unit;
		}
    }
    
    echo '<style>.red_td td{background-color:red !important; color:white}.yellow_td td{background-color:yellow !important; color:black}</style><div class="tablebg"><table id="sortabletbl0" class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3"><tbody>';
    
    if($view === 'containers')
    {
        echo '<tr><th>Container ID</th><th>Root Password</th><th>Client</th><th>Service</th><th>IP Address</th><th>Domains</th><th>CPU Limit</th><th>RAM Limit</th><th>HDD Limit</th><th>NET Limit</th><th>status</th><th>Action</th></tr>';
        foreach($response['containers'] as $container)
        {
            echo '<tr class="'.($container['status'] != 'Active' ? 'red_td' : ($container['hdd_used_number'] > $container['hdd_limit_number'] ? 'yellow_td' : '')).'"><td>'.$container['container_id'].'</td><td>'.$container['root_password'].'</td><td>'.$container['user_name'].'</td><td>'.$container['product_name'].'</td><td align="center">'.$container['ip_address'].'</td><td align="center"><a href="containers.php?view=domains&container_id='.$container['container_id'].'">'.count($container['domains']).'</a></td><td align="center">'.$container['cpu_limit'].'MHZ ('.round($container['cpu_limit_percent'], 2).'%)</td><td align="center">'.$container['ram_limit'].'MB</td><td align="center">'.$container['hdd_used'].'/'.$container['hdd_limit'].'</td><td align="center">'.$container['net_limit'].'</td><td align="center">'.$container['status'].'</td><td align="center">'.($container['service_id'] == -1 ? '' : '<a href="containers.php?view='.$view.'&delete_container_id='.$container['container_id'].'" onclick="return confirm(\'Are you sure you want to delete this container?\');">Delete</a>').'</td></tr>';
        }
        
        
        if(!$total_hdd_limit || $total_hdd_limit == '0')
			$total_hdd_limit = 'Unlimited';
		else
		{
    		$unit = 'MB';
    		if($total_hdd_limit > 1000)
    		{
    			$total_hdd_limit = $total_hdd_limit / 1000;
    			$unit = 'GB';
    		}
    		if($total_hdd_limit > 1000)
    		{
    			$total_hdd_limit = $total_hdd_limit / 1000;
    			$unit = 'TB';
    		}
    		$total_hdd_limit = round($total_hdd_limit, 2).$unit;
		}
		
		if(!$total_hdd_used || $total_hdd_used == '0')
			$total_hdd_used = 'Unlimited';
		else
		{
    		$unit = 'MB';
    		if($total_hdd_used > 1000)
    		{
    			$total_hdd_used = $total_hdd_used / 1000;
    			$unit = 'GB';
    		}
    		if($total_hdd_used > 1000)
    		{
    			$total_hdd_used = $total_hdd_used / 1000;
    			$unit = 'TB';
    		}
    		$total_hdd_used = round($total_hdd_used, 2).$unit;
		}
		
		if(!$total_net_limit || $total_net_limit == '0')
			$total_net_limit = 'Unlimited';
		else
		{
    		$unit = 'MB';
    		if($total_net_limit > 1000)
    		{
    			$total_net_limit = $total_net_limit / 1000;
    			$unit = 'GB';
    		}
    		if($total_net_limit > 1000)
    		{
    			$total_net_limit = $total_net_limit / 1000;
    			$unit = 'TB';
    		}
    		$total_net_limit = round($total_net_limit, 2).$unit;
		}
        echo '<tr><td colspan="6"><b>TOTALS:</b></td><td align="center">'.$total_cpu_limit.'MHZ ('.round($total_cpu_limit_percent, 2).'%)</td><td align="center">'.$total_ram_limit.'MB</td><td align="center">'.$total_hdd_used.'/'.$total_hdd_limit.'</td><td align="center">'.$total_net_limit.'</td><td colspan="2"></td></tr>';
    }
    elseif($view === 'domains')
    {
        echo '<tr><th>Container ID</th><th>Root Password</th><th>Client</th><th>Service</th><th>IP Address</th><th>Domain</th><th>SSL Status</th><th>Action</th></tr>';
        foreach($response['containers'] as $container)
        {
            if(isset($_GET['container_id']) && $_GET['container_id'] != $container['container_id'])
                continue;
            foreach($container['domains'] as $domain)
            {
                echo '<tr class="'.($domain['ssl_status'] ? '' : 'red_td').'"><td>'.$container['container_id'].'</td><td>'.$container['root_password'].'</td><td>'.$container['user_name'].'</td><td>'.$container['product_name'].'</td><td>'.$container['ip_address'].'</td><td>'.$domain['domain'].'</td><td align="center">'.($domain['ssl_status'] ? 'OK' : 'Pending').'</td><td align="center">'.($container['service_id'] == -1 ? '' : '<a href="containers.php?view='.$view.'&delete_container_id='.$container['container_id'].'&domain='.$domain['domain'].'" onclick="return confirm(\'Are you sure you want to delete this container?\');">Delete</a>').'</td></tr>';
            }
        }
        
    }
    echo '</tbody></table></div>';
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();