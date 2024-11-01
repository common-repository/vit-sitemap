<?php
/**
 * Plugin Name: VIT Sitemap
 * Plugin URI: -- 
 * Description: VIT Sitemap plugin allows you to effortlessly create both HTML and XML sitemaps.
 * Version: 1.0
 * Author: Vidushi Infotech
 * Author URI: https://vidushiinfotech.com/
 * Text Domain: --
 * License: --
 *
 * @since 0.1
 *
 * @package sitemap
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define( 'VITSITEMAP_PLUGIN_FILE', __FILE__ );

/**
 * Loads the action plugin
 */
require_once dirname( VITSITEMAP_PLUGIN_FILE ) . '/includes/sitemap_Main.php';

vitsitemap_Main::instance();

register_activation_hook( VITSITEMAP_PLUGIN_FILE, array( 'vitsitemap_Main', 'activate' ) );

register_deactivation_hook( VITSITEMAP_PLUGIN_FILE, array( 'vitsitemap_Main', 'deactivate' ) );

register_uninstall_hook( VITSITEMAP_PLUGIN_FILE, array( 'vitsitemap_Main', 'uninstall' ) ); 
