<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Shortcode_Data extends NAS_Shortcode_Base {
	protected $mode = 'latest';
    protected $data = null;
    protected $module = null;
    protected $location = NAS_Device_Locations::Outdoor;
    
	public function __constructor() {
        /*$this->mode = 'latest'; 
        $this->data = null; 
        $this->module = null; 
        $this->location = NAS_Device_Locations::Outdoor;*/
	}
	
	public function init($attributes) {

        if(isset($attributes)) {
            if(isset($attributes['mode'])) {
                $this->mode = strtolower($attributes['mode']);
            }		
            if(isset($attributes['data'])) {
                $this->data = strtolower($attributes['data']);
            }
            if(isset($attributes['module'])) {
                $this->module = strtolower($attributes['module']);
            }
            if(isset($attributes['location'])) {
                $this->location = strtolower($attributes['location']);
            }
        }
	}
	public function render() {
        $html = "";
        if($this->mode == 'latest') {
            $html .= $this->renderLatest();
        } 
        elseif($this->mode = 'overview') {
            $html .= $this->renderOverview();
        }
        elseif($this->mode = 'device') {
            $html .= $this->renderDevice();
        }    
        return $html;
	}
    public function renderExample() {
        return "[" . NAS_Plugin::$shortcode_data . "]";
    }
    public function renderOptions() {
        $attr['mode'] = array( 'latest' );
        $attr['location'] = array('indoor', 'outdoor');
        
        return $this->array2htmlList($attr);
    }
    
    protected function renderLatest() {
        $latest = NAS_Data_Adapter::getLatest($this->location);
		
		$html = "<h2>" . __('Latest measurements', 'netatmosphere') . "</h2>";

        if(isset($latest) && count($latest) > 0) { 
            $html .= "<div class='table-wrapper'>
                        <table>
                            <thead>
                                <tr>";
			
			foreach($latest as $key => $row) {
				$html .= "<th>" . $row['value_category'] . "</th>";
			}
			$html .= "</tr></thead><tbody><tr>";
			
			foreach($latest as $key => $row) {
				$html .= "<td>" . $row['value'] . $row['value_unit'] . "</td>";
			}
			$html .= "</tbody></table></div>";

        } else { 
            $html .= "<p>" . __("No data records cached!", 'netatmosphere') . "</p>";
        }
        
        return $html;
    }
    protected function renderOverview() {
        return $this->renderNotImplemented(__CLASS__, __FUNCTION__);
    }
    protected function renderDevice() {
        return $this->renderNotImplemented(__CLASS__, __FUNCTION__);
    }
}

?>