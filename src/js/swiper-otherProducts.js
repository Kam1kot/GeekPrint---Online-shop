const swiperOP = new Swiper(".swiperOP", {
  // Optional parameters
  slidesPerView: 5,
  spaceBetween: 40,
  direction: "horizontal",
  loop: false,

  // If we need pagination
  pagination: {
    el: ".swiper-pagination",
  },
  breakpoints: {
    425: {
      slidesPerView: 2, // 2 отзыва в ряд
      spaceBetween: 40,
    },
    568: {
      slidesPerView: 2, // 2 отзыва в ряд
      spaceBetween: 40,
    },
    768: {
      slidesPerView: 3, // 2 отзыва в ряд
      spaceBetween: 50,
    },
    1024: {
      slidesPerView: 4, // 3 отзыва в ряд
      spaceBetween: 40,
    },
    1440: {
      slidesPerView: 5, // 3 отзыва в ряд
      spaceBetween: 40,
    },
    1920: {
      slidesPerView: 5, // 3 отзыва в ряд
      spaceBetween: 40,
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
