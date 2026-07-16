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
    }

    /**
     * Renderiza los beneficios del curso debajo del botón de compra.
     *
     * @param int $course_id
     */
    public function render_benefits($course_id)
    {
        $updated = get_the_modified_date('M Y', $course_id);
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
                <span class="jlc-course-benefit__text">Nivel: Todos los niveles</span>
            </div>

        </div>
<?php
    }
}
