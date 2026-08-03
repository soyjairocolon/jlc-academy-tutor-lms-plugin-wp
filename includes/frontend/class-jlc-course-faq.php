<?php

if (!defined('ABSPATH')) {
    exit;
}

class JLC_Course_FAQ
{
    public function __construct()
    {
        add_action(
            'wp_footer',
            [$this, 'render']
        );
    }

    /**
     * Renderiza la sección FAQ únicamente
     * para el curso Facialista.
     */
    public function render()
    {
        if (
            !is_singular('courses')
            || get_post_field(
                'post_name',
                get_queried_object_id()
            ) !== 'curso-facialista'
        ) {
            return;
        }

        ob_start();
?>

        <!-- Skeleton Loader -->
        <div class="jlc-course-faq-loader">

            <div class="jlc-course-faq__container">

                <div class="jlc-course-faq__header">

                    <div class="jlc-skeleton jlc-skeleton--eyebrow"></div>

                    <div class="jlc-skeleton jlc-skeleton--title"></div>

                    <div class="jlc-skeleton jlc-skeleton--subtitle"></div>

                </div>

                <div class="jlc-course-faq__accordion">

                    <div class="jlc-skeleton jlc-skeleton--item"></div>
                    <div class="jlc-skeleton jlc-skeleton--item"></div>
                    <div class="jlc-skeleton jlc-skeleton--item"></div>
                    <div class="jlc-skeleton jlc-skeleton--item"></div>
                    <div class="jlc-skeleton jlc-skeleton--item"></div>

                </div>

            </div>

        </div>

        <!-- FAQ -->
        <div
            class="jlc-course-faq"
            style="display:none;">

            <div class="jlc-course-faq__container">

                <div class="jlc-course-faq__header">

                    <span class="jlc-course-faq__eyebrow">
                        PREGUNTAS FRECUENTES
                    </span>

                    <h2 class="jlc-course-faq__title">
                        Resolvemos tus dudas antes de comenzar
                    </h2>

                    <p class="jlc-course-faq__subtitle">
                        Estas son las preguntas que más recibimos antes de que nuestros estudiantes inicien el curso.
                    </p>

                </div>

                <div class="jlc-course-faq__accordion">

                    <div class="jlc-course-faq__item">
                        <button class="jlc-course-faq__question">
                            ¿Necesito experiencia previa?
                        </button>

                        <div class="jlc-course-faq__answer">
                            No. En este curso están explicados paso a paso para que puedas seguirlo, aunque nunca hayas hecho una formación de masaje previa.
                        </div>
                    </div>

                    <div class="jlc-course-faq__item">
                        <button class="jlc-course-faq__question">
                            ¿El acceso es de por vida?
                        </button>

                        <div class="jlc-course-faq__answer">
                            Sí. Tendrás acceso permanente al contenido y también a futuras actualizaciones del curso. El acceso online es ilimitado y podrás verlo a tu ritmo.
                        </div>
                    </div>

                    <div class="jlc-course-faq__item">
                        <button class="jlc-course-faq__question">
                            ¿Cómo recibo el certificado?
                        </button>

                        <div class="jlc-course-faq__answer">
                            Al completar el curso podrás descargar tu certificado de finalización.
                        </div>
                    </div>

                    <!-- <div class="jlc-course-faq__item">
                        <button class="jlc-course-faq__question">
                            ¿Cómo funciona el bono de asesoría?
                        </button>

                        <div class="jlc-course-faq__answer">
                            Una vez adquieras el curso podrás agendar una videollamada personalizada de 45 minutos con Natalia Bustillo.
                        </div>
                    </div> -->

                    <div class="jlc-course-faq__item">
                        <button class="jlc-course-faq__question">
                            ¿Es muy difícil aplicarlo?
                        </button>

                        <div class="jlc-course-faq__answer">
                            No. Precisamente una de las cosas más importantes para mí es que cada curso fuese práctico, claro y fácil de integrar en tus sesiones. Muchas alumnas anteriores me dicen que lo que más valoran es la forma clara y cercana de enseñar.
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const course = document.querySelector('.tutor-course-details-page');
                const faq = document.querySelector('.jlc-course-faq');
                const loader = document.querySelector('.jlc-course-faq-loader');

                if (!course || !faq || !loader) {
                    return;
                }

                course.insertAdjacentElement('afterend', loader);
                loader.insertAdjacentElement('afterend', faq);

                requestAnimationFrame(function() {

                    faq.style.display = 'block';

                    requestAnimationFrame(function() {

                        faq.classList.add('is-visible');

                        setTimeout(function() {

                            loader.remove();

                        }, 250);

                    });

                });

            });
        </script>

<?php

        echo ob_get_clean();
    }
}
