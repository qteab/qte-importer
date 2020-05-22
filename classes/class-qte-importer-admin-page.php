<?php

if ( ! defined('ABSPATH') ) {
    exit();
}


class QTE_Importer_Admin_Page
{
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
    }

    public function register_admin_page() {
        $hookname = add_menu_page(
            __( 'QTE Importer', 'qte-importer' ),
            __( 'QTE Importer', 'qte-importer' ),
            'manage_options',
            'qte_importer',
            array( $this, 'page_content' ),
            'dashicons-update-alt',
            90
        );

        add_action( 'load-' . $hookname, array( $this, 'pre_page_load' ) );
    }

    public function page_content() {
        require_once plugin_dir_path( __FILE__ ) . '../assets/admin/page.php';
    }

    public function pre_page_load() {
        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            return;
        }

        $engine_repository = QTE_Importer_Engine_Repository::get_instance();

        try {
            $engine = $engine_repository->get_engine( $_POST['engine'] );
            $fetcher = new QTE_Importer_Fetcher( $_POST['wp_json_url'] );
        } catch ( Exception $exception ) {
            add_action( 'admin_notices', function () use ( $exception ) {
                $class = 'notice notice-error';
                $message = sprintf( __( 'Import failed with the following error: %s', 'qte-importer' ), $exception->getMessage() );

                printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
            } );

            return;
        }

        $imported_ids = [];

        if ( $fetcher->have_posts() ) {
            while ( $fetcher->have_posts() ) {
                $imported_ids[] = $engine->import_post( $fetcher->get_post() );
            }
        }

        $errors = array_filter( $imported_ids, function( $post_id ) {
            return is_wp_error( $post_id );
        } );

        if ( $errors ) {
            add_action( 'admin_notices', function () use ( $errors, &$imported_ids ) {
                $class = 'notice notice-warning';
                $message = sprintf( __( 'Imported %s posts, %s failed. See errors below!', 'qte-importer' ), count( $imported_ids ), count( $errors ) );
                $error_messages = array_map( function( WP_Error $error ) {
                    return $error->get_error_message();
                }, $errors );

                printf(
                    '<div class="%1$s"><p>%2$s</p><pre>%3$s</pre></pr></or></div>',
                    esc_attr( $class ),
                    esc_html( $message ),
                    esc_html( implode( '\n', $error_messages ) ) );
            } );

            return;
        }


        add_action( 'admin_notices', function () use ( $imported_ids ) {
            $class = 'notice notice-success';
            $message = sprintf( __( 'Imported %s posts!', 'qte-importer' ), count( $imported_ids ) );

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        } );

        return;
    }

}
