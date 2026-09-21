# Spec tham khảo — Landing Vé Limousine HN ⇄ Sapa

Tài liệu mô tả landing **hiện tại** (GeneratePress child) để làm lại gần như cùng pattern.

Repo theme: `https://github.com/minhthangdev93/annam`  
Template WP: `page-template-limousine-sapa-landing.php`  
Prefix CSS/JS/PHP: `annam-limo-*` / `annam_limo_*`  
Nguồn nội dung: `inc/limo-landing-config.php`

---

## 1. Mục tiêu sản phẩm

Landing **bán vé Limousine 11 chỗ** tuyến **Hà Nội ⇄ Sapa**:

- Trả lời nhanh: giá, lịch, điểm đón/trả, ảnh/video xe, FAQ.
- Thu lead giữ chỗ qua form (không thanh toán online ngay).
- CTA song song: **Gọi hotline** + **Zalo** + form.

Chỉ hiển thị **giá niêm yết công khai**. Không đưa giá/chính sách nội bộ hay wording đại lý.

**Lưu ý chỗ ngồi:** “11 chỗ” đã **gồm ghế tài xế**. Hành khách tối đa **10 ghế** = 06 ghế giữa + 01 ghế đầu + 03 ghế cuối.

---

## 2. Brand & liên hệ

CTA lấy từ `annam_contact_get_details()` (An Nam Discovery), ghi đè qua filter `annam_limo_landing_cta`:

| Trường | Giá trị hiện tại |
|--------|------------------|
| Brand | An Nam Discovery |
| Hotline 1 | `1900 8164` / `tel:19008164` |
| Hotline 2 | `0942471111` / `tel:0942471111` |
| Zalo | `http://zalo.me/2127942034358673568` |

Clone brand khác: đổi trong `annam_limo_landing_get_cta()`.

---

## 3. Mô tả từng khối (ngôn ngữ tự nhiên)

Thứ tự từ trên xuống.

### Banner đầu trang
Ảnh ngang full viewport (~tỷ lệ 2048×751), không chữ đè lên ảnh.

### Hero
Hai cột desktop: copy trái | form phải.

**Trái:** eyebrow “Limousine 11 chỗ” → H1 vé HN ⇄ Sapa → subtitle (2 chuyến/ngày, giờ theo chiều, ~6 giờ) → “Giá từ 450.000đ/ghế/chiều” → badges (11 chỗ gồm ghế tài xế; lịch HN→Sapa / Sapa→HN; điểm đón; giữ chỗ nhanh) → nút Gọi / Zalo / Xem bảng giá.

**Phải — form:** điểm đón/trả (chỉ HN ⇄ Sapa), ngày, giờ theo chiều, hạng ghế, số khách, họ tên, SĐT/Zalo, điểm đón/trả mong muốn (optional). Mobile: nút gọi/Zalo phía trên form.

### Bảng giá (`#gia-ve`)
Lead: hai chiều + “Xe 11 chỗ đã gồm ghế tài xế — tối đa 10 ghế khách”.

Ba thẻ: ghế giữa (Phổ biến, 500k) · ghế đầu/cuối (450k) · bao nguyên xe (Nhóm, **4.500.000đ** — bao 10 ghế khách). Nút “Chọn vé này” đổ ghế vào form. Ghi chú dưới: giải thích 11 chỗ / 10 ghế khách + phụ thu ngoài phạm vi.

### Lịch xe (`#lich-xe`)
Tabs **Hà Nội → Sapa** / **Sapa → Hà Nội**. Nút giờ lớn + timeline:

- HN→Sapa: đón nội thành → **Sảnh Royal City** (đúng giờ) → Nội Bài (~40 phút) → Lào Cai (~5 giờ) → Sapa (~6 giờ).
- Sapa→HN: đón Sapa/VP 697 → xuất phát 07:30/14:30 → Lào Cai (~30 phút) → Nội Bài (~5 giờ hơn) → nội thành HN (~6 giờ).

CTA “Chọn giờ này và giữ chỗ”. Lào Cai chỉ là mốc lộ trình.

### Điểm đón & trả (`#diem-don`)
**Hà Nội:** 214 TQK & Phố Cổ · Nhà Hát Lớn · Rạp Xiếc · Mediamart 72 Trường Chinh · **Sảnh Royal City** (đúng giờ) · 23 Tú Mỡ · Lotte Mall Tây Hồ.

**Sapa:** KS khu vực thị trấn · VP 697 Điện Biên Phủ (chiều về).

Note: giờ xác nhận trước chuyến; phụ thu ngoài phạm vi; có thể trung chuyển Phố Cổ.

### Gallery (`#anh-xe`)
Ảnh + caption; lightbox prev/next/đóng (SVG nút tròn). Ảnh 6 chỉ mobile.

### Video (`#video-xe`)
Embed YouTube 16:9, URL admin.

### Vì sao chọn (`#uu-diem`)
4 thẻ: 11 chỗ (gồm tài xế / tối đa 10 ghế khách) · lịch cố định · đón trả tiện · giữ chỗ nhanh.

### Đặt vé 3 bước (`#dat-ve`)
Chọn tuyến & giờ → gửi form → xác nhận lên xe.

### FAQ (`#faq`)
Accordion exclusive; desktop 2 cột. Giá, loại ghế, giờ chạy, đón HN/Sapa, Nội Bài, bao xe, phụ thu. **Không** FAQ kiểu “11 chỗ có phải 11 ghế khách?”.

### CTA cuối (`#cta-cuoi`)
Giữ chỗ + Zalo + 2 hotline; nền featured image tùy chọn.

### SEO (tuỳ chọn)
`post_content` trang; collapse + “Xem thêm” nếu dài.

### Sticky mobile
Dock nổi: icon + **Gọi** | **Zalo** | **Giữ chỗ**.

---

## 4. Map kỹ thuật nhanh

Banner → Hero+form → Giá → Lịch → Đón trả → Gallery → Video → Why → 3 bước → FAQ → Final CTA → SEO → Sticky + lightbox.

---

## 5. Map file

```
page-template-limousine-sapa-landing.php
inc/limo-landing-page.php
inc/limo-landing-config.php                  # nội dung + lịch + giá + FAQ
inc/limo-landing-routes.php                  # route, filter giờ theo chiều/ngày
inc/limo-landing-booking.php
inc/limo-landing-images-admin.php
inc/annam-limo-landing-schema.php
template-parts/limo-landing/landing-page.php
template-parts/limo-landing/part-form.php
assets/css/limo-landing.css
assets/js/limo-landing.js
assets/img/limo-landing-banner.png
```

`functions.php` require: `limo-landing-page.php`, `limo-landing-images-admin.php`, `annam-limo-landing-schema.php`.

Mail: `inc/annam-lead-mail.php` → `annam_lead_send_notification()`.

---

## 6. Form giữ chỗ

| Field | Required | Ghi chú |
|-------|----------|---------|
| Điểm đón / Điểm trả | * | Chỉ `hanoi` ⇄ `sapa` |
| Ngày đi | * | `type=date`, min hôm nay; **iOS chống overflow** |
| Giờ đi | * | Theo chiều qua `annam_limo_landing_get_schedule_times_map()`; không option “Chọn giờ”; mặc định `07:00` (HN→Sapa) |
| Hạng ghế | * | Ghế giữa / Ghế đầu–cuối / Bao nguyên xe (11 chỗ gồm tài xế) |
| Số khách | | 1–20 |
| Họ tên, SĐT/Zalo | * | Validate phone |
| Điểm đón / trả mong muốn | | Optional; placeholder đổi theo chiều |

Honeypot + timestamp + nonce + rate limit.

### UX JS

- Đổi chiều → rebuild điểm trả + giờ + placeholder.
- Hôm nay: lọc giờ đã qua (+ lead hours); hết giờ → auto ngày mai.
- AJAX + **fresh nonce** (LiteSpeed).
- FAQ accordion exclusive; gallery lightbox prev/next/Esc.

### Email lead

Subject: `[LIMO HN-SAPA] Giữ chỗ — A → B — tên — SĐT`  
Recipient: admin option `lead_emails`, fallback `admin_email`.

---

## 7. Giá & ghế (niêm yết)

| Loại | Chi tiết | Giá / chiều |
|------|----------|-------------|
| Ghế giữa | 06 ghế khách giữa xe | 500.000đ |
| Ghế đầu / cuối | 01 đầu + 03 cuối (ghế khách) | 450.000đ (hero “giá từ”) |
| Bao nguyên xe | Toàn bộ 10 ghế khách trên xe 11 chỗ (gồm tài xế) | **4.500.000đ** |

Áp dụng 2 chiều trong phạm vi đón/trả tiêu chuẩn.

---

## 8. Lịch & lộ trình

Nguồn sự thật: `annam_limo_landing_get_schedule_times_map()` / `annam_limo_landing_departure_times( $from, $to )`.

| Chiều | Sáng | Chiều |
|--------|------|--------|
| Hà Nội → Sapa | **07:00** | **14:30** |
| Sapa → Hà Nội | **07:30** | **14:30** |

Timeline / pickup: xem mục 3 (Royal City = mốc đúng giờ HN→Sapa).

---

## 9. Điểm đón/trả (tabs)

- **Hà Nội:** 214 TQK & Phố Cổ, Nhà Hát Lớn, Rạp Xiếc, 72 Trường Chinh, **Sảnh Royal City**, 23 Tú Mỡ, Lotte Tây Hồ.
- **Sapa:** KS khu vực thị trấn + VP 697 Điện Biên Phủ.
- Số thứ tự + giờ dự kiến; note phụ thu / trung chuyển.

---

## 10. Media

### Banner
Full-bleed; aspect ~2048/751 contain; slot `hero-banner`.

