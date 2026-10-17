
<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Personalización de WooCommerce Checkout Blocks
 * para cursos online y estilos generales del checkout.
 */
class JLC_Course_Checkout_Blocks
{
    public function __construct()
    {
        add_filter(
            'woocommerce_get_country_locale',
            [$this, 'customize_country_locale'],
            999
        );

        add_filter(
            'pre_option_woocommerce_checkout_phone_field',
            [$this, 'require_course_phone'],
            999
        );

        add_filter(
            'body_class',
            [$this, 'add_checkout_body_class']
        );

        add_action(
            'wp_enqueue_scripts',
            [$this, 'enqueue_styles']
        );
    }

    /**
     * Identifica carritos exclusivos de cursos virtuales.
     *
     * @return bool
     */
    private function is_course_checkout()
    {
        if (
            !function_exists('WC')
            || !WC()
            || !WC()->cart
        ) {
            return false;
        }

        $cart = WC()->cart;
        $items = $cart->get_cart();

        if (empty($items) || $cart->needs_shipping()) {
            return false;
        }

        foreach ($items as $item) {
            $product = isset($item['data'])
                ? $item['data']
                : false;

            if (
                !$product
                || !is_a($product, 'WC_Product')
                || !$product->is_virtual()
            ) {
                return false;
            }

            $product_id = $product->get_parent_id()
                ?: $product->get_id();

            if (
                !has_term(
                    'cursos-online',
                    'product_cat',
                    $product_id
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Oculta campos de dirección para cursos.
     *
     * @param array $locale
     * @return array
     */
    public function customize_country_locale($locale)
    {
        if (
            !$this->is_course_checkout()
            || !is_array($locale)
        ) {
            return $locale;
        }

        if (
            !isset($locale['CO'])
            || !is_array($locale['CO'])
        ) {
            $locale['CO'] = [];
        }

        $hidden_fields = [
            'company',
            'address_1',
            'address_2',
            'city',
            'state',
            'postcode',
        ];

        foreach ($hidden_fields as $field) {
            if (
                !isset($locale['CO'][$field])
                || !is_array($locale['CO'][$field])
            ) {
                $locale['CO'][$field] = [];
            }

            $locale['CO'][$field]['required'] = false;
            $locale['CO'][$field]['hidden'] = true;
        }

        if (
            !isset($locale['CO']['phone'])
            || !is_array($locale['CO']['phone'])
        ) {
            $locale['CO']['phone'] = [];
        }

        $locale['CO']['phone']['required'] = true;
        $locale['CO']['phone']['hidden'] = false;

        return $locale;
    }

    /**
     * Hace obligatorio el teléfono para cursos.
     *
     * @param mixed $value
     * @return mixed
     */
    public function require_course_phone($value)
    {
        if ($this->is_course_checkout()) {
            return 'required';
        }

        return $value;
    }

    /**
     * Agrega una clase al body según el tipo de carrito.
     *
     * @param array $classes
     * @return array
     */
    public function add_checkout_body_class($classes)
    {
        if (
            !function_exists('is_checkout')
            || !is_checkout()
            || !function_exists('WC')
            || !WC()
            || !WC()->cart
        ) {
            return $classes;
        }

        $items = WC()->cart->get_cart();

        if (empty($items)) {
            return $classes;
        }

        // Conservar la clasificación exclusiva de cursos.
        if ($this->is_course_checkout()) {
            $classes[] = 'jlc-course-checkout-blocks';

            return $classes;
        }

        $has_courses = false;
        $has_physical = false;

        foreach ($items as $item) {
            $product = isset($item['data'])
                ? $item['data']
                : false;

            if (
                !$product
                || !is_a($product, 'WC_Product')
            ) {
                return $classes;
            }

            $product_id = $product->get_parent_id()
                ?: $product->get_id();

            $is_course = $product->is_virtual()
                && has_term(
                    'cursos-online',
                    'product_cat',
                    $product_id
                );

            if ($is_course) {
                $has_courses = true;
            } else {
                $has_physical = true;
            }
        }

        if ($has_courses && $has_physical) {
            $classes[] = 'jlc-mixed-checkout-blocks';
        } elseif ($has_physical) {
            $classes[] = 'jlc-physical-checkout-blocks';
        }

        return $classes;
    }


    /**
     * Carga los estilos de Checkout Blocks.
     *
     * Global: todos los tipos de carrito.
     * Cursos: únicamente cursos online.
     *
     * @return void
     */
    public function enqueue_styles()
    {
        if (
            !function_exists('is_checkout')
            || !is_checkout()
        ) {
            return;
        }

        // Estilos generales del checkout.
        $global_relative_path = 'assets/css/frontend/physical-checkout-blocks.css';
        $global_file_path = JLC_ACADEMY_PATH . $global_relative_path;

        if (file_exists($global_file_path)) {
            wp_enqueue_style(
                'jlc-checkout-blocks-global',
                plugins_url(
                    $global_relative_path,
                    JLC_ACADEMY_PATH . 'jlc-academy.php'
                ),
                [],
                filemtime($global_file_path)
            );
        }

        // Estilos exclusivos para cursos online.
        if (!$this->is_course_checkout()) {
            return;
        }

        $course_relative_path = 'assets/css/frontend/course-checkout-blocks.css';
        $course_file_path = JLC_ACADEMY_PATH . $course_relative_path;

        if (!file_exists($course_file_path)) {
            return;
        }

        wp_enqueue_style(
            'jlc-course-checkout-blocks',
            plugins_url(
                $course_relative_path,
                JLC_ACADEMY_PATH . 'jlc-academy.php'
            ),
            ['jlc-checkout-blocks-global'],
            filemtime($course_file_path)
        );
    }
}
