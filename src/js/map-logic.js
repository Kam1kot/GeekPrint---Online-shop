document.addEventListener("DOMContentLoaded", () => {
  const pickupCheck = document.getElementById("pickupCheck");
  const deliveryBlock = document.getElementById("deliveryBlock");
  const addressInput = document.getElementById("orderAddress");

  // Чекбокс логика
  pickupCheck.addEventListener("change", () => {
    if (pickupCheck.checked) {
      deliveryBlock.classList.add("d-none");
      addressInput.removeAttribute("required");
    } else {
      deliveryBlock.classList.remove("d-none");
      addressInput.setAttribute("required", "required");
    }
  });
});
