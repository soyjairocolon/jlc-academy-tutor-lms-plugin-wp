(function () {
  "use strict";

  const FORM_SELECTOR = ".tutor-course-details-page form";

  const PURCHASE_BUTTON_SELECTOR =
    ".tutor-add-to-cart-button[name='jlc-course-purchase']";

  const DIRECT_CHECKOUT_FIELD = "jlc_course_direct_checkout";
  const PRODUCT_FIELD = "jlc_course_product_id";
  const NONCE_FIELD = "jlc_course_checkout_nonce";

  const initDirectCheckout = () => {
    const forms = document.querySelectorAll(FORM_SELECTOR);

    forms.forEach((form) => {
      if (form.dataset.jlcDirectCheckoutInitialized === "true") {
        return;
      }

      const button = form.querySelector(PURCHASE_BUTTON_SELECTOR);

      const directCheckoutField = form.querySelector(
        `[name="${DIRECT_CHECKOUT_FIELD}"]`,
      );

      const productField = form.querySelector(`[name="${PRODUCT_FIELD}"]`);

      const nonceField = form.querySelector(`[name="${NONCE_FIELD}"]`);

      if (!button || !directCheckoutField || !productField || !nonceField) {
        return;
      }

      form.dataset.jlcDirectCheckoutInitialized = "true";

      form.addEventListener(
        "submit",
        (event) => {
          if (form.dataset.jlcSubmitting === "true") {
            event.preventDefault();
            event.stopImmediatePropagation();
            return;
          }

          /*
           * Respeta el modal de inicio de sesión de Tutor LMS
           * cuando el usuario todavía no puede comprar.
           */
          if (button.classList.contains("tutor-open-login-modal")) {
            return;
          }

          /*
           * Evita que otros manejadores procesen la compra
           * mediante AJAX.
           */
          event.preventDefault();
          event.stopImmediatePropagation();

          form.dataset.jlcSubmitting = "true";

          button.disabled = true;

          const buttonText = button.querySelector("span:last-child");

          if (buttonText) {
            buttonText.textContent = "Procesando compra...";
          }

          /*
           * El formulario ya contiene los campos necesarios:
           *
           * jlc_course_direct_checkout
           * jlc_course_product_id
           * jlc_course_checkout_nonce
           *
           * No añadimos add-to-cart para evitar que WooCommerce
           * procese la solicitud por segunda vez.
           */
          HTMLFormElement.prototype.submit.call(form);
        },
        true,
      );
    });
  };

  const init = () => {
    initDirectCheckout();
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
