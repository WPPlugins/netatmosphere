<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_DB_Tool {
	
	protected static function getTableNamePrefix() {
		global $wpdb;
		return $wpdb->prefix . "netatmosphere_"; 
	}
	protected static function getViewNamePrefix() {
		return "v_" . self::getTableNamePrefix();
	}
	protected static function getCollation() {
		global $wpdb;
		return $wpdb->get_charset_collate();
	}
	public static function executeQuery( $sql ) {
		global $wpdb;
		try {
			if(is_array($sql)) {
				foreach($sql as $s) {
					$wpdb->query( $s );
				}
			} else {
				$wpdb->query( $sql );
			}
		}
		catch(Exception $ex)
		{
			NAS_Plugin::debugBar($ex, 'netatmosphere - executeQuerys()', __FILE__, __LINE__);
		}
	}
	
	
	/* 
	 * TABLE / VIEW NAMES -------------------------
	 */
	public static function GetDataTableName() {
		$tableNamePrefix = self::getTableNamePrefix();
		return "${tableNamePrefix}data";
	}
	public static function GetDevicesTableName() {
		$tableNamePrefix = self::getTableNamePrefix();
		return "${tableNamePrefix}devices";
	}
	public static function GetDevicesTypesTableName() {
		$tableNamePrefix = self::getTableNamePrefix();
		return "${tableNamePrefix}devices_types";
	}
	
	public static function GetDataDetailsViewName() {
		$viewNamePrefix = self::getViewNamePrefix();
		return "${viewNamePrefix}data_details";
	}
	public static function GetDataOverviewViewName() {
		$viewNamePrefix = self::getViewNamePrefix();
		return "${viewNamePrefix}data_overview";
	}
	public static function GetDevicesDetailsViewName() {
		$viewNamePrefix = self::getViewNamePrefix();
		return "${viewNamePrefix}devices_details";
	}

	
	/* 
	 * DEFAULT DATA -------------------------
	 */
	public static function InsertDefaultData() {
		//global $wpdb;
		
		//$wpdb->replace(self::GetGenderTableName(), array("ID" => 1, "Code" => "m", "Description" => "maennlich"));
	}

	/*
	 * DROPS's
	 */
    public static function DropDataTable() {
        $sql = self::GetDropDataTable();
        self::executeQuery( $sql );
    }
    public static function GetDropDataTable() {
        $sql = array();
		
		$sql[] = "DROP TABLE " . self::GetDataTableName() . ";";
		
		return $sql;
    }
    public static function DropDeviceTable() {
        $sql = self::GetDropDeviceTable();
        self::executeQuery( $sql );
    }
    public static function GetDropDeviceTable() {
        $sql = array();
		
		$sql[] = "DROP TABLE " . self::GetDevicesTypesTableName() . ";";
		$sql[] = "DROP TABLE " . self::GetDevicesTableName() . ";";
		
		return $sql;
    }
	public static function DropAllTables() {
		// create abstract weather data table if not existing
		$sql = self::GetAllDropTables();
		if(is_array($sql)) {
			foreach($sql as $s) {
				self::executeQuery( $s );
			}
		} else {
			self::executeQuery( $sql );
		}
	}
	public static function GetAllDropTables() {
		$sql = array();
		
		$sql[] = "DROP TABLE " . self::GetDataTableName() . ";";
		$sql[] = "DROP TABLE " . self::GetDevicesTypesTableName() . ";";
		$sql[] = "DROP TABLE " . self::GetDevicesTableName() . ";";
		
		return $sql;
	}
	public static function GetDropTableSql($tableName) {
		return "DROP TABLE " . $tableName . ";";
	}
	public static function DropAllViews() {
		// create abstract weather data table if not existing
		$sql = self::GetAllDropViews();
		if(is_array($sql)) {
			foreach($sql as $s) {
				self::executeQuery( $s );
			}
		} else {
			self::executeQuery( $sql );
		}
	}
	public static function GetAllDropViews() {
		$sql = array();
		
		$sql[] = self::GetDropViewSql( self::GetDataDetailsViewName() );
		$sql[] = self::GetDropViewSql( self::GetDataOverviewViewName() );
		$sql[] = self::GetDropViewSql( self::GetDevicesDetailsViewName() );
		
		return $sql;
	}
	public static function GetDropViewSql($viewName) {
		return "DROP VIEW " . $viewName . ";";
	}

	/*
	 * TRUNCATE's
	 */
	public static function TruncateAllTables() {
		$sql = self::GetAllTruncateTables();
		if(is_array($sql)) {
			foreach($sql as $s) {
				self::executeQuery($s);
			}
		} else {
			self::executeQuery( $sql );
		}
	}
	public static function GetAllTruncateTables() {
		$sql = array();
		
		$sql[] = self::GetTruncateTableSql( self::GetDataTableName() );
		$sql[] = self::GetTruncateTableSql( self::GetDevicesTypesTableName() );
		$sql[] = self::GetTruncateTableSql( self::GetDevicesTableName() );
		
		return $sql;
	}
	public static function GetTruncateTableSql($tableName) {
		return "TRUNCATE TABLE " . $tableName . ";";
	}
	
	/*
	 * CREATE's
	 */
	public static function CreateAllTables() {
		// include for dbDelta
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		try {
		
			// create abstract weather data table if not existing
			$sql = self::GetAllCreateTables();
			if(is_array($sql)) {
				foreach($sql as $s) {
					dbDelta($s);
				}
			} else {
				dbDelta( $sql );
			}
		} catch(\Exception $ex) {
			NAS_Plugin::debugFile($ex, __FILE__, __LINE__);
		}
	}
	public static function GetAllCreateTables() {
		$sql = array();
		
		$sql[] = self::GetCreateTable4Data();
		$sql[] = self::GetCreateTable4Devices();
		$sql[] = self::GetCreateTable4DevicesTypes();
		
		return $sql;
	}	
	public static function CreateAllViews() {
		$sql = self::GetAllCreateViews();
		if(is_array($sql)) {
			foreach($sql as $s) {
				self::executeQuery($s);
			}
		} else {
			self::executeQuery( $sql );
		}
	}
	public static function GetAllCreateViews() {
		$sql = array();
		
		$sql[] = self::GetCreateViewDevicesDetails();
		$sql[] = self::GetCreateViewDataDetails();
		$sql[] = self::GetCreateViewDataOverview();
		
		return $sql;
	}
	
	/* 
	 * VIEWS -------------------------
	 */
	public static function GetCreateViewDevicesDetails() {
		$tnDevices = self::GetDevicesTableName();
		$tnDevicesTypes = self::GetDevicesTypesTableName();
		
		$vnDeviceDetails = self::GetDevicesDetailsViewName(); 
		$sql = "CREATE OR REPLACE VIEW $vnDeviceDetails AS 
					SELECT d.*, t.module_meter_type FROM $tnDevices as d, $tnDevicesTypes as t
					WHERE d.id_module = t.id_module;";
				
		return $sql;
	}
	public static function GetCreateViewDataDetails() {
		$tnData = self::GetDataTableName();
		$vnDeviceDetails = self::GetDevicesDetailsViewName(); 
		
		$vnDataDetails = self::GetDataDetailsViewName(); 
        
        // was before: SELECT v1.*, t1.time_stamp, t1.value_category, t1.value 
		$sql = "CREATE OR REPLACE VIEW $vnDataDetails AS 
					SELECT v1.id_device, v1.id_module, v1.meter_location, t.time_stamp, t.value, t1.value_category
						FROM $tnData as t1, $vnDeviceDetails as v1
						WHERE t1.module_id = v1.id_module 
						AND t1.value_category = v1.module_meter_type;";
        
		return $sql;
	}
	public static function GetCreateViewDataOverview() {
		$vnDataDetails = self::GetDataDetailsViewName(); 
		$vnDataOverview = self::GetDataOverviewViewName(); 
		/** 
		 * NOTE: would be the ideal query, but subqueries are not allowed for VIEWs, unfortunatelly :(
		 $sql = "CREATE OR REPLACE VIEW $v AS 
					SELECT T1.*, DataOverview.* FROM $t2 as T1 
					INNER JOIN 
						(SELECT module_id, value_category, max(value) as max_val, min(value) as min_val, max(time_stamp) as max_time, min(time_stamp) as min_time, count(*) as cnt 
							FROM $t1
							GROUP BY module_id, value_category) as DataOverview 
						ON DataOverview.module_id = T1.id_module 
						AND DataOverview.value_category = T1.module_meter_type; ";
		 */
		$sql = "CREATE OR REPLACE VIEW $vnDataOverview AS 
				SELECT id, id_device, id_module, last_refresh, module_type, module_meter_type, meter_location, module_name, date_setup, value_category, max(value) as max_val, min(value) as min_val, max(time_stamp) as max_time, min(time_stamp) as min_time, count(*) as cnt 
					FROM $vnDataDetails
					GROUP BY id, id_device, id_module, last_refresh, module_type, module_meter_type, meter_location, module_name, date_setup, value_category; ";
				
		return $sql;
	}
	
	/* 
	 * TABLES -------------------------
	 */

	public static function GetCreateTable4Data() {
		$table_name = self::GetDataTableName();
		$charset_collate = self::getCollation();

		$sql = "CREATE TABLE $table_name (
			id int NOT NULL AUTO_INCREMENT,
			time_stamp timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
			module_id varchar(55) NOT NULL COMMENT 'ID of the sensor module',
			value_category varchar(55) NOT NULL,
			value FLOAT NULL,
			UNIQUE KEY id (id),
			INDEX idx_time_stamp (time_stamp, module_id, value_category, value) USING BTREE
		) $charset_collate;";
		
		return $sql;
	}
	
	public static function GetCreateTable4Devices() {
		$tableName = self::GetDevicesTableName();
		$charset_collate = self::getCollation();

		$sql = "CREATE TABLE $tableName (
			id int NOT NULL AUTO_INCREMENT,
			id_device varchar(55) NOT NULL COMMENT 'Base station ID',
			id_module varchar(55) NOT NULL COMMENT 'Module ID',
			last_refresh timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
			module_type varchar(20) NOT NULL COMMENT 'Module Type such as: NAMain, NAModule1, ...',
			meter_location varchar(15) DEFAULT 'outdoor' NOT NULL,
			module_name varchar(55) NULL COMMENT 'Readable name of module',
			date_setup timestamp NULL COMMENT 'UTC of setup',
			coord_latitude float NULL COMMENT 'Coordinates: Latitude',
			coord_longitude float NULL COMMENT 'Coordinates: Longitude',
			owned BOOLEAN DEFAULT true NULL COMMENT 'Device owned by me or a favorite device',
			UNIQUE KEY id (id)
		) $charset_collate;";
		
		
		return $sql;
	}
	
	public static function GetCreateTable4DevicesTypes() {
		$tableName = self::GetDevicesTypesTableName();
		$charset_collate = self::getCollation();
		
		$sql = "CREATE TABLE $tableName (
			id int NOT NULL AUTO_INCREMENT,
			id_module varchar(55) NOT NULL COMMENT 'Module ID',
			module_meter_type varchar(55) NOT NULL COMMENT 'Such as Temperature, Noise, Humidity, ...',
			UNIQUE KEY id (id)
		) $charset_collate;";
		
		return $sql;
	}
	
}

?>