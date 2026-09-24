# Spec — Landing Tour Sapa 3 Ngày 2 Đêm

Landing Ads riêng (không Woo) cho tour **Cát Cát – Fansipan – Moana**.

Repo theme: `https://github.com/minhthangdev93/annam`  
URL: `/tour-sapa-3-ngay-2-dem/`  
Template: `page-template-tour-sapa-3n2d-landing.php`  
Prefix: `annam-tour-sapa-*` / `annam_tour_sapa_*`

---

## Mục tiêu

- Keyword phrase match QS~8: mosaic ảnh trải nghiệm **đầu trang** (#trai-nghiem).
- Giá công khai: **2.990.000đ** (KS 3★) / **3.990.000đ** (KS 4★) — **đã gồm** xe limo/cabin VIP HN ⇄ Sapa.
- Không gồm: vé Fansipan, buffet Sapa Xưa 400k.
- Lead form + conversion event `tour_sapa_booking_success` (dataLayer / gtag).

---

## Stack file

| File | Vai trò |
|------|---------|
| `inc/tour-sapa-landing-config.php` | Nội dung Word → config |
| `inc/tour-sapa-landing-page.php` | Detect template, enqueue, auto-create page |
| `inc/tour-sapa-landing-booking.php` | POST + AJAX + mail |
| `inc/tour-sapa-landing-images-admin.php` | 6 slot ảnh + email lead |
| `inc/annam-tour-sapa-landing-schema.php` | TouristTrip + Offer + FAQPage |
| `template-parts/tour-sapa-landing/*` | Markup |
| `assets/css/tour-sapa-landing.css` | Style |
| `assets/js/tour-sapa-landing.js` | Form AJAX, lightbox |

Admin: **Appearance → Tour Sapa Landing**.

---

## Sections (anchors Ads sitelinks)

1. `#trai-nghiem` — mosaic 1 lớn + 2×2, ô 5 = nút Gallery lightbox (6 ảnh)
2. `#dat-tour` — hero + form giữ chỗ
3. `#diem-noi-bat`
4. `#gia-tour`
5. `#lich-trinh`
6. `#bao-gom`
7. `#faq`
8. `#cta-cuoi` + sticky mobile (Gọi / Zalo / Giữ chỗ)

---

## Tracking

```js
dataLayer.push({
  event: 'tour_sapa_booking_success',
  eventCategory: 'tour_sapa_landing',
  form_id: 'annam-tour-sapa-form',
  landing: 'tour_sapa_3n2d',
  hotel, date, guests
});
```

GTM: trigger Custom Event `tour_sapa_booking_success` → Ads conversion.

---

## Ads Final URL

`https://<domain>/tour-sapa-3-ngay-2-dem/`

Sitelinks gợi ý: Trải nghiệm → `#trai-nghiem`, Giá → `#gia-tour`, Lịch trình → `#lich-trinh`, FAQ → `#faq`, Đánh giá → Trustindex (khối reviews trước CTA).

## Ảnh admin

**Appearance → Tour Sapa Landing**

1. **Thư viện đầu trang** — danh sách động (tối thiểu 6, thêm được nhiều hơn). Mosaic hiện 6 ô; lightbox mở toàn bộ. Caption chỉnh từng ảnh.
2. **Ảnh lịch trình** — 3 slot Ngày 01 / 02 / 03.
3. **Email nhận lead**.

Option: `annam_tour_sapa_landing_gallery`, `annam_tour_sapa_landing_images`, `annam_tour_sapa_landing_settings`.

- ATV mosaic: **Tour Sapa 3 ngày 2 đêm** + **Du lịch Sapa 3N2Đ**
- H1: Tour Sapa 3 Ngày 2 Đêm
- H2 giá: Giá Tour Sapa 3 Ngày 2 Đêm
- H2 lịch: Lịch Trình Tour Sapa 3 Ngày 2 Đêm
- Rank Math title/description mặc định (không ghi đè nếu đã nhập tay)
- GTM / RSA / sitelinks: cấu hình trong Ads (ngoài theme)
