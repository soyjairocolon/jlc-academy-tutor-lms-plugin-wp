
<?php

if (!defined('ABSPATH')) {
    exit;
}

class JLC_Assets
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action(
            'wp_enqueue_scripts',
            [$this, 'enqueue_frontend_assets']
        );
    }

    /**
     * Carga los assets del frontend.
     */
    public function enqueue_frontend_assets()
    {
        if (!is_singular('courses')) {
            return;
        }

        wp_enqueue_style(
            'jlc-academy-tutor-course',
            JLC_ACADEMY_URL . 'assets/css/tutor-course.css',
            [],
            JLC_ACADEMY_VERSION
        );

        wp_enqueue_script(
            'jlc-academy-tutor-course',
            JLC_ACADEMY_URL . 'assets/js/tutor-course.js',
            [],
            JLC_ACADEMY_VERSION,
            true
        );

        wp_enqueue_script(
            'jlc-academy-course-direct-checkout',
            JLC_ACADEMY_URL . 'assets/js/frontend/jlc-course-direct-checkout.js',
            [],
            JLC_ACADEMY_VERSION,
            true
        );
    }
}
