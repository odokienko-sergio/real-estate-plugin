<?php
/**
 * Plugin Name: RGBC Real Estate Plugin
 * Description: Initialization of a new post type and taxonomy
 * Plugin URI:  https://my-plugins.com
 * Author URI:  https://odokiienko-plugins.com
 * Author:      Serhii Odokiienko
 * Version:     1.1
 * License:     GPLv2 or later
 * Text Domain: real_estate_object
 * Domain Path: /lang
 */

/* перевірка */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/* версія плагіна, підключення інших файлів з класами */
const RGBC_RE_VERSION = '1.1';
define( 'RGBC_RE_PATH', plugin_dir_path( __FILE__ ) . '/' );
define( 'RGBC_RE_URL', plugin_dir_url(__FILE__) . '/' );
define( 'RGBC_RE_DOMAIN', plugin_basename( dirname( __FILE__ ) ) . '/lang' );
define( 'RGBC_RE_INC_PATH', RGBC_RE_PATH . 'inc/' );
require_once RGBC_RE_INC_PATH . 'class-real-estate-object-cpt.php';
/*if(!class_exists('Real_Estate_Object_Cpt')) {
    require RGBC_RE_PATH . 'class-real-estate-object-cpt.php';
};*/

/* підключаєм додадкові класи з новими темплейтами */
if ( ! class_exists( 'Gamajo_Template_Loader' ) ) {
	require RGBC_RE_PATH . 'inc/class-gamajo-template-loader.php';
}

require RGBC_RE_PATH . 'inc/class-rgbc-re.php';
require RGBC_RE_PATH . 'inc/class-rgbc-real-estate-template-loader.php';
require RGBC_RE_PATH . 'inc/class-real-estate-shortcodes.php';
require RGBC_RE_PATH . 'inc/class-real-estate-filter-widget.php';
require RGBC_RE_PATH . 'inc/class-real-estate-bookingform.php ';
/************************************************************* */
/* увесь клас RGBC_RE повинен бути в окремому файлі */



if ( class_exists( 'RGBC_RE' ) ) {
	$RGBC_RE = new RGBC_RE();
	$RGBC_RE->register(); /* викликаєм функціонал */
}

register_activation_hook( __FILE__, array(
	$RGBC_RE,
	'activation',
) );
register_deactivation_hook( __FILE__, array(
	$RGBC_RE,
	'deactivation',
) );









