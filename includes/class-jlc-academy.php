<?php

if (!defined('ABSPATH')) {
    exit;
}

class JLC_Academy
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->load_modules();
    }

    /**
     * Inicializa los módulos del plugin.
     */
    private function load_modules()
    {
        new JLC_Assets();
        new JLC_Hooks();
        new JLC_Course_Sidebar();
    }
}
