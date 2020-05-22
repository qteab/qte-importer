<?php
/**
 * Plugin Name: QTE Importer
 * Description: This plugin can be used to import content from other Wordpress sites via the wp-json api.
 * Version: 1.0.0
 * Author: QTE Development AB
 * Author URI: https://qte.se
 */

require_once plugin_dir_path( __FILE__ ) . '/classes/class-qte-importer-autoloader.php';

QTE_Importer_Autoloader::register_autoloader();

QTE_Importer::get_instance();
