<?php

/**
 * ACTION: Add templates to new child/club site
 *
 * @param $site WP_Site New site, passed by core
 * @param array $args New site args, passed by core
 */
function bwrt_default_pages( WP_Site $site, array $args ) {
    // Get template pages
    $templates = bwrt_get_default_pages();

    if ( empty($templates) ) {
        return;
    }

    switch_to_blog( $site->id );

    // Create pages on new site
    foreach ( $templates as $template ) {
        if ( is_object($template) && $template instanceof WP_Post ) {

            $result = wp_insert_post(
                array(
                    'post_title'     => $template->post_title,
                    'post_name'      => $template->post_name,
                    'post_content'   => $template->post_content,
                    'post_status'    => 'publish',
                    'post_author'    => $args['user_id'],
                    'post_type'      => 'page',
                    'menu_order'     => 1,
                    'comment_status' => 'closed',
                    'ping_status'    => 'closed',
                )
            );

            if ( is_wp_error( $result ) ) {
                error_log( 'Error adding BWR Template: ' . print_r( $result, true ) );
            }
        }
    }

    // Delete WP Sample Page
    $default_page = get_page_by_title( 'Sample Page' );
    wp_delete_post( $default_page->ID );

    restore_current_blog();
}
add_action( 'wp_initialize_site', 'bwrt_default_pages', 10, 2 );

/**
 * ACTION: Include a message in the "Add New Site" page
 */
function bwrt_site_new_message() {
    $templates = bwrt_get_default_pages();

    ?>
    <h3>BWR Club Templates</h3>
    <p>This new club site will include <b><?php echo count($templates) ?></b> template page(s).
    To change the club site templates, toggle <em>BWR Template Page</em> in the page editor.</p>
    <?php
}
add_action( 'network_site_new_form', 'bwrt_site_new_message' );

/**
 * Get an array of BWR club template pages
 *
 * @return array templates
 */
function bwrt_get_default_pages(): array {
    $args = array(
        'post_type'     => 'page',
        'meta_query'    => array(
            array(
                'key'       => '_bwrt_template',
                'value'     => 'on',
                'compare'   => '=',
            )
        ),
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        return $query->posts;
    } else {
        return array();
    }
}