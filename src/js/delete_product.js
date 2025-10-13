const deleteButtons = document.querySelectorAll(".confirm-delete-btn");

deleteButtons.forEach((delBtn) => {
  const form = delBtn.closest("form");
  let timer = null;
  let clickedOnce = false;
  const originalColor = delBtn.style.backgroundColor || "";

  delBtn.addEventListener("click", (event) => {
    if (!clickedOnce) {
      event.preventDefault(); // Остановить отправку формы
      delBtn.style.backgroundColor = "green";
      clickedOnce = true;

      timer = setTimeout(() => {
        delBtn.style.backgroundColor = originalColor;
        clickedOnce = false;
        timer = null;
      }, 3000);
    } else {
      // Второй клик — подтверждение
      clearTimeout(timer);
      delBtn.style.backgroundColor = "green";
      delBtn.setAttribute("name", "product-delete");
      clickedOnce = false;
      timer = null;
      form.submit();
    }
  });
});
