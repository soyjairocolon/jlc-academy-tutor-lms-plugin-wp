<?php

/**
 * Plugin Name: JLC Academy
 * Plugin URI: https://jairocolon.dev
 * Description: Personalizaciones para Tutor LMS, WooCommerce y Academy.
 * Version: 1.0.2
 * Author: Jairo Colón Developer
 * Author URI: https://jairocolon.dev
 * License: GPL2+
 */

if (!defined('ABSPATH')) {
    exit;
}

define('JLC_ACADEMY_VERSION', '1.0.2');
define('JLC_ACADEMY_FILE', __FILE__);
define('JLC_ACADEMY_PATH', plugin_dir_path(__FILE__));
define('JLC_ACADEMY_URL', plugin_dir_url(__FILE__));

require_once JLC_ACADEMY_PATH . 'includes/loader/class-jlc-loader.php';

JLC_Loader::init();
