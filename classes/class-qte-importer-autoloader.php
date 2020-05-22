<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class QTE_Importer_Autoloader {

    public static function autoloader( $class_name ) {
        if ( false !== strpos( $class_name, 'QTE' ) ) {
            $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
            $class_file = 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';
            require_once $classes_dir . $class_file;
        }
    }

    public static function register_autoloader() {
        spl_autoload_register( array( static::class, 'autoloader' ) );
    }
}
