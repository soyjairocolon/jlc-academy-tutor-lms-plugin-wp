(function () {
  "use strict";

  const initFAQs = () => {
    const items = document.querySelectorAll(".jlc-course-faq__item");

    items.forEach((item) => {
      const button = item.querySelector(".jlc-course-faq__question");

      if (!button || button.dataset.jlcFaqInitialized === "true") {
        return;
      }

      button.dataset.jlcFaqInitialized = "true";

      button.addEventListener("click", () => {
        const opened = item.classList.contains("active");

        items.forEach((faq) => {
          faq.classList.remove("active");
        });

        if (!opened) {
          item.classList.add("active");
        }
      });
    });
  };

  const scrollToPurchase = () => {
    const sidebar = document.querySelector(
      ".tutor-course-details-page aside.tutor-col-xl-4",
    );

    if (!sidebar) {
      return;
    }

    const purchaseButton = sidebar.querySelector(".tutor-add-to-cart-button");

    const target = purchaseButton || sidebar;

    const targetPosition =
      target.getBoundingClientRect().top + window.scrollY - 100;

    window.scrollTo({
      top: Math.max(0, targetPosition),
      behavior: window.matchMedia("(prefers-reduced-motion: reduce)").matches
        ? "auto"
        : "smooth",
    });
  };

  const createPurchaseCTA = (className) => {
    const button = document.createElement("button");

    button.type = "button";
    button.className = className;
    button.textContent = "Quiero adquirir el Curso";

    button.addEventListener("click", scrollToPurchase);

    return button;
  };

  const initMobilePurchaseCTA = () => {
    const video = document.querySelector(
      ".tutor-course-details-page .tutor-video-player",
    );

    const tabs = document.querySelector("#tutor-course-details-tab-wrapper");

    if (!video || !tabs) {
      return;
    }

    const main = video.closest("main");

    if (!main || !main.contains(tabs)) {
      return;
    }

    if (main.querySelector(".jlc-course-mobile-cta")) {
      return;
    }

    const button = createPurchaseCTA("jlc-course-mobile-cta");

    video.insertAdjacentElement("afterend", button);
  };

  const initFAQPurchaseCTA = () => {
    const faq = document.querySelector(".jlc-course-faq");

    if (!faq) {
      return;
    }

    if (document.querySelector(".jlc-course-faq-cta")) {
      return;
    }

    const button = createPurchaseCTA("jlc-course-faq-cta");

    faq.insertAdjacentElement("afterend", button);
  };

  const init = () => {
    initFAQs();
    initMobilePurchaseCTA();
    initFAQPurchaseCTA();
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
