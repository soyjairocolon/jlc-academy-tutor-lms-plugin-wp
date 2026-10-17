
<?php

if (!defined('ABSPATH')) {
    exit;
}

class JLC_Course_Direct_Checkout
{
    private const PRODUCT_CATEGORY = 'cursos-online';

    public function __construct()
    {
        add_filter(
            'tutor_add_to_cart_btn',
            [$this, 'prepare_checkout_form'],
            20,
            2
        );

        add_action(
            'template_redirect',
            [$this, 'handle_direct_checkout'],
            1
        );
    }

    /**
     * Prepara el formulario de compra directa de cursos online.
     *
     * @param string $html
     * @param int    $course_id
     * @return string
     */
    public function prepare_checkout_form($html, $course_id)
    {
        if (!function_exists('wc_get_product')) {
            return $html;
        }

        $product_id = absint(
            tutor_utils()->get_course_product_id($course_id)
        );

        if (!$this->is_online_course($product_id)) {
            return $html;
        }

        $fields = sprintf(
            '<input type="hidden" name="jlc_course_direct_checkout" value="1">
             <input type="hidden" name="jlc_course_product_id" value="%d">
             <input type="hidden" name="jlc_course_checkout_nonce" value="%s">',
            $product_id,
            esc_attr(
                wp_create_nonce('jlc_course_direct_checkout')
            )
        );

        /*
         * Inserta los campos dentro del formulario original.
         */
        $html = preg_replace(
            '/<\/form\s*>/i',
            $fields . '</form>',
            $html,
            1
        );

        /*
         * El botón conserva su diseño y clases de Tutor LMS,
         * pero deja de enviar el parámetro add-to-cart.
         */
        $html = preg_replace(
            '/(<button\b[^>]*\bname=["\'])add-to-cart(["\'])/i',
            '$1jlc-course-purchase$2',
            $html,
            1
        );

        return $html;
    }

    /**
     * Procesa la compra directa del curso.
     */
    public function handle_direct_checkout()
    {
        if (
            'POST' !== ($_SERVER['REQUEST_METHOD'] ?? '') ||
            !isset(
                $_POST['jlc_course_direct_checkout'],
                $_POST['jlc_course_product_id']
            )
        ) {
            return;
        }

        if (
            !function_exists('WC') ||
            !function_exists('wc_get_checkout_url')
        ) {
            return;
        }

        $nonce = isset($_POST['jlc_course_checkout_nonce'])
            ? sanitize_text_field(
                wp_unslash($_POST['jlc_course_checkout_nonce'])
            )
            : '';

        if (!wp_verify_nonce($nonce, 'jlc_course_direct_checkout')) {
            wp_die(
                esc_html__(
                    'No se pudo validar la solicitud de compra.',
                    'jlc-academy'
                ),
                '',
                ['response' => 403]
            );
        }

        $product_id = absint(
            wp_unslash($_POST['jlc_course_product_id'])
        );

        if (!$this->is_online_course($product_id)) {
            return;
        }

        /*
         * Comprueba que el producto pertenece al curso
         * desde cuya página se realiza la solicitud.
         */
        if (!is_singular('courses')) {
            return;
        }

        $course_id = get_queried_object_id();

        if (
            !$course_id ||
            absint(tutor_utils()->get_course_product_id($course_id)) !== $product_id
        ) {
            return;
        }

        /*
         * Respeta la configuración de Tutor LMS cuando
         * se requiere iniciar sesión para comprar.
         */
        $guest_cart_enabled = tutor_utils()->get_option(
            'enable_guest_course_cart'
        );

        if (!is_user_logged_in() && !$guest_cart_enabled) {
            return;
        }

        if (!WC()->cart) {
            return;
        }

        /*
         * Si el curso ya está en el carrito, continúa
         * directamente al checkout.
         */
        foreach (WC()->cart->get_cart() as $cart_item) {
            if ((int) $cart_item['product_id'] === $product_id) {
                wp_safe_redirect(wc_get_checkout_url());
                exit;
            }
        }

        /*
         * Agrega el curso utilizando WooCommerce.
         */
        $cart_item_key = WC()->cart->add_to_cart($product_id, 1);

        if (!$cart_item_key) {
            return;
        }

        wp_safe_redirect(wc_get_checkout_url());
        exit;
    }

    /**
     * Identifica los productos virtuales de cursos online.
     *
     * @param int $product_id
     * @return bool
     */
    private function is_online_course($product_id)
    {
        if (!function_exists('wc_get_product') || !$product_id) {
            return false;
        }

        $product = wc_get_product($product_id);

        if (!$product || !$product->is_purchasable()) {
            return false;
        }

        if (!$product->is_virtual()) {
            return false;
        }

        return has_term(
            self::PRODUCT_CATEGORY,
            'product_cat',
            $product_id
        );
    }
}
