<?php
class GCC
{
	private $project;
	private $region;
	private $zone;
	private $client;
	private $compute;
	public function __construct($project, $region, $zone, $credentials)
	{
		$this->project = $project;
		$this->region = $region;
		$this->zone = $zone;
		$this->client = new Google_Client();
		$this->client->setHttpClient(new GuzzleHttp\Client(['verify' => false]));
		$this->client->setAuthConfig($credentials);
		$this->client->addScope(Google_Service_Compute::COMPUTE);
		$this->compute = new Google_Service_Compute($this->client);
	}
	public function create($name, $os, $cpu, $ram, $gpu, $hdd, $ip_address, $startup_script, $publickey)
	{
		try
		{
			$instance = new Google_Service_Compute_Instance();
			$instance->setName($name);
			$instance->setMachineType("projects/".$this->project."/zones/".$this->zone."/machineTypes/f1-micro");
			//$instance->setMachineType("projects/".$this->project."/zones/".$this->zone."/machineTypes/custom-$cpu-$ram");
			$disks = new Google_Service_Compute_AttachedDisk();
			$disks->setType("PERSISTENT");
			$disks->setBoot(true);
			$disks->setMode("READ_WRITE");
			$disks->setAutoDelete(true);
			$disks->setDeviceName($name);			
			$disksInitParams = new Google_Service_Compute_AttachedDiskInitializeParams();
			$disksInitParams->setSourceImage($os);
			$disksInitParams->setDiskType("projects/".$this->project."/zones/".$this->zone."/diskTypes/pd-standard");
			$disksInitParams->setDiskSizeGb(10);
			//$disksInitParams->setDiskSizeGb($hdd);
			$disks->setInitializeParams($disksInitParams);		
			$instance->setDisks([$disks]);
			$networkInterface = new Google_Service_Compute_NetworkInterface();
			$networkInterface->setSubnetwork("projects/".$this->project."/regions/".$this->region."/subnetworks/default");
			$accessConfig = new Google_Service_Compute_AccessConfig();
			$accessConfig->setName("External NAT");
			$accessConfig->setType("ONE_TO_ONE_NAT");
			$accessConfig->setNetworkTier("PREMIUM");
			$accessConfig->setNatIP($ip_address);
			$networkInterface->setAccessConfigs([$accessConfig]);
			$instance->setNetworkInterfaces([$networkInterface]);
			$scheduling = new Google_Service_Compute_Scheduling();
			if(is_array($gpu))
			{
			    $accelerator = new Google_Service_Compute_AcceleratorConfig();
			    $accelerator->setAcceleratorType("projects/".$this->project."/zones/".$this->zone."/acceleratorTypes/".str_replace(" ", "-", strtolower($gpu["name"])));
			    $accelerator->setAcceleratorCount($gpu["count"]);
			    $instance->setGuestAccelerators([$accelerator]);
			    $scheduling->setOnHostMaintenance("TERMINATE");
			}
			else
			    $scheduling->setOnHostMaintenance("MIGRATE");
			$metadataArray = array();
			$metadata = new Google_Service_Compute_Metadata();
			$authMetadata = new Google_Service_Compute_MetadataItems();
			$authMetadata->setKey("ssh-keys");
			$authMetadata->setValue($publickey);
			$metadataArray[] = $authMetadata;
			if(!empty($startup_script))
			{
			    $autorunMetadata = new Google_Service_Compute_MetadataItems();
			    $autorunMetadata->setKey("startup-script");
			    $autorunMetadata->setValue($startup_script);
			    $metadataArray[] = $autorunMetadata;
			}
			$metadata->setItems($metadataArray);
			$instance->setMetadata($metadata);
			$instance->setScheduling($scheduling);
			return $this->compute->instances->insert($this->project, $this->zone, $instance);
		}
		catch(Exception $e)
		{
			$error = new stdClass();
            $error->error = $e->getMessage();
        	return $error;
		}
	}
	public function createIpAddress($name)
	{
	    try
		{
	        $address = new Google_Service_Compute_Address();
		    $address->setName($name);
		    $address->setAddressType("EXTERNAL");
			$this->compute->addresses->insert($this->project, $this->region, $address);
		    $r = $this->compute->addresses->get($this->project, $this->region, $name);
		    $tm = time();
		    while(empty($r->getAddress()))
		    {
		        if(time() - $tm > 30)
		        {
		            $error = new stdClass();
                    $error->error = "Static ip address \"$name\" created, but not obtained.";
                    return $error;
		        }
		        sleep(2);
		        $r = $this->compute->addresses->get($this->project, $this->region, $name);
		    }
		    return $r->getAddress();
		}
		catch(Exception $e)
		{
			$error = new stdClass();
            $error->error = $e->getMessage();
        	return $error;
		}
	}
	public function deleteIpAddress($name)
	{
	    try
		{
	        $this->compute->addresses->delete($this->project, $this->region, $name);
		    return true;
		}
		catch(Exception $e)
		{
			$error = new stdClass();
            $error->error = $e->getMessage();
        	return $error;
		}
	}
	public function stop($name)
	{
		try
		{
			return $this->compute->instances->stop($this->project, $this->zone, $name);
		}
		catch(Exception $e)
		{
			$error = new stdClass();
            $error->error = $e->getMessage();
        	return $error;
		}
	}
	public function start($name)
	{
		try
		{
			return $this->compute->instances->start($this->project, $this->zone, $name);
		}
		catch(Exception $e)
		{
			$error = new stdClass();
            $error->error = $e->getMessage();
        	return $error;
		}
	}
	public function reset($name)
	{
		try
		{
			return $this->compute->instances->reset($this->project, $this->zone, $name);
		}
		catch(Exception $e)
		{
			$error = new stdClass();
            $error->error = $e->getMessage();
        	return $error;
		}
	}
	public function delete($name)
	{
		try
		{
			$this->compute->instances->delete($this->project, $this->zone, $name);
			return $this->deleteIpAddress($name);
		}
		catch(Exception $e)
		{
			$error = new stdClass();
            $error->error = $e->getMessage();
        	return $error;
		}
	}
	public function get($name)
	{
		try
		{
			return $this->compute->instances->get($this->project, $this->zone, $name);
		}
		catch(Exception $e)
		{
			$error = new stdClass();
            $error->error = $e->getMessage();
        	return $error;
		}
	}
}