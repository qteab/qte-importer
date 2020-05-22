<?php

if ( ! defined('ABSPATH') ) {
    exit();
}


class QTE_Importer
{
    private static $instance;

    public $admin_page;

    public function __construct() {
        $this->admin_page = new QTE_Importer_Admin_Page();
    }

    public static function get_instance(): self {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
