<?php
    /**
     * Trigger this file on Plugin uninstall
     *
     * @package AxeptioWPPlugin
     */
    if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        die;
    }
    // Clear Database stored data
    $collection = get_posts( array( 'post_type' => 'xplomate', 'numberposts' => -1 ) );
    foreach( $collection as $data ) {
        wp_delete_post( $data->ID, true );
    }
    // Access the database via SQL
    global $wpdb;
    $wpdb->query( "DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)" );
    $wpdb->query( "DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts)" );
    $wpdb->query( "DELETE FROM wp_posts WHERE post_type = 'xplomate'" );