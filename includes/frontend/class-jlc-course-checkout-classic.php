
<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Checkout clásico optimizado para cursos online.
 *
 * Plugin: JLC Academy
 * Compatible con WooCommerce Checkout Clásico.
 */
class JLC_Course_Checkout_Classic
{
    /**
     * Categoría de productos correspondientes a cursos.
     *
     * @var string
     */
    private $course_category = 'cursos-online';

    public function __construct()
    {
        add_filter(
            'woocommerce_checkout_fields',
            [$this, 'customize_checkout_fields'],
            999
        );

        add_filter(
            'woocommerce_enable_order_notes_field',
            [$this, 'disable_order_notes'],
            20,
            1
        );

        add_filter(
            'woocommerce_checkout_get_value',
            [$this, 'prefill_checkout_value'],
            20,
            2
        );

        add_action(
            'woocommerce_after_checkout_validation',
            [$this, 'validate_checkout'],
            20,
            2
        );

        add_action(
            'woocommerce_checkout_create_order',
            [$this, 'save_order_whatsapp'],
            20,
            2
        );

        add_action(
            'woocommerce_checkout_update_user_meta',
            [$this, 'save_user_whatsapp'],
            20,
            2
        );

        add_action(
            'woocommerce_before_checkout_form',
            [$this, 'log_checkout_status'],
            5
        );

        add_action(
            'wp_enqueue_scripts',
            [$this, 'enqueue_styles'],
            20
        );
    }

    /**
     * Registra información de diagnóstico.
     *
     * No registra información personal.
     */
    private function log($message)
    {
        if (
            defined('WP_DEBUG') &&
            WP_DEBUG &&
            defined('WP_DEBUG_LOG') &&
            WP_DEBUG_LOG
        ) {
            error_log('[JLC Checkout Classic] ' . $message);
        }
    }

