<?php 
if( !defined("WHMCS") ) 
{
    exit( "This file cannot be accessed directly" );
}
use WHMCS\Database\Capsule;
use phpseclib\Crypt\RSA;
include_once(__DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "GCC.php");
function gcc_randomPassword($length = 8)
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}
function gcc_curlPost($url, $data)
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
function gcc_MetaData()
{
    return array( "DisplayName" => "Google Cloud Compute");
}
function gcc_ClientArea($params)
{
    $containerInfo = Capsule::table('tblcontainers')->where('service_id', $params['serviceid'])->orderBy('id', 'desc')->first();
    $container_id = $containerInfo ? $containerInfo->container_id : '';
    $sub_hostname = $containerInfo ? substr($containerInfo->container_id, 0, 12) : '';
    $hostname = str_replace('www.', '', $_SERVER['HTTP_HOST']);
    return array( "overrideDisplayTitle" => ucfirst($params["domain"]), "tabOverviewReplacementTemplate" => "overview.tpl", 'vars' => array("username" => $params["username"], "password" => $params["password"], "sub_hostname" => $sub_hostname, "container_id" => $container_id, "hostname" => $hostname));
}
function gcc_ClientAreaAllowedFunctions()
{
    return array( "DownloadPrivateKey" );
}
function gcc_ClientAreaCustomButtonArray() 
{
    $buttonarray = array("Reboot" => "reboot");
	return $buttonarray;
}
function gcc_AdminCustomButtonArray() 
{
    $buttonarray = array("Reboot" => "reboot");
	return $buttonarray;
}
function gcc_CreateAccount($params)
{
    if($params["producttype"] == "hostingaccount")
    {
        $container_db_id = 0;
        $ip_address = '';
        $ssh_port = '';
        $sftp_port = '';
        $rmysql_port = '';
        $containerInfo = Capsule::table('tblcontainers')->where('service_id', 0)->orderBy('id', 'asc')->first();
        if($containerInfo)
        {
            $container_db_id = $containerInfo->id;
            $ip_address = $containerInfo->ip_address;
            $ssh_port = $containerInfo->ssh_port;
            $sftp_port = $containerInfo->sftp_port;
            $rmysql_port = $containerInfo->rmysql_port;
        }
        else
        {
            $containerInfo = Capsule::table('tblcontainers')->orderBy('id', 'desc')->first();
            if($containerInfo)
            {
                $ip_long = ip2long($containerInfo->ip_address);
                $ip_long++;
                $ip_address = long2ip($ip_long);
                $ssh_port = $containerInfo->ssh_port + 1;
                $sftp_port = $containerInfo->sftp_port + 1;
                $rmysql_port = $containerInfo->rmysql_port + 1;
            }
            else
            {
                $ip_address = '172.18.0.3';
                $ssh_port = 30001;
                $sftp_port = 40001;
                $rmysql_port = 50001;
            }
        }
        if(ip2long($ip_address) > ip2long('172.18.255.254'))
            return 'Invalid IP Address : '.$ip_address.'. IP Address must be between 172.18.0.1 - 172.18.255.255';
        $data = 
        [
            'key' => SHARED_HOSTING_KEY,
            'action' => 'create',
			'service_id' => $params['serviceid'],
            'domain' => $params['domain'],
            'user' => $params['username'],
            'password' => $params['password'],
            'email' => $params['clientsdetails']['email'],
            'fullname' => $params['clientsdetails']['fullname'],
            'address' => $params['clientsdetails']['address1'].', '.$params['clientsdetails']['address2'],', '.$params['clientsdetails']['city'],', '.$params['clientsdetails']['countryname'],
            'phone' => $params['clientsdetails']['phonecc'].$params['clientsdetails']['phonenumber'],
            'post_code' => $params['clientsdetails']['postcode'],
            'ip_address' => $ip_address,
            'ssh_port' => $ssh_port,
            'sftp_port' => $sftp_port,
            'rmysql_port' => $rmysql_port,
            'cpu_limit' => $params['configoptions']['CPU'],
            'ram_limit' => $params['configoptions']['RAM'],
            'hdd_limit' => $params['configoptions']['HDD'],
            'net_limit' => 0
        ];
        $json = gcc_curlPost('http://'.SHARED_HOSTING_IP.':4444/ghosting_api.php', $data);
        $response = json_decode($json, true);
        if(is_array($response) && !empty($response['container_id']) && $response['status'] == 'success')
        {
            if($container_db_id)
                Capsule::table('tblcontainers')->where('id', $container_db_id)->update(['service_id' => $params['serviceid'], 'container_id' => $response['container_id']]);
            else
                Capsule::table('tblcontainers')->insert(['service_id' => $params['serviceid'], 'container_id' => $response['container_id'], 'ip_address' => $ip_address, 'ssh_port' => $ssh_port, 'sftp_port' => $sftp_port, 'rmysql_port' => $rmysql_port]);
            return "success";
        }
        else if(is_array($response) && $response['status'] == 'error')
            return $response['message'];
        return $json;
    }
    return "Unknown error!";
}

