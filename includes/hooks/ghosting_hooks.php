<?php
use WHMCS\View\Menu\Item as MenuItem;
add_hook('ClientAreaPrimarySidebar', 1, function(MenuItem $primarySidebar)
{
    if(null !== $primarySidebar->getChild('Service Details Actions'))
    {
            $primarySidebar->getChild('Service Details Actions')->removeChild('Custom Module Button Reboot');
    }
});
add_hook('AdminHomeWidgets', 1, function() 
{
    return new gHostingResourcesUsageWidget();
});
class gHostingResourcesUsageWidget extends \WHMCS\Module\AbstractWidget
{
    protected $title = 'gHosting server resources usage';
    protected $description = '';
    protected $weight = 150;
    protected $columns = 1;
    protected $cache = false;
    protected $cacheExpiry = 120;
    protected $requiredPermission = '';
    private function curlPost($url, $data)
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
    public function getData()
    {
        $data = 
        [
            'key' => SHARED_HOSTING_KEY,
            'action' => 'get_available_cpu_ram_hdd'
        ];
        $json = $this->curlPost('http://'.SHARED_HOSTING_IP.':4444/ghosting_api.php', $data);
        $resp_array = json_decode($json, true);
        return is_array($resp_array) ? $resp_array : $json;
    }

    public function generateOutput($data)
    {
        if(!is_array($data))
            return '<div align="center" style="margin:20px">'.$data.'</div>';
        
        $unit = 'MB';
    	if($data['allocated_hdd'] > 1000)
    	{
    		$data['allocated_hdd'] = $data['allocated_hdd'] / 1000;
    		$unit = 'GB';
    	}
    	if($data['allocated_hdd'] > 1000)
    	{
    		$data['allocated_hdd'] = $data['allocated_hdd'] / 1000;
    		$unit = 'TB';
    	}
    	$data['allocated_hdd'] = round($data['allocated_hdd'], 2).$unit;
    	
    	$unit = 'MB';
    	if($data['used_hdd'] > 1000)
    	{
    		$data['used_hdd'] = $data['used_hdd'] / 1000;
    		$unit = 'GB';
    	}
    	if($data['used_hdd'] > 1000)
    	{
    		$data['used_hdd'] = $data['used_hdd'] / 1000;
    		$unit = 'TB';
    	}
    	$data['used_hdd'] = round($data['used_hdd'], 2).$unit;
    	
    	$data['available_hdd'] = $data['available_hdd'] > 0 ? $data['available_hdd'] / 1000 : $data['available_hdd'];
    	$unit = 'MB';
    	if($data['available_hdd'] > 1000)
    	{
    		$data['available_hdd'] = $data['available_hdd'] / 1000;
    		$unit = 'GB';
    	}
    	if($data['available_hdd'] > 1000)
    	{
    		$data['available_hdd'] = $data['available_hdd'] / 1000;
    		$unit = 'TB';
    	}
    	$data['available_hdd'] = round($data['available_hdd'], 2).$unit;
        
        return '<div align="center" style="margin:20px">
        <table width="100%">
            <tr>
                <td><b>Total CPU : </b></td>
                <td align="right">'.$data['total_cpu'].'MHZ</td>
            </tr>
            <tr>
                <td><b>Used CPU : </b></td>
                <td align="right">'.$data['used_cpu'].'MHZ</td>
            </tr>
            <tr>
                <td><b>Available CPU : </b></td>
                <td align="right">'.$data['available_cpu'].'MHZ</td>
            </tr>
            <tr>
                <td><b>Total RAM : </b></td>
                <td align="right">'.$data['total_ram'].'MB</td>
            </tr>
            <tr>
                <td><b>Used RAM : </b></td>
                <td align="right">'.$data['used_ram'].'MB</td>
            </tr>
            <tr>
                <td><b>Available RAM : </b></td>
                <td align="right">'.$data['available_ram'].'MB</td>
            </tr>
            <tr>
                <td><b>Allocated HDD : </b></td>
                <td align="right">'.$data['allocated_hdd'].'</td>
            </tr>
            <tr>
                <td><b>Used HDD : </b></td>
                <td align="right">'.$data['used_hdd'].'</td>
            </tr>
            <tr>
                <td><b>Available HDD : </b></td>
                <td align="right">'.$data['available_hdd'].'</td>
            </tr>
        </table>
        </div>';
    }
}