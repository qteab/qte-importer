<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


class QTE_Importer_Media_Importer
{
    protected $url_map = [];

    protected $attachment_id_map = [];

    /**
     * @param string $url
     * @return int|WP_Error
     */
    public function import_media( string $url ) {
        $upload_dir = wp_upload_dir();

        $image_data = file_get_contents( $url );

        $filename = basename( $url );

        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        file_put_contents( $file, $image_data );

        $wp_filetype = wp_check_filetype( $filename, null );

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name( $filename ),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $file );

        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
        }

        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }

    public function import_media_from_content( string &$content ) {
        preg_match_all( "/(<img)(.*?)(\/>)/", $content, $matches, PREG_SET_ORDER );

        foreach( $matches as $match ) {
            $element = $match[0];

            preg_match( '/(?:src=")(.*?)(?:")/', $element, $src );
            $src = $src[1];

            preg_match( '/(?:wp-image-)(\d+)/', $element, $attachment_id );
            $attachment_id = $attachment_id[1] ?? "";

            if ( ! isset( $this->url_map[$src] ) ) {
                $new_attachment_id = $this->import_media( $src );

                if ( $attachment_id ) {
                    $this->attachment_id_map[$attachment_id] = $new_attachment_id;
                }

                $this->url_map[$src] = wp_get_attachment_url( $new_attachment_id );
            }

            if ( $attachment_id ) {
                $content = str_replace("wp-image-{$attachment_id}", "wp-image-{$this->attachment_id_map[$attachment_id]}", $content);
            }

            $content = str_replace($src, $this->url_map[$src], $content);
        }
    }
}
