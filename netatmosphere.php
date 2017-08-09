<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 
/*
 *	Plugin Name: NetAtmoSphere
 *	Plugin URI: http://www.teni.at/
 *	Description: Fetch hourly and display weather data coming from www.netatmo.com
 *	Version: 2.0.1
 *	Author: Martin Teni
 *	Author URI: http://www.teni.at/
 *	License: GPL2
 */

if (!defined('NAS_PLUGIN_ROOT')) {
	define('NAS_PLUGIN_ROOT', dirname(__FILE__) . '/');
	define('NAS_PLUGIN_NAME', basename(dirname(__FILE__)));
	define('NAS_PLUGIN_VERSION', '2.0.1');
	define('NAS_DB_VERSION', '0.2');
}

require_once (NAS_PLUGIN_ROOT . '/lib/Netatmo-API/src/Netatmo/autoload.php');

function nas_autoload($class_name) {
	static $classMap = array (
        // base folder
        'NAS_Cron'              => 'inc/class-nas-cron.php',
		'NAS_Plugin'  		    => 'inc/class-nas-plugin.php',
        'NetAtmo_Client_Wrapper'=> 'inc/class-netatmo-client-wrapper.php',
        // commons
        'NAS_Device_Locations'  => 'inc/Commons/enum-device-locations.php',
        'NAS_Measure_Units'     => 'inc/Commons/enum-measure-units.php',
        // data
        'NAS_Devices_Adapter'   => 'inc/Data/class-adapter-devices.php',
        'NAS_Data_Adapter'      => 'inc/Data/class-adapter-data.php',
        'NAS_Data_Cell'         => 'inc/Data/class-nas-data-cell.php',
        'NAS_Data_Row'          => 'inc/Data/class-nas-data-row.php',
        'NAS_Data_Table'        => 'inc/Data/class-nas-data-table.php',
        // shortcodes
        'NAS_Shortcode_Base'        => 'inc/Shortcodes/class-shortcode-base.php',
        'NAS_Shortcode_Data'        => 'inc/Shortcodes/class-shortcode-data.php',
        'NAS_Shortcode_Devices'     => 'inc/Shortcodes/class-shortcode-devices.php',
        'NAS_Shortcode_Schedules'   => 'inc/Shortcodes/class-shortcode-schedules.php',
        // tools
		'NAS_DB_Tool'           => 'inc/Tools/class-tool-db.php',
        'NAS_Options'           => 'inc/Tools/class-tool-options.php',
        'NAS_Synch_Data'        => 'inc/Tools/class-synch-data.php',
        'NAS_Synch_Devices'     => 'inc/Tools/class-synch-devices.php',
        // widgets
        'NAS_Widget'    => 'inc/Widgets/class-widget.php',
        // admin
		'NAS_Admin_Plugin'      => 'admin/class-admin-nas-plugin.php',
		'NAS_Admin_Menu'  		=> 'admin/class-admin-menu.php',
		'NAS_Admin_Options' 	=> 'admin/class-admin-options.php',
	);
	
	// add references to lib
	//$classMap['parseCSV'] = 'lib/parsecsv/parsecsv.lib.php';
	//$classMap['Encoding'] = 'lib/forceutf8/Encoding.php';


	if (isset($classMap[$class_name])) {
		require_once(NAS_PLUGIN_ROOT . $classMap[$class_name]);
	}
}

// register a function for autoloading required classes
spl_autoload_register('nas_autoload');

/**
 * De-/Activation / Uninstall must be global function 
 */
function nas_plugin_activation() {
	NAS_Plugin::pluginActivation();
}
function nas_plugin_deactivation() {
	NAS_Plugin::pluginDeactivation();
}
function nas_plugin_uninstall() {
	NAS_Plugin::pluginUninstall();
}

/** 
 * Same for cron callbacks, must be global functions
 */
require_once( NAS_PLUGIN_ROOT . 'inc/class-nas-cron.php' );

// activation and deactivation hooks for this plugin (need to stay in this file, doesnt work in nas-plugin.php)
register_activation_hook   ( __FILE__, 'nas_plugin_activation');
register_deactivation_hook ( __FILE__, 'nas_plugin_deactivation');
// or better use this approach with uninstall.php?
// https://codex.wordpress.org/Function_Reference/register_uninstall_hook 
register_uninstall_hook    ( __FILE__, 'nas_plugin_uninstall');

// instantiate the class
NAS_Plugin::getInstance();

?>