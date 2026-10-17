<?php

if (!defined('ABSPATH')) {
    exit;
}

class JLC_Loader
{
    /**
     * Inicializa el plugin.
     */
    public static function init()
    {
        self::load_dependencies();

        new JLC_Academy();
        new JLC_Course_FAQ();
        new JLC_Course_Checkout_Classic();
    }

    /**
     * Carga las dependencias.
     */
    private static function load_dependencies()
    {
        require_once JLC_ACADEMY_PATH . 'includes/class-jlc-academy.php';
        require_once JLC_ACADEMY_PATH . 'includes/class-jlc-assets.php';
        require_once JLC_ACADEMY_PATH . 'includes/class-jlc-hooks.php';
        require_once JLC_ACADEMY_PATH . 'includes/frontend/class-jlc-course-sidebar.php';
        require_once JLC_ACADEMY_PATH . 'includes/frontend/class-jlc-course-faq.php';
        require_once JLC_ACADEMY_PATH . 'includes/frontend/class-jlc-course-checkout-classic.php';
    }
}
