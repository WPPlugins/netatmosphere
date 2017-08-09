<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Synch_Devices {
	public function refresh() {
		
        $html = '';
        
		try {
            $client = NetAtmo_Client_Wrapper::getInstance()->client;
			$devicesData = $client->getData(null, false);
			
            //NAS_Plugin::debugFile($devicesData, __FILE__, __LINE__);
            
			if(isset($devicesData)) {
				$devices = $devicesData["devices"];
				
				// clear table after successfully retrieved the new device list
				NAS_Devices_Adapter::clear();
				
				foreach($devices as $device) {

                    $this->refreshCacheDevice($device);
					$this->refreshCacheDeviceTypes($device['_id'], $device['data_type']);
					
					foreach($device["modules"] as $module) {

                        $this->refreshCacheDevice($module, $device['_id']);
						$this->refreshCacheDeviceTypes($module['_id'], $module['data_type']);
					}
				}
			}
		}
		catch(\Exception $ex)
		{
			$html .= "<strong>ERROR during refreshing the cache for netatmo devices!</strong>";
            NAS_Plugin::debugFile($ex, __FILE__, __LINE__);
		}
        
        return $html;
	}
	
    
    protected function refreshCacheDeviceTypes($id_module, $dataTypes) {
		global $wpdb;

		foreach($dataTypes as $dataType) {
																					
			if(isset($id_module) && isset($dataType)) {
				$wpdb->insert(NAS_DB_Tool::GetDevicesTypesTableName(), 
					array( 
							'id_module' => $id_module,
							'module_meter_type' => $dataType
					)
				);
			}
		}
	}
	protected function refreshCacheDevice($module, $id_device = null) {
		global $wpdb;

        $date_setup = null; $coord_lat = null; $coord_lng = null;
        
		if($id_device === null) {
			// means its the base station
			$id_device = $module['_id'];
			$coord_lat = $module['place']['location'][1];
			$coord_lng = $module['place']['location'][0];
		}
		$id_module = $module['_id'];
		$type = $module['type'];
		$module_name = $module['module_name'];
		
		//$date_setup = $module['date_setup']['sec'];
		$date_setup_asdate = NAS_Plugin::utc2date($date_setup);
		$meter_location = $this->translateTypeToInOutDoor($type);
        $last_refresh = NAS_Plugin::utc2date(date('Y-m-d H:i:s'), 'UTC');
        
        NAS_Plugin::debugFile("---------------");
        NAS_Plugin::debugFile(date('Y-m-d H:i:s'));
        NAS_Plugin::debugFile($last_refresh);
		
		$wpdb->insert(NAS_DB_Tool::GetDevicesTableName(), 
			array( 
					'id_device' => $id_device,
					'id_module' => $id_module,
					'meter_location' => $meter_location,
                    //'last_refresh' => gmdate('Y-m-d H:i:s'),
                    'last_refresh' => $last_refresh,
					'module_type' => $type,
					'module_name' => $module_name,
					'date_setup' => $date_setup_asdate,
					'coord_latitude' => $coord_lat,
					'coord_longitude' => $coord_lng
			)
		);
	}
    
	private function translateTypeToInOutDoor($type) {
		switch(strtolower($type)) {
			case "namain":          // NAMain :     for the base station
			case "namodule4":		// NAModule4 :  for the additionnal indoor module
				return NAS_Device_Locations::Indoor;
				break;
			
			case "namodule1":		// NAModule1 :  for the outdoor module
			case "namodule2":		// NAModule2 :  for the wind gauge module
			case "namodule3":		// NAModule3 :  for the rain gauge module
				return NAS_Device_Locations::Outdoor;
				break;
				
			default:
				return null;
				break;
		}
	}
}

?>