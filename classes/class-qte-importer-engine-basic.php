<?php

if ( ! defined( 'ABSPATH') ) {
    exit();
}


class QTE_Importer_Engine_Basic implements QTE_Importer_Engine_Interface
{

    public function import_post( array $data ) {

        $content = $data['content']['rendered'];

        $media_importer = new QTE_Importer_Media_Importer();

        $media_importer->import_media_from_content( $content );

        $post_id = wp_insert_post( array(
            'post_type' => $data['type'],
            'post_title' => $data['title']['rendered'],
            'post_date' => $data['date'],
            'post_modified' => $data['modified'],
            'post_status' => $data['status'],
            'post_content' => $content,
        ) );

        return $post_id;
    }

    public function get_name(): string {
        return __( 'Basic', 'qte-importer' );
    }
}
