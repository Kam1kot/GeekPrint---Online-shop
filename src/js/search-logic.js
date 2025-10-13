// search_logic.js

document.addEventListener("DOMContentLoaded", () => {
  const mainNav = document.getElementById("mainNav"); // Получаем навигационную панель по ID

  const searchBtn = document.getElementById("searchBtn");

  const searchPanel = document.getElementById("searchPanel");

  const searchInput = document.getElementById("searchInput");

  const searchDropdown = document.getElementById("searchDropdown");

  // !!! УСТАНОВИТЕ ПРАВИЛЬНЫЙ ЭНДПОИНТ !!!
  const API_ENDPOINT = "src/functions/search_api.php";

  // Переключение видимости панели поиска и навигации

  searchBtn.addEventListener("click", () => {
    searchPanel.classList.add("active");

    mainNav.classList.add("fade-out"); // Добавляем/убираем класс для скрытия

    if (searchPanel.classList.contains("active")) {
      searchInput.focus(); // Устанавливаем фокус на поле ввода
    } else {
      // Очищаем и скрываем результаты при закрытии
      searchDropdown.innerHTML = "";
      searchDropdown.style.display = "none";
    }
  });

  // --- Функция для отображения результатов ---
  const displayResults = (products) => {
    searchDropdown.innerHTML = "";

    if (products && products.length > 0) {
      products.forEach((product) => {
        const item = document.createElement("a");
        item.href = `/templates/product.php?id=${product.id}`;
        item.classList.add("search-result-item");

        // --- ДОБАВЛЕНИЕ КАРТИНКИ И УЛУЧШЕНИЕ СТРУКТУРЫ ---

        // Проверяем наличие пути к изображению, используем заглушку, если его нет
        // const imageUrl = product.image_url
        //   ? product.image_url
        //   : "/path/to/default-toy-image.jpg"; // Укажите путь к изображению-заглушке

        // Используем более сложную структуру HTML для размещения картинки и текста
        item.innerHTML = `
                <div class="search-product"
                  <div class="product-image-wrapper">
                    <img src="src/data/covers/${product.cover}" alt="${product.title}" class="product-thumb">
                  </div>
                  <div class="product-details">
                      <strong class="product-name">${product.title}</strong>
                      <div class="product-price">${product.price} руб.</div>
                  </div>
                </div>
            `;
        // --------------------------------------------------------

        searchDropdown.appendChild(item);
      });
      searchDropdown.style.display = "flex";
    } else {
      searchDropdown.innerHTML =
        '<div class="search-result-item no-results">Ничего не найдено.</div>';
      searchDropdown.style.display = "flex";
    }
  };

  // --- Функция для выполнения поиска (FETCH) ---
  const fetchProducts = async (searchTerm) => {
    try {
      // Используем явный API_ENDPOINT
      const response = await fetch(
        `${API_ENDPOINT}?q=${encodeURIComponent(searchTerm)}`
      );

      if (!response.ok) {
        // Если статус HTTP не 200 (например, 404 или 500)
        throw new Error(`Ошибка сервера: ${response.status}`);
      }

      // Здесь мы ждем JSON, который нам должен вернуть search_api.php
      const products = await response.json();

      displayResults(products);
    } catch (error) {
      console.error("Ошибка при поиске:", error);
      searchDropdown.innerHTML =
        '<div class="search-result-item error">Не удалось загрузить данные. Проверьте консоль.</div>';
      searchDropdown.style.display = "block";
    }
  };

  // --- Обработчик события 'input' с Debounce ---
  let timeoutId;
  const DEBOUNCE_DELAY = 300; // Задержка в миллисекундах

  searchInput.addEventListener("input", (event) => {
    const searchTerm = event.target.value.trim();

    clearTimeout(timeoutId);

    if (searchTerm.length >= 2) {
      // Начинаем поиск только после 2 символов
      timeoutId = setTimeout(() => {
        fetchProducts(searchTerm);
      }, DEBOUNCE_DELAY);
    } else {
      // Скрываем выпадающий список, если запрос слишком короткий или пуст
      searchDropdown.innerHTML = "";
      searchDropdown.style.display = "none";
    }
  });

  // --- Скрытие панели при клике вне неё ---
  document.addEventListener("click", (event) => {
    // Проверяем, был ли клик вне области навигации И вне панели поиска
    const isClickedOnNavOrPanel =
      event.target.closest(".navbar") ||
      event.target.closest(".search-panel") ||
      event.target === searchBtn;

    if (!isClickedOnNavOrPanel) {
      searchPanel.classList.remove("active");
      mainNav.classList.remove("fade-out");
      searchDropdown.style.display = "none"; // Дополнительно скрываем дропдаун
    }
  });
});
