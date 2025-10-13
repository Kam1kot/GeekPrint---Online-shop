const swiper = new Swiper(".swiper", {
  // Optional parameters
  slidesPerView: 2,
  direction: "horizontal",
  loop: true,

  // If we need pagination
  pagination: {
    el: ".swiper-pagination",
  },
  breakpoints: {
    468: {
      slidesPerView: 1, // 1 отзыв в ряд
      spaceBetween: 50,
    },
    568: {
      slidesPerView: 1, // 2 отзыва в ряд
      spaceBetween: 20,
    },
    768: {
      slidesPerView: 2, // 2 отзыва в ряд
      spaceBetween: 30,
    },
    1024: {
      slidesPerView: 2, // 3 отзыва в ряд
      spaceBetween: 35,
    },
    1440: {
      slidesPerView: 3, // 3 отзыва в ряд
      spaceBetween: 35,
    },
  },
  // Navigation arrows
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },

  // And if we need scrollbar
  scrollbar: {
    el: ".swiper-scrollbar",
  },
});
