// cart.js
document.addEventListener("DOMContentLoaded", () => {
  // Элементы
  const cartCountEl = document.getElementById("cartCount");
  const cartListEl = document.getElementById("cartList");
  const cartEmptyEl = document.getElementById("cartEmpty");
  const cartTotalEl = document.getElementById("cartTotal");
  const clearCartBtn = document.getElementById("clearCartBtn");
  const orderForm = document.getElementById("orderForm");
  const addressInput = document.getElementById("orderAddress");
  const pickupCheck = document.getElementById("pickupCheck");
  const deliveryBlock = document.getElementById("deliveryBlock");
  const cartModalEl = document.getElementById("cartModal");

  // === ЛОГИКА КАРТЫ (Leaflet.js) ===
  let map, marker;
  let isMapInitialized = false;

  function initMap() {
    if (isMapInitialized) return;

    map = L.map("deliveryMap").setView([55.751574, 37.573856], 10);
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution:
        '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    map.on("click", (e) => {
      updateMapWithCoords(e.latlng);
    });

    addressInput.addEventListener("input", () => {
      const query = addressInput.value.trim();
      if (query.length < 3) return;

      const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(
        query
      )}`;
      fetch(url)
        .then((response) => response.json())
        .then((data) => {
          if (data.length > 0) {
            const firstResult = data[0];
            const coords = L.latLng(firstResult.lat, firstResult.lon);
            updateMapWithCoords(coords);
          }
        })
        .catch((error) =>
          console.error("Ошибка прямого геокодирования:", error)
        );
    });

    isMapInitialized = true;
  }

  function getAddressFromCoords(coords) {
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${coords.lat}&lon=${coords.lng}`;
    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        const fullAddress = data.display_name || "Адрес не найден";
        addressInput.value = fullAddress;
      })
      .catch((error) => {
        console.error("Ошибка обратного геокодирования:", error);
        addressInput.value = "";
      });
  }

  function updateMapWithCoords(coords) {
    if (marker) {
      marker.setLatLng(coords);
    } else {
      marker = L.marker(coords, { draggable: true }).addTo(map);
      marker.on("dragend", (e) => {
        getAddressFromCoords(e.target.getLatLng());
      });
    }
    map.setView(coords, 14);
    getAddressFromCoords(coords);
  }

  // === Инициализация карты при открытии модального окна ===
  cartModalEl.addEventListener("shown.bs.modal", function () {
    initMap();
    map.invalidateSize(); // Заставляем карту пересчитать свои размеры
  });

  // Чекбокс логика
  pickupCheck.addEventListener("change", () => {
    if (pickupCheck.checked) {
      deliveryBlock.classList.add("d-none");
    } else {
      deliveryBlock.classList.remove("d-none");
    }
  });

  addressInput.addEventListener("input", () => {
    const query = addressInput.value.trim();
    if (query.length < 3) return;

    const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(
      query
    )}`;
    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        if (data.length > 0) {
          const firstResult = data[0];
          const coords = L.latLng(firstResult.lat, firstResult.lon);
          updateMapWithCoords(coords);
        }
      })
      .catch((error) => console.error("Ошибка прямого геокодирования:", error));
  });

  // Чекбокс логика
  pickupCheck.addEventListener("change", () => {
    if (pickupCheck.checked) {
      document.getElementById("deliveryBlock").classList.add("d-none");
    } else {
      document.getElementById("deliveryBlock").classList.remove("d-none");
    }
  });

  // Helpers
  const CART_KEY = "site_cart_v1";

  function formatPrice(num) {
    if (isNaN(num)) num = 0;
    return new Intl.NumberFormat("ru-RU", {
      style: "currency",
      currency: "RUB",
    }).format(num);
  }

  function loadCart() {
    try {
      const raw = localStorage.getItem(CART_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch (e) {
      console.error("Ошибка при чтении корзины", e);
      return [];
    }
  }

  function saveCart(cart) {
    try {
      localStorage.setItem(CART_KEY, JSON.stringify(cart));
      updateCartCount(cart);
      renderCart(cart);
    } catch (e) {
      console.error("Ошибка при сохранении корзины", e);
    }
  }

  function findItem(cart, id) {
    return cart.find((it) => String(it.id) === String(id));
  }

  function addToCart(item) {
    const cart = loadCart();
    const existing = findItem(cart, item.id);
    if (existing) {
      existing.qty = (existing.qty || 1) + 1;
    } else {
      cart.push({ ...item, qty: 1 });
    }
    saveCart(cart);
  }

  function removeFromCart(id) {
    let cart = loadCart();
    cart = cart.filter((it) => String(it.id) !== String(id));
    saveCart(cart);
  }

  function changeQty(id, qty) {
    const cart = loadCart();
    const it = findItem(cart, id);
    if (!it) return;
    it.qty = Math.max(0, Math.floor(qty));
    if (it.qty === 0) {
      removeFromCart(id);
      return;
    }
    saveCart(cart);
  }

  function clearCart() {
    localStorage.removeItem(CART_KEY);
    updateCartCount([]);
    renderCart([]);
  }

  function cartTotals(cart) {
    let total = 0;
    let count = 0;
    cart.forEach((it) => {
      total += (parseFloat(it.price) || 0) * (it.qty || 0);
      count += it.qty || 0;
    });
    return { total, count };
  }

  // UI
  function updateCartCount(cart = null) {
    if (cart === null) cart = loadCart();
    const totals = cartTotals(cart);
    cartCountEl.textContent = totals.count || 0;
    if ((totals.count || 0) === 0) {
      cartCountEl.classList.add("d-none");
    } else {
      cartCountEl.classList.remove("d-none");
    }
  }

  document.body.addEventListener("click", (e) => {
    const btn = e.target.closest(".add-to-cart");
    if (!btn) return;
    const id = btn.dataset.id || btn.getAttribute("data-id") || "";
    const cover = btn.dataset.cover || btn.getAttribute("data-cover") || "";
    const title =
      btn.dataset.title || btn.getAttribute("data-title") || "Товар";
    const price = btn.dataset.price || btn.getAttribute("data-price") || "0";
    addToCart({ id, cover, title, price });
    e.preventDefault();
  });

  function renderCart(cart = null) {
    if (cart === null) cart = loadCart();
    cartListEl.innerHTML = "";

    if (!cart.length) {
      cartEmptyEl.classList.remove("d-none");
      cartListEl.classList.add("d-none");
    } else {
      cartEmptyEl.classList.add("d-none");
      cartListEl.classList.remove("d-none");

      cart.forEach((item) => {
        const priceNum = parseFloat(item.price) || 0;
        const itemTotal = priceNum * (item.qty || 0);

        const el = document.createElement("div");
        el.className =
          "list-group-item d-flex flex-nowrap align-items-center justify-content-between";
        el.innerHTML = `
                  <div class="me-3" style="min-width: 15%;">
                      <img src="http://geekprint/src/data/product_covers/${
                        item.cover
                      }" 
                           class="modal-img img-fluid">
                  </div>
                  <div class="me-3" style="min-width: 35%;">
                      <div class="fw-bold">${escapeHtml(item.title)}</div>
                      <div class="text-muted small modal-price">Цена: ${formatPrice(
                        priceNum
                      )} x ${item.qty} = ${formatPrice(itemTotal)}</div>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                      <button class="btn btn-sm btn-outline-secondary cart-decr" data-id="${
                        item.id
                      }" title="Уменьшить">−</button>
                      <input class="form-control form-control-sm text-center cart-qty" data-id="${
                        item.id
                      }" type="text" min="1" value="${
          item.qty
        }" style="width:70px;">
                      <button class="btn btn-sm btn-outline-secondary cart-incr" data-id="${
                        item.id
                      }" title="Увеличить">+</button>
                      <button class="btn btn-sm btn-outline-danger cart-remove" data-id="${
                        item.id
                      }" title="Удалить">✕</button>
                  </div>
              `;
        cartListEl.appendChild(el);
      });
    }

    const totals = cartTotals(cart);
    cartTotalEl.textContent = formatPrice(totals.total);
  }

  function escapeHtml(text) {
    if (!text && text !== 0) return "";
    return String(text)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  cartListEl.addEventListener("click", (e) => {
    const decr = e.target.closest(".cart-decr");
    const incr = e.target.closest(".cart-incr");
    const rem = e.target.closest(".cart-remove");

    if (decr) {
      const id = decr.getAttribute("data-id");
      const cart = loadCart();
      const it = findItem(cart, id);
      if (it) changeQty(id, Math.max(1, it.qty - 1));
    } else if (incr) {
      const id = incr.getAttribute("data-id");
      const cart = loadCart();
      const it = findItem(cart, id);
      if (it) changeQty(id, it.qty + 1);
    } else if (rem) {
      const id = rem.getAttribute("data-id");
      removeFromCart(id);
    }
  });

  cartListEl.addEventListener("change", (e) => {
    const input = e.target.closest(".cart-qty");
    if (!input) return;
    const id = input.getAttribute("data-id");
    let val = parseInt(input.value, 10);
    if (isNaN(val) || val < 1) val = 1;
    changeQty(id, val);
  });

  clearCartBtn.addEventListener("click", (e) => {
    if (!confirm("Очистить корзину?")) return;
    clearCart();
  });

  orderForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!pickupCheck.checked && !addressInput.value.trim()) {
      alert("Выберите адрес на карте или введите его вручную.");
      return;
    }

    const cart = loadCart();
    if (!cart.length) {
      alert("Корзина пуста.");
      return;
    }

    const formData = {
      name: orderForm.orderName.value || "",
      phone: orderForm.orderPhone.value || "",
      comment: orderForm.orderComment.value || "",
      pickup: pickupCheck.checked,
      address: addressInput.value || "",
      cart,
    };

    try {
      const res = await fetch("/src/functions/order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData),
      });
      if (!res.ok) throw new Error("Ошибка сети");
      const data = await res.json();
      if (data.success) {
        alert(data.message || "Заказ создан.");
        clearCart();
        try {
          const modalEl = document.getElementById("cartModal");
          const bsModal =
            bootstrap.Modal.getInstance(modalEl) ||
            new bootstrap.Modal(modalEl);
          bsModal.hide();
        } catch (ee) {}
      } else {
        alert(data.message || "Ошибка при создании заказа.");
      }
    } catch (err) {
      console.error(err);
      alert("Ошибка отправки заказа. Смотри консоль.");
    }
  });

  // Инициализация UI
  updateCartCount();
  renderCart();
});
