<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


class QTE_Importer_Fetcher
{
    protected $url;

    protected $total_posts;

    protected $total_pages;

    protected $current_page = 1;

    protected $current_posts;

    public function __construct( string $url ) {
        if ( ! $this::validate_url( $url ) ) {
            throw new Exception( __( 'Invalid url!', 'qte-importer' ) );
        }

        $this->url = $url;

        $this->current_posts = $this->fetch_page( 1, true );
    }

    public static function validate_url( $url ): bool {
        if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
            return false;
        }

        if ( ! strpos( $url, 'wp-json' ) ) {
            return false;
        }

        return true;
    }

    private function fetch_page( int $page = 1, bool $set_metadata = false ): array {
        $url = add_query_arg( array(
            'page' => $page,
            'per_page' => 10,
        ), $this->url );

        $response = wp_remote_get( $url );

        if ( $set_metadata ) {
            $this->total_posts = wp_remote_retrieve_header( $response,'x-wp-total' );
            $this->total_pages = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );
        }

        $posts = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! isset( $posts[0]['type'] ) && ! isset( $posts[0]['title'] ) ) {
            throw new Exception( __( 'Invalid source.', 'qte-importer' ) );
        }

        return $posts;
    }

    public function have_posts() {
        if ( $this->current_posts ) {
            return true;
        }

        if ( $this->current_page < $this->total_pages ) {
            $this->current_page++;
            $this->current_posts = $this->fetch_page( $this->current_page );

            return true;
        }

        return false;
    }

    public function get_post() {
        return array_shift( $this->current_posts );
    }
}
