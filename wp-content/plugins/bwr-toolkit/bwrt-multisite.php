<?php

/**
 * ACTION: Add templates to new child/club site
 *
 * @param $site WP_Site New site, passed by core
 */
function bwrt_default_pages( WP_Site $site ) {
    // Get template pages
    $templates = bwrt_get_default_pages();

    if ( empty($templates) ) {
        return;
    }

    switch_to_blog( $site->id );

    // Create pages on new site
    foreach ( $templates as $template ) {
        if ( is_object($template) && $template instanceof WP_Post ) {

            wp_insert_post(
                array(
                    'post_title'     => $template->post_title,
                    'post_name'      => $template->post_name,
                    'post_content'   => $template->post_content,
                    'post_status'    => 'publish',
                    'post_author'    => $template->post_author,
                    'post_type'      => 'page',
                    'menu_order'     => 1,
                    'comment_status' => 'closed',
                    'ping_status'    => 'closed',
                )
            );
        }
    }

    restore_current_blog();
}
add_action( 'wp_insert_site', 'bwrt_default_pages' );

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