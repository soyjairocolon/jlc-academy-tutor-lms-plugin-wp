<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * JLC Academy — Hero Banner del Curso Facialista.
 */
class JLC_Course_Hero_Banner
{
    public static function init()
    {
        add_action(
            'wp_footer',
            [__CLASS__, 'render'],
            5
        );
    }

    public static function render()
    {
        if (!is_singular('courses')) {
            return;
        }

        // Mostrar únicamente en el Curso Pregrabado Facialista.
        if (get_post_field('post_name', get_queried_object_id()) !== 'curso-pregrabado-facialista') {
            return;
        }

        $desktop_image = JLC_ACADEMY_URL . 'assets/images/black-friday.webp';

?>
        <template id="jlc-course-hero-banner-template">
            <section
                class="jlc-course-hero-banner"
                aria-label="Promoción Black Friday Curso Facialista">
                <div class="jlc-course-hero-banner__container">
                    <img
                        src="<?php echo esc_url($desktop_image); ?>"
                        alt="Black Friday: 10% de descuento en el Curso Pregrabado Facialista"
                        width="1920"
                        height="400"
                        loading="eager"
                        decoding="async">
                </div>
            </section>
        </template>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const template = document.getElementById('jlc-course-hero-banner-template');
                const courseDetails = document.querySelector(
                    '.tutor-wrap .tutor-course-details-page'
                );

                if (!template || !courseDetails) {
                    return;
                }

                const banner = template.content.firstElementChild.cloneNode(true);

                banner.classList.add('jlc-course-hero-banner--enter');

                courseDetails.parentNode.insertBefore(
                    banner,
                    courseDetails
                );

                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        banner.classList.add('jlc-course-hero-banner--visible');
                    });
                });
            });
        </script>
<?php
    }
}
