<?php
/**
 * Plugin Name:       GB Splide
 * Plugin URI:        https://minhthe.net
 * Description:       Slider/Carousel for GenerateBlocks
 * Version:           1.0.2
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            MinhThe.net
 * Author URI:        https://minhthe.net
 * Text Domain:       generateblocks
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path(__FILE__) . 'github-updater.php';

new GitHub_Plugin_Updater( __FILE__, 'ryandever', 'gb-splide', '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' );

function create_block_gb_splide_block_init() {

	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}

	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', 'create_block_gb_splide_block_init' );

function gb_splide_enqueue_frontend_scripts() {
    if (is_admin()) return;

    wp_enqueue_script(
        'splide-js',
        plugin_dir_url(__FILE__) . 'libs/splide/splide.min.js',
        [],
        '4.1.3',
        true
    );

    wp_enqueue_style(
        'splide-css',
        plugin_dir_url(__FILE__) . 'libs/splide/splide.min.css',
        [],
        '4.1.3'
    );

    wp_enqueue_script(
        'gb-splide-master',
        plugin_dir_url(__FILE__) . 'public/master.js',
        ['splide-js'],
        filemtime(plugin_dir_path(__FILE__) . 'public/master.js'),
        true
    );
}
add_action('wp_enqueue_scripts', 'gb_splide_enqueue_frontend_scripts');