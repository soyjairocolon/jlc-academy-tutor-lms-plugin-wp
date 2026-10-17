
<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Personalización de WooCommerce Checkout Blocks
 * para carritos que contienen únicamente cursos online.
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
    }

    /**
     * Comprueba si todos los productos del carrito
     * son cursos online virtuales.
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

            // Las variaciones utilizan la categoría
            // del producto padre.
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
     * Personaliza los campos de dirección
     * compatibles con Checkout Blocks.
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

        // Configuración específica para Colombia.
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

        // Mantener el teléfono visible y obligatorio.
        if (
            !isset($locale['CO']['phone'])
            || !is_array($locale['CO']['phone'])
        ) {
            $locale['CO']['phone'] = [];
        }

        $locale['CO']['phone']['required'] = true;
        $locale['CO']['phone']['hidden'] = false;

        // País, nombre y apellidos permanecen visibles.
        return $locale;
    }

    /**
     * Solicita teléfono obligatorio únicamente
     * para carritos exclusivos de cursos.
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
}
