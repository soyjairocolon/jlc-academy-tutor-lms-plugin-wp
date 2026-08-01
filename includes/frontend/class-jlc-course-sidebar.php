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
                🎁 BONUS EXCLUSIVOS
            </span>

            <div class="jlc-course-bonus__item jlc-course-bonus__item--masterclass">
                <h3 class="jlc-course-bonus__title">
                    <!-- <span class="jlc-course-benefit__icon">✓</span> -->
                    ✓ Masterclass en vivo
                </h3>
                <span>
                    Convierte tu cabina en un SPA <br> ⭐️⭐️⭐️⭐️⭐
                </span>
            </div>

            <div class="jlc-course-bonus__item">
                <p>
                    ✓ Manual PDF descargable
                    <br>
                    método facialista
                </p>
            </div>

            <div class="jlc-course-bonus__item--wsp">
                <p>
                    ✓ WSP grupal comunidad facialista.
                </p>
                <span>
                    En donde te actualizarás en tips cada semana
                </span>
            </div>

            <div class="jlc-course-bonus__free">
                INCLUIDO HOY
            </div>

            <div class="jlc-course-bonus__subtitle">
                con tu inscripción
            </div>
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
