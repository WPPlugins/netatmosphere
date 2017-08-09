<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * 
 */
class NAS_Options {
    private static $instance = NULL;
	
    protected $isFahrenheit = false; 
    protected $ismmHg = false; 
    
   /**
	* static method for getting the instance of this singleton object
	*
	* @return NAS_Options
	*/
	public static function getInstance() {

		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
    
    
    private function __construct() {
        $options = get_option("nas_admin_options_display");
		
		if(isset($options)) {
            $this->isFahrenheit = ( isset( $options['temperature_unit']) && $options['temperature_unit'] == NAS_Measure_Units::TEMPERATURE_FAHRENHEIT );
            $this->ismmHg = ( isset( $options['pressure_unit']) && $options['pressure_unit'] == NAS_Measure_Units::PRESSURE_MMHG );
        }
    }
    
    public static function GetTemperatureUnitNames() {
        $a = array();
        $a[] = NAS_Measure_Units::TEMPERATURE_CELSIUS;
        $a[] = NAS_Measure_Units::TEMPERATURE_FAHRENHEIT;
        return $a;
    }
    public static function GetPressureUnitNames() {
        $a = array();
        $a[] = NAS_Measure_Units::PRESSURE_HPA;
        $a[] = NAS_Measure_Units::PRESSURE_MMHG;
        return $a;
    }
    public static function GetTimeZones() {
        return DateTimeZone::listIdentifiers();
    }
    public static function GetTimeZone() {
        return get_option('timezone_string');
    }
    
    
    
    
    public function IsFahrenheit() {
        return $this->isFahrenheit;
    }
    public function IsmmHg() {
        return $this->ismmHg;
    }
}

?>