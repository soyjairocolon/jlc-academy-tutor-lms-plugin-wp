<?php

if (!defined('ABSPATH')) {
    exit;
}

class JLC_Course_Sidebar
{
    public function __construct()
    {
        add_action(
            'tutor_course/single/entry/after',
            [$this, 'render_benefits'],
            10,
            1
        );

        add_filter(
            'woocommerce_product_single_add_to_cart_text',
            [$this, 'change_add_to_cart_text']
        );
    }

    /**
     * Renderiza el bono y los beneficios del curso debajo del botón de compra.
     *
     * @param int $course_id
     */
    public function render_benefits($course_id)
    {
        $updated = get_the_modified_date('M Y', $course_id);

        $this->render_bonus();
?>

        <div class="jlc-course-benefits">

            <div class="jlc-course-benefit">
                <span class="jlc-course-benefit__icon">✓</span>
                <span class="jlc-course-benefit__text">Acceso inmediato</span>
            </div>

            <div class="jlc-course-benefit">
                <span class="jlc-course-benefit__icon">✓</span>
                <span class="jlc-course-benefit__text">Actualizaciones incluidas</span>
            </div>

            <div class="jlc-course-benefit">
                <span class="jlc-course-benefit__icon">✓</span>
                <span class="jlc-course-benefit__text">Certificado de finalización</span>
            </div>

            <div class="jlc-course-benefit">
                <span class="jlc-course-benefit__icon">✓</span>
                <span class="jlc-course-benefit__text">Acceso de por vida</span>
            </div>

            <div class="jlc-course-benefit">
                <span class="jlc-course-benefit__icon">✓</span>
                <span class="jlc-course-benefit__text">
                    Última actualización <?php echo esc_html($updated); ?>
                </span>
            </div>

            <div class="jlc-course-benefit">
                <span class="jlc-course-benefit__icon">✓</span>
                <span class="jlc-course-benefit__text">
                    Nivel: Todos los niveles
                </span>
            </div>

        </div>

    <?php
    }

    /**
     * Renderiza la card del bono exclusivo.
     */
    private function render_bonus()
    {
    ?>
        <div class="jlc-course-bonus">

            <span class="jlc-course-bonus__badge">
                🎁 BONO EXCLUSIVO
            </span>

            <h3 class="jlc-course-bonus__title">
                Asesoría personalizada
                <br>
                por videollamada
            </h3>

            <div class="jlc-course-bonus__duration">
                45 minutos
            </div>

            <div class="jlc-course-bonus__value">
                Valorada en
                <del>$170.000 COP</del>
            </div>

            <div class="jlc-course-bonus__free">
                HOY GRATIS
            </div>

            <div class="jlc-course-bonus__subtitle">
                con tu inscripción
            </div>

            <p class="jlc-course-bonus__description">
                Exclusiva para estudiantes
                de este curso.
            </p>

        </div>
<?php
    }

    /**
     * Cambia el texto del botón de compra.
     *
     * Solo aplica al curso Facialista.
     *
     * @return string
     */
    public function change_add_to_cart_text()
    {
        if (
            is_singular('courses')
            && get_queried_object_id() === get_the_ID()
            && get_post_field('post_name', get_the_ID()) === 'curso-facialista'
        ) {
            return 'Comprar Curso';
        }

        return 'Añadir al carrito';
    }
}