    /**
     * Detecta si el carrito contiene exclusivamente
     * productos de la categoría cursos-online.
     *
     * @return bool
     */
    private function is_course_checkout()
    {
        if (
            !function_exists('WC') ||
            !function_exists('has_term')
        ) {
            return false;
        }

        $woocommerce = WC();

        if (
            !$woocommerce ||
            !isset($woocommerce->cart) ||
            !$woocommerce->cart
        ) {
            return false;
        }

        $items = $woocommerce->cart->get_cart();

        if (empty($items)) {
            return false;
        }

        foreach ($items as $item) {

            if (
                !is_array($item) ||
                empty($item['product_id'])
            ) {
                return false;
            }

            $product_id = absint($item['product_id']);

            if (
                !has_term(
                    $this->course_category,
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
     * Simplifica los campos del checkout.
     *
     * @param array $fields
     * @return array
     */
    public function customize_checkout_fields($fields)
    {
        if (!$this->is_course_checkout()) {
            return $fields;
        }

        $this->log('Aplicando checkout simplificado.');

        if (!isset($fields['billing'])) {
            $fields['billing'] = [];
        }

        /**
         * Campos permitidos.
         */
        $allowed_fields = [
            'billing_email',
            'billing_first_name',
            'billing_last_name',
            'billing_phone'
        ];

        /**
         * Elimina todos los campos de facturación
         * que no necesitamos para cursos.
         */
        foreach (array_keys($fields['billing']) as $key) {

            if (!in_array($key, $allowed_fields, true)) {
                unset($fields['billing'][$key]);
            }
        }

        /**
         * Configuración de correo electrónico.
         */
        $fields['billing']['billing_email'] = array_merge(
            $fields['billing']['billing_email'] ?? [],
            [
                'type'         => 'email',
                'label'        => 'Correo electrónico',
                'placeholder'  => 'correo@ejemplo.com',
                'required'     => true,
                'priority'     => 10,
                'class'        => ['form-row-wide'],
                'autocomplete' => 'email'
            ]
        );

        /**
         * Nombre.
         */
        $fields['billing']['billing_first_name'] = array_merge(
            $fields['billing']['billing_first_name'] ?? [],
            [
                'type'         => 'text',
                'label'        => 'Nombre',
                'required'     => true,
                'priority'     => 20,
                'class'        => ['form-row-first'],
                'autocomplete' => 'given-name'
            ]
        );

        /**
         * Apellidos.
         */
        $fields['billing']['billing_last_name'] = array_merge(
            $fields['billing']['billing_last_name'] ?? [],
            [
                'type'         => 'text',
                'label'        => 'Apellidos',
                'required'     => true,
                'priority'     => 30,
                'class'        => ['form-row-last'],
                'autocomplete' => 'family-name'
            ]
        );

        /**
         * WhatsApp.
         *
         * Utilizamos billing_phone para conservar
         * compatibilidad con WooCommerce.
         */
        $fields['billing']['billing_phone'] = array_merge(
            $fields['billing']['billing_phone'] ?? [],
            [
                'type'         => 'tel',
                'label'        => 'WhatsApp',
                'placeholder'  => 'Ej. 3001234567',
                'required'     => true,
                'priority'     => 40,
                'class'        => ['form-row-wide'],
                'autocomplete' => 'tel',
                'description'  => 'Ingresa tu número de WhatsApp con indicativo de país si corresponde.'
            ]
        );

        /**
         * Elimina los campos de envío y notas.
         */
        $fields['shipping'] = [];
        $fields['order'] = [];

        $this->log('Campos simplificados correctamente.');

        return $fields;
    }

    /**
     * Deshabilita notas del pedido.
     *
     * @param bool $enabled
     * @return bool
     */
    public function disable_order_notes($enabled)
    {
        if ($this->is_course_checkout()) {
            return false;
        }

        return $enabled;
    }

    /**
     * Normaliza el número de WhatsApp.
     *
     * Conserva únicamente dígitos y un
     * signo + inicial, si existe.
     *
     * @param string $phone
     * @return string
     */
    private function normalize_phone($phone)
    {
        $phone = trim((string) $phone);

        $has_plus = strpos($phone, '+') === 0;

        $digits = preg_replace('/\D+/', '', $phone);

        if (empty($digits)) {
            return '';
        }

        return ($has_plus ? '+' : '') . $digits;
    }

    /**
     * Permite precargar un teléfono normalizado
     * cuando WooCommerce recupera su valor.
     *
     * @param mixed  $value
     * @param string $input
     * @return mixed
     */
    public function prefill_checkout_value($value, $input)
    {
        if (
            !$this->is_course_checkout() ||
            $input !== 'billing_phone' ||
            empty($value)
        ) {
            return $value;
        }

        return $this->normalize_phone($value);
    }

    /**
     * Valida WhatsApp antes de crear el pedido.
     *
     * @param array    $data
     * @param WP_Error $errors
     */
    public function validate_checkout($data, $errors)
    {
        if (!$this->is_course_checkout()) {
            return;
        }

        $phone = isset($data['billing_phone'])
            ? $this->normalize_phone($data['billing_phone'])
            : '';

        if (empty($phone)) {

            $errors->add(
                'jlc_whatsapp_required',
                'Por favor, ingresa tu número de WhatsApp.'
            );

            return;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        $length = strlen($digits);

        if ($length < 10 || $length > 15) {

            $errors->add(
                'jlc_whatsapp_invalid',
                'Ingresa un número de WhatsApp válido, entre 10 y 15 dígitos.'
            );

            return;
        }

        $this->log('Validación de WhatsApp completada.');
    }

    /**
     * Guarda WhatsApp en el pedido.
     *
     * WooCommerce utiliza billing_phone
     * como campo nativo del pedido.
     *
     * @param WC_Order $order
     * @param array    $data
     */
    public function save_order_whatsapp($order, $data)
    {
        if (!$this->is_course_checkout()) {
            return;
        }

        if (empty($data['billing_phone'])) {
            return;
        }

        $phone = $this->normalize_phone(
            $data['billing_phone']
        );

        $order->set_billing_phone($phone);

        $this->log('WhatsApp asignado al pedido.');
    }

    /**
     * Guarda WhatsApp en el perfil del usuario.
     *
     * @param int   $customer_id
     * @param array $data
     */
    public function save_user_whatsapp($customer_id, $data)
    {
        if (
            !$this->is_course_checkout() ||
            !$customer_id ||
            empty($data['billing_phone'])
        ) {
            return;
        }

        $phone = $this->normalize_phone(
            $data['billing_phone']
        );

        update_user_meta(
            $customer_id,
            'billing_phone',
            $phone
        );

        $this->log('WhatsApp guardado en el usuario.');
    }

    /**
     * Diagnóstico del checkout.
     */
    public function log_checkout_status()
    {
        if ($this->is_course_checkout()) {

            $this->log(
                'Checkout detectado: Cursos Online.'
            );
        } else {

            $this->log(
                'Checkout detectado: Normal.'
            );
        }
    }
}
