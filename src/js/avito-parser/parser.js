const puppeteer = require("puppeteer");
const mysql = require("mysql2/promise");

(async () => {
  const url =
    "https://www.avito.ru/brands/ec8aea67ae5ca40fae709aa9d2e61c68/all?gdlkerfdnwq=101&page_from=from_item_card_icon&iid=7418041963&sellerId=82e5de2636ff05c30924d46394c6060f";

  const browser = await puppeteer.launch({ headless: true });
  const page = await browser.newPage();
  await page.goto(url, { waitUntil: "networkidle2" });

  await page.waitForSelector('[data-marker="profile/score"]', {
    timeout: 15000,
  });

  const data = await page.evaluate(() => {
    const parseDateToDDMMYYYY = (rawDate) => {
      if (!rawDate) return null;

      const months = {
        января: "01",
        февраля: "02",
        марта: "03",
        апреля: "04",
        мая: "05",
        июня: "06",
        июля: "07",
        августа: "08",
        сентября: "09",
        октября: "10",
        ноября: "11",
        декабря: "12",
      };

      const now = new Date();
      const defaultYear = now.getFullYear();

      const clean = rawDate.split("·")[0].trim();
      const parts = clean.split(" ");

      if (parts.length < 2) return null;

      const day = parts[0].padStart(2, "0");
      const month = months[parts[1]];
      if (!month) return null;

      let year = defaultYear;
      if (parts.length > 2) {
        year = parts[2].replace("года", "").trim();
      }

      return `${day}.${month}.${year}`;
    };

    const ratingEl = document.querySelector('[data-marker="profile/score"]');
    const rating = ratingEl
      ? parseFloat(ratingEl.textContent.trim().replace(",", "."))
      : null;

    const reviewsCountEl = document.querySelector(
      '[data-marker="profile/summary"]'
    );
    const totalReviews = reviewsCountEl
      ? parseInt(reviewsCountEl.textContent.trim())
      : null;

    const reviews = [];

    const reviewEls = document.querySelectorAll('[data-marker^="review("]');

    reviewEls.forEach((el, i) => {
      const base = `review(${i})`;

      const nameEl = el.querySelector('[data-marker$="/header/title"]');
      const dateEl = el.querySelector('[data-marker$="/header/subtitle"]');
      const textEl = el.querySelector('[data-marker$="/text-section/text"]');

      const rawDate = dateEl?.textContent.trim() || null;
      const date = parseDateToDDMMYYYY(rawDate);

      const stars = el.querySelectorAll(
        '[data-marker$="/score"] svg.styles-filled-eMoPj'
      ).length;

      if (nameEl && textEl) {
        reviews.push({
          name: nameEl.textContent.trim(),
          date,
          text: textEl.textContent.trim(),
          stars,
        });
      }
    });

    return { rating, totalReviews, reviews };
  });

  await browser.close();

  // Подклбчение БД
  const connection = await mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "geekprint",
  });

  for (const review of data.reviews) {
    await connection.execute(
      "INSERT INTO avito_reviews (rating, total_reviews, name, review_date, review_text, stars) VALUES (?, ?, ?, ?, ?, ?)",
      [
        data.rating,
        data.totalReviews,
        review.name,
        review.date,
        review.text,
        review.stars,
      ]
    );
  }

  await connection.end();
  console.log(`Готово: записано ${data.reviews.length} отзывов в базу!`);
})();
