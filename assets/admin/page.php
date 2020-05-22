<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! current_user_can('manage_options') ) {
    wp_die( __( 'Sorry, you are not allowed to see this page.', 'qte-importer' ) );
}

$engine_repository = QTE_Importer_Engine_Repository::get_instance();

?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <?php if( $engines = $engine_repository->get_all_engines() ): ?>
    <form id="import-form" method="POST">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="wp-json-url"><?php _e( 'External wp-json url:', 'qte-importer' ); ?></label></th>
                    <td><input id="wp-json-url" class="regular-text" type="url" name="wp_json_url" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="engine"><?php _e( 'Import engine:'); ?></label></th>
                    <td>
                        <select id="engine" name="engine">
                            <option value=""><?php _e( 'Choose', 'qte-importer'); ?></option>
                            <?php foreach( $engines as $slug => $engine ): ?>
                                <option value="<?php echo $slug; ?>"><?php echo $engine->get_name(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button( __( 'Import' ) ); ?>
    </form>
    <?php else: ?>
        <p class="error"><?php _e( 'Missing engines.', 'qte-importer' ); ?></p>
    <?php endif; ?>
</div>
