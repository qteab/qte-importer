<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


interface QTE_Importer_Engine_Interface {

    /**
     * @param array $data
     * @return int|WP_Error
     */
    public function import_post( array $data );

    /**
     * @return string
     */
    public function get_name(): string;
}
