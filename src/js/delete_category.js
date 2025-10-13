const deleteButtonsCat = document.querySelectorAll(".confirm-delete-btn-cat");

deleteButtonsCat.forEach((delBtn) => {
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
      clickedOnce = false;
      timer = null;
      form.submit();
    }
  });
});
