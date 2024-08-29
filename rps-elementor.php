<?php
/**
 * RealtyPress Elementor Dynamic Tags
 *
 * @link        sonofmedia.ca
 * @package     RealtyPress Elementor Dynamic Tags
 * @license     GPL-2.0+
 * @since:      0.1.0
 *
 * @wordpress-plugin
 * Plugin Name: RealtyPress Elementor Dynamic Tags
 * Description: A plugin for RealtyPress and Elementor. Exposing RPS fields as Dynamic Tags
 * Version:     0.1.0
 * Author:      Dylan McLeod
 * Author URI:  sonofmedia.ca
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: realtypress-elementor
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function register_site_dynamic_tag_group( $dynamic_tags_manager ) {

	$dynamic_tags_manager->register_group(
		'rps-elementor',
		[
			'title' => esc_html__( 'RealtyPress Listing Fields', 'rps-elementor-dynamic-tag' )
		]
	);

}
add_action( 'elementor/dynamic_tags/register', 'register_site_dynamic_tag_group' );

function register_new_dynamic_tags( $dynamic_tags_manager ) {

	require_once( __DIR__ . '/includes/dynamic-tags/realtypress-elementor-dynamic-tags.php' );

	$dynamic_tags_manager->register( new \RPS_Elementor_Dynamic_Tag() );
    $dynamic_tags_manager->register( new \RPS_Elementor_Dynamic_Tag__photos() );
}
add_action( 'elementor/dynamic_tags/register', 'register_new_dynamic_tags' );