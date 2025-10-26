document.addEventListener("click", function (event) {
  if (event.target.classList.contains("delete-cover-btn")) {
    const button = event.target;
    const productId = button.getAttribute("data-product-id");

    const formData = new FormData();
    formData.append("action", "delete_cover");
    formData.append("id", productId);

    fetch("", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const container = document.getElementById(
            `cover-container-${productId}`
          );
          if (container) {
            container.style.display = "none";
          }
        }
      });
  }
});
document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("categoryCoverInput");
  const preview = document.getElementById("categoryCoverPreview");

  if (input && preview) {
    input.addEventListener("change", function () {
      const file = this.files[0];
      if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = "block";
        };
        reader.readAsDataURL(file);
      } else {
        preview.src = "";
        preview.style.display = "none";
      }
    });
  }
});