function gcc_SuspendAccount($params)
{
    if($params["producttype"] == "hostingaccount")
    {
        $containerInfo = Capsule::table('tblcontainers')->where('service_id', $params['serviceid'])->orderBy('id', 'desc')->first();
        if(!$containerInfo)
            return 'Service not found!';
        $data = 
        [
            'key' => SHARED_HOSTING_KEY,
            'action' => 'suspend',
            'container_id' => $containerInfo->container_id
        ];
        $json = gcc_curlPost('http://'.SHARED_HOSTING_IP.':4444/ghosting_api.php', $data);
        $response = json_decode($json, true);
        if(is_array($response) && $response['status'] == 'success')
            return "success";
        else if(is_array($response) && $response['status'] == 'error')
            return $response['message'];
        return $json;
    }
    return "Unknown error!";
}

function gcc_UnsuspendAccount($params)
{
    if($params["producttype"] == "hostingaccount")
    {
        $containerInfo = Capsule::table('tblcontainers')->where('service_id', $params['serviceid'])->orderBy('id', 'desc')->first();
        if(!$containerInfo)
            return 'Service not found!';
        $data = 
        [
            'key' => SHARED_HOSTING_KEY,
            'action' => 'unsuspend',
            'container_id' => $containerInfo->container_id
        ];
        $json = gcc_curlPost('http://'.SHARED_HOSTING_IP.':4444/ghosting_api.php', $data);
        $response = json_decode($json, true);
        if(is_array($response) && $response['status'] == 'success')
            return "success";
        else if(is_array($response) && $response['status'] == 'error')
            return $response['message'];
        return $json;
    }
    return "Unknown error!";
}

function gcc_TerminateAccount($params)
{
    if($params["producttype"] == "hostingaccount")
    {
        $containerInfo = Capsule::table('tblcontainers')->where('service_id', $params['serviceid'])->orderBy('id', 'desc')->first();
        if(!$containerInfo)
            return 'Service not found!';
        $data = 
        [
            'key' => SHARED_HOSTING_KEY,
            'action' => 'terminate',
            'container_id' => $containerInfo->container_id
        ];
        $json = gcc_curlPost('http://'.SHARED_HOSTING_IP.':4444/ghosting_api.php', $data);
        $response = json_decode($json, true);
        if(is_array($response) && $response['status'] == 'success')
        {
            Capsule::table('tblcontainers')->where('service_id', $params['serviceid'])->update(['service_id' => 0]);
            return "success";
        }
        else if(is_array($response) && $response['status'] == 'error')
            return $response['message'];
        return $json;
    }
    return "Unknown error!";
}
function gcc_reboot($params) 
{
	if($params["producttype"] == "hostingaccount")
    {
        $containerInfo = Capsule::table('tblcontainers')->where('service_id', $params['serviceid'])->orderBy('id', 'desc')->first();
        if(!$containerInfo)
            return 'Service not found!';
        $data = 
        [
            'key' => SHARED_HOSTING_KEY,
            'action' => 'reboot',
            'container_id' => $containerInfo->container_id
        ];
        $json = gcc_curlPost('http://'.SHARED_HOSTING_IP.':4444/ghosting_api.php', $data);
        $response = json_decode($json, true);
        if(is_array($response) && $response['status'] == 'success')
            return "success";
        else if(is_array($response) && $response['status'] == 'error')
            return $response['message'];
        return $json;
    }
    return "Unknown error!";
}
function gcc_ChangePassword($params)
{
    if($params["producttype"] == "hostingaccount")
    {
        $containerInfo = Capsule::table('tblcontainers')->where('service_id', $params['serviceid'])->orderBy('id', 'desc')->first();
        if(!$containerInfo)
            return 'Service not found!';
        $data = 
        [
            'key' => SHARED_HOSTING_KEY,
            'action' => 'changepassword',
            'container_id' => $containerInfo->container_id,
            'user' => $params['username'],
            'password' => $params['password']
        ];
        $json = gcc_curlPost('http://'.SHARED_HOSTING_IP.':4444/ghosting_api.php', $data);
        $response = json_decode($json, true);
        if(is_array($response) && $response['status'] == 'success')
            return "success";
        else if(is_array($response) && $response['status'] == 'error')
            return $response['message'];
        return $json;
    }
    return "Unknown error!";
}

function gcc_ChangePackage($params)
{
    if($params["producttype"] == "hostingaccount")
    {
        $containerInfo = Capsule::table('tblcontainers')->where('service_id', $params['serviceid'])->orderBy('id', 'desc')->first();
        if(!$containerInfo)
            return 'Service not found!';
        $data = 
        [
            'key' => SHARED_HOSTING_KEY,
            'action' => 'changepackage',
            'container_id' => $containerInfo->container_id,
            'cpu_limit' => $params['configoptions']['CPU'],
            'ram_limit' => $params['configoptions']['RAM'],
            'hdd_limit' => $params['configoptions']['HDD'],
            'net_limit' => 0
        ];
        $json = gcc_curlPost('http://'.SHARED_HOSTING_IP.':4444/ghosting_api.php', $data);
        $response = json_decode($json, true);
        if(is_array($response) && $response['status'] == 'success')
            return "success";
        else if(is_array($response) && $response['status'] == 'error')
            return $response['message'];
        return $json;
    }
    return "Unknown error!";
}