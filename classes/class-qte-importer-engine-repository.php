<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class QTE_Importer_Engine_Repository {

    /**
     * @var QTE_Importer_Engine_Interface[]
     */
    private $engines = [];

    /**
     * @var self
     */
    private static $instance = null;

    public function __construct() {
        $this->engines['basic'] = new QTE_Importer_Engine_Basic();
    }

    /**
     * @return QTE_Importer_Engine_Repository
     */
    public static function get_instance() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $slug
     * @param QTE_Importer_Engine_Interface $engine
     * @throws Exception
     */
    public function add_engine( string $slug, QTE_Importer_Engine_Interface $engine ) {
        if ( isset( $this->engines[$slug] ) ) {
            throw new Exception( __( 'Slug already in use!', 'qte-importer' ) );
        }

        $this->engines[$slug] = $engine;
    }

    /**
     * @param string $slug
     */
    public function remove_engine( string $slug ) {
        unset( $this->engines[$slug] );
    }

    /**
     * @return array|QTE_Importer_Engine_Interface[]
     */
    public function get_all_engines(): array {
        return $this->engines;
    }

    /**
     * @param $slug
     * @return QTE_Importer_Engine_Interface
     * @throws Exception
     */
    public function get_engine( $slug ) {
        if ( ! isset( $this->engines[$slug] ) ) {
            throw new Exception( sprintf( __( 'Could not find engine %s', 'qte-importer' ), $slug ) );
        }

        return $this->engines[$slug];
    }
}
