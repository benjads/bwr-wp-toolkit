<?php

/**
 * ACTION: Register new meta box in the page editor
 */
function bwrt_add_template_box() {
    add_meta_box(
        'bwrt_template_box_id',
        'BWR Template',
        'bwrt_template_box_html',
        'page'
    );
}
add_action( 'add_meta_boxes', 'bwrt_add_template_box' );

/**
 * Get the HTML content for custom meta box
 *
 * @param $post WP_Post, passed by core
 */
function bwrt_template_box_html( WP_Post $post ) {
    $value = get_post_meta( $post->ID, '_bwrt_template', true );

    ?>
    <input type="checkbox" name="bwrt_template" id="bwrt_template" class="postbox" <?php checked($value, 'on') ?>
    <label for="bwrt_template">BWR Club Template Page</label>
    <input type="hidden" name="bwrt_template_hidden" value="0" />
    <?php
}

/**
 * ACTION: Save custom meta
 *
 * @param $post_id int Post ID, passed by core
 */
function bwrt_save_postdata( int $post_id ) {
    if ( array_key_exists( 'bwrt_template_hidden', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_bwrt_template',
            isset($_POST['bwrt_template']) ? $_POST['bwrt_template'] : 'off'
        );
    }
}
add_action( 'save_post', 'bwrt_save_postdata' );

/**
 * FILTER: Add template column to 'Pages'
 *
 * @param $defaults array Current default columns, passed by core
 * @return          array Updated default columns
 */
function bwrt_page_columns( array $defaults ): array {
    $defaults['bwrt_template'] = 'BWR Club Template';
    return $defaults;
}
add_filter( 'manage_pages_columns', 'bwrt_page_columns' );

/**
 * ACTION: Fill contents of custom column
 *
 * @param $column_name  string  Column being iterated
 * @param $id           int     Post IT
 */
function bwrt_page_column_view( string $column_name, int $id ) {
    if ( $column_name === 'bwrt_template' ) {
        $is_template = get_post_meta( $id, '_bwrt_template', true );
        echo $is_template === 'on' ? 'Yes' : 'No';
    }
}
add_action( 'manage_pages_custom_column', 'bwrt_page_column_view', 5, 2 );

/**
 * FILTER: Add add/remove template page bulk actions
 *
 * @param   array $bulk_actions     Current bulk actions, passed by core
 * @return  array                   Updated bulk actions
 */
function bwrt_register_bulk( array $bulk_actions ): array {
    $bulk_actions['bwrt_template_enable'] = 'Add to club template pages';
    $bulk_actions['bwrt_template_disable'] = 'Remove from club template pages';
    return $bulk_actions;
}
add_filter( 'bulk_actions-edit-page', 'bwrt_register_bulk' );

/**
 * FILTER: Perform bulk template enable/disable
 *
 * @param   string  $sendback   Redirect URL, passed by core
 * @param   string  $doaction   Bulk action, passed by core
 * @param   array   $items      Post IDs, passed by core
 * @return  string              Modified ``$sendback``
 */
function bwrt_handle_bulk( string $sendback, string $doaction, array $items ): string {
    if ( $doaction !== 'bwrt_template_enable' && $doaction !== 'bwrt_template_disable' ) {
        return $sendback;
    }

    $state = (bool) ($doaction === 'bwrt_template_enable');
    foreach ( $items as $post_id ) {
        update_post_meta(
            $post_id,
            '_bwrt_template',
            $state ? 'on' : 'off'
        );
    }

    $sendback = add_query_arg( 'bwrt_template_completed', $state ? 'true' : 'false', $sendback );
    $sendback = add_query_arg( 'bwrt_template_count', count( $items ), $sendback );
    return $sendback;
}
add_filter( 'handle_bulk_actions-edit-page', 'bwrt_handle_bulk', 10, 3 );

/**
 * ACTION: Admin notice for bulk action confirmation
 */
function bwrt_bulk_notice() {
    if ( empty( $_REQUEST['bwrt_template_completed'] ) ) {
        return;
    }

    $op = $_REQUEST['bwrt_template_completed'] == 'true' ? 'Added' : 'Removed';
    $count = intval( $_REQUEST['bwrt_template_count'] );

    printf( '<div class="notice notice-success">' .
        _n( '%s %s club template',
            '%s %s club templates',
            $count
        ) . '</div>', esc_attr( $op ), esc_attr( $count ) );
}
add_action( 'admin_notices', 'bwrt_bulk_notice' );