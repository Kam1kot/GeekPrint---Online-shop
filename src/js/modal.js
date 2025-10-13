document.querySelectorAll(".modal").forEach((modal) => {
  modal.addEventListener("shown.bs.modal", () => {
    const input = modal.querySelector("input");
    if (input) input.focus();
  });
});
