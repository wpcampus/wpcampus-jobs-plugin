<?php
/**
 * Plugin Name:     WPCampus: Jobs
 * Plugin URI:      https://github.com/wpcampus/wpcampus-jobs-plugin
 * Description:     Manages data for the WPCampus jobs board.
 * Version:         1.0.0
 * Author:          WPCampus
 * Author URI:      https://wpcampus.org
 * Domain Path:     /languages
 *
 * @package         WPCampus_Jobs
 */

defined( 'ABSPATH' ) or die();

$plugin_dir = plugin_dir_path( __FILE__ );

require_once $plugin_dir . 'inc/class-wpcampus-jobs-global.php';

if ( is_admin() ) {
	require_once $plugin_dir . 'inc/class-wpcampus-jobs-admin.php';
} else {
	require_once $plugin_dir . 'inc/class-wpcampus-jobs-public.php';
}