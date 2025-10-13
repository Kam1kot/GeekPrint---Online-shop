const previewUploadCover = () => {
  const input = document.querySelector("#coverInput");
  const preview = document.querySelector("#coverPreview");
  const wrapper = document.querySelector(".cover-preview-wrapper");

  if (!input || !preview || !wrapper) return; // если хотя бы одного элемента нет — прекращаем

  input.addEventListener("change", function () {
    const file = this.files[0];

    if (file && file.type.startsWith("image/")) {
      const reader = new FileReader();

      reader.onload = ({ target }) => {
        preview.src = target.result;
        preview.classList.add("shadow");
        wrapper.style.display = "block";
      };

      reader.readAsDataURL(file);
    }
  });
};

previewUploadCover();