### Gallery
Desktop: ảnh 1 lớn + lưới phụ; ảnh 6 mobile-only. Caption ngoài `<button>`. Lightbox: SVG close/prev/next nút tròn.

### Video
YouTube nocookie 16:9; URL admin.

---

## 11. Design tokens (CSS)

```css
--annam-limo-green: #0f766e;
--annam-limo-orange: #ea580c;
--annam-limo-zalo: #0068ff;
--annam-limo-text: #1f2937;
--annam-limo-muted: #6b7280;
--annam-limo-radius: 14px;
```

- Override GP container full width + `button { color:#fff }`.
- Sticky: dock nổi, icon SVG, safe-area.
- Date iOS: `min-width:0`, `max-width:100%`, `-webkit-appearance:none`.

---

## 12. Admin

**An Nam Settings → Landing Limousine HN–Sapa**

1. Email lead + YouTube URL  
2. Banner + gallery + caption từng ảnh  

Page meta: H1 tùy chọn · tắt SEO · featured → nền Final CTA.

---

## 13. SEO / Schema

JSON-LD: `Service` + `Offer` + `FAQPage` + breadcrumb. SEO dài từ `post_content`.

---

## 14. Checklist clone / làm lại

1. Copy scaffold + đổi prefix nếu cần.
2. CTA hotline/Zalo/brand trong `get_cta()`.
3. Config: hero, giá 11 chỗ, pickup (Royal City…), FAQ, captions — **không** wording đại lý.
4. Banner/gallery/YouTube thật.
5. Page WP → template Limousine → publish.
6. SMTP cho lead mail.
7. Test: iOS date, đổi chiều giờ, AJAX + fresh nonce, lightbox, sticky, FAQ.
8. Git: repo `minhthangdev93/annam` (không nhầm remote `wp-content`).

---

## 15. Pattern kiến trúc

| Layer | Việc |
|-------|------|
| Config PHP | Nội dung + lịch theo chiều + flags |
| Page template | `get_header` + `get_template_part` |
| Template parts | Markup section |
| CSS + body class | Full-bleed, chống GP |
| JS IIFE | Form/route/tabs/gallery/FAQ/nonce |
| Admin | Ảnh, caption, email, video |
| Booking | Sanitize → validate → email |
| Schema | Graph riêng landing |

Landing **cabin VIP** / **thuê xe** cùng theme dùng pattern tương tự.

---

## 16. Biến thể đa site (tránh 2 landing giống hệt)

Hai site có thể **cùng sản phẩm / cùng thứ tự section / cùng số liệu vận hành**, nhưng phải **khác brand skin + giọng + cách nhấn layout**. Không copy nguyên UI An Nam sang site kia.

### Giữ nguyên giữa các site

- Thứ tự khối: Banner → Hero+form → Giá → Lịch → Đón trả → Media → Why → 3 bước → FAQ → CTA → Sticky.
- Logic: lịch theo chiều, form giữ chỗ, giá niêm yết, điểm đón, không wording đại lý.
- Spec này vẫn là nguồn sự thật nghiệp vụ (giờ, giá, ghế, pickup).

### Bắt buộc đổi (ưu tiên)

1. **Brand skin** — token màu khác hẳn (không teal+cam An Nam); font display/body khác; chọn một hướng radius/nút (bo mềm vs góc vuông/pill), không trùng bộ An Nam.
2. **Hero composition** — không chỉ đổi logo. Ví dụ site B: form dưới hero full-bleed, hoặc form card nổi giữa, hoặc cột hẹp khác tỷ lệ — không clone 2 cột An Nam y nguyên.
3. **Media** — banner, gallery, YouTube **file riêng** từng brand; caption giọng khác.
4. **Nhấn section nhẹ** — vẫn đủ khối bắt buộc; có thể đảo nhấn (Why trước Gallery, Video sát Hero). FAQ cùng ý nhưng diễn đạt khác.
5. **Copy & CTA** — hotline/Zalo/brand đúng từng site; lead/subtitle/FAQ không paste nguyên An Nam; nhãn nút có thể khác nhẹ.
6. **Prefix code** — đổi `annam-limo-*` → prefix brand B; config + filter CTA riêng; không share class CSS An Nam.

### Quy tắc cho AI / dev site thứ hai

> Clone **logic + thứ tự section** từ spec này, nhưng **thiết kế lại skin + hero composition + media + copy**. Không tái sử dụng màu/font/layout hero An Nam. Test nhanh: bỏ logo/nav vẫn không nhầm với site kia.

### Checklist “đã khác chưa?”

- [ ] Screenshot mobile hero 2 site cạnh nhau — khác rõ trong 5 giây.
- [ ] Màu / font / radius không trùng bộ An Nam.
- [ ] Banner + ≥4 ảnh gallery + video riêng brand.
- [ ] FAQ / lead / CTA wording không copy nguyên.
- [ ] Số liệu vận hành (giờ / giá / điểm đón / ghế) vẫn đúng phụ lục.
