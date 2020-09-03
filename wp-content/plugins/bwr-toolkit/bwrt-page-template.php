<?php

/**
 * ACTION: Register new meta box in the page editor
 */
function bwrt_add_template_box() {
    add_meta_box(
        'bwrt_template_box_id',
        'BWR Club Template',
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
    <label for="bwrt_template">BWR Template Page</label>
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