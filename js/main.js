
document.querySelectorAll(".faq-question").forEach((question) => {
    question.addEventListener("click", () => {
        const faqItem = question.parentElement;
        const faqContainer = faqItem.parentElement;

        faqContainer.querySelectorAll(".faq-item").forEach((item) => {
            if (item !== faqItem) {
                item.classList.remove("active");
            }
        });

        faqItem.classList.toggle("active");
    });
});
