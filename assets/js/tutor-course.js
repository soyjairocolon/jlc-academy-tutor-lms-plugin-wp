// *** FAQs ***
document.addEventListener("DOMContentLoaded", () => {
  const items = document.querySelectorAll(".jlc-course-faq__item");

  if (!items.length) {
    return;
  }

  items.forEach((item) => {
    const button = item.querySelector(".jlc-course-faq__question");

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
});
