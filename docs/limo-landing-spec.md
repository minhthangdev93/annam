# Spec tham khảo — Landing Vé Limousine HN ⇄ Sapa

## 1. Mục tiêu sản phẩm

Landing **bán vé limousine 10 chỗ** tuyến **Hà Nội ⇄ Sapa**:

- Trả lời nhanh: giá, lịch, điểm đón/trả, ảnh/video xe, FAQ.
- Thu lead giữ chỗ qua form (không thanh toán online ngay).
- CTA song song: **Gọi hotline** + **Zalo** + form.

Không dùng wording/giá đại lý nội bộ — chỉ giá niêm yết công khai.

---

## 2. Brand & liên hệ (landing-scoped)

CTA lấy từ `annam_contact_get_details()` (An Nam Discovery), có thể ghi đè qua filter `annam_limo_landing_cta`:

| Trường | Giá trị hiện tại |
|--------|------------------|
| Brand | An Nam Discovery |
| Hotline 1 | `1900 8164` / `tel:19008164` |
| Hotline 2 | `0942471111` / `tel:0942471111` |
| Zalo | `http://zalo.me/2127942034358673568` |

Khi clone brand khác: đổi trong `annam_limo_landing_get_cta()` hoặc filter `annam_limo_landing_cta`.

---

## 3. Mô tả từng khối (ngôn ngữ tự nhiên)

Thứ tự từ trên xuống — dùng làm brief khi làm lại landing gần giống.

### Banner đầu trang
Ảnh ngang lớn trải full chiều rộng màn hình (không nằm trong khung hẹp của theme). Thường là ảnh xe limousine hoặc hành trình; tỷ lệ ngang rộng (~2048×751) để thấy gần như nguyên khung xe, không bị cắt mạnh. Không có chữ đè lên ảnh — chỉ là hình mở đầu.

### Hero (phần “bán hàng” chính)
Hai cột trên desktop: bên trái là thông điệp sản phẩm, bên phải là form giữ chỗ.

**Cột trái:** nhãn nhỏ “Limousine 10 chỗ” → tiêu đề lớn (vé HN ⇄ Sapa) → một câu mô tả ngắn (2 chuyến/ngày, ~6 giờ, đón trả trong phạm vi) → dòng “Giá từ 450.000đ/ghế/chiều” → vài badge tin cậy (loại xe, giờ chạy, điểm đón, giữ chỗ nhanh) → ba nút: gọi hotline, chat Zalo, nhảy xuống bảng giá.

**Cột phải — form “Giữ chỗ trong 1 phút”:** khách chọn điểm đón / điểm trả (chỉ HN ⇄ Sapa), ngày đi, giờ theo chiều (HN→Sapa: 07:00/14:30; Sapa→HN: 07:30/14:30 — không có option “Chọn giờ”), hạng ghế, số khách, họ tên, SĐT/Zalo, và tùy chọn ghi điểm đón/trả mong muốn (placeholder đổi theo chiều). Gửi xong không thanh toán online; nhân viên gọi/Zalo xác nhận. Trên mobile, nút gọi/Zalo nằm phía trên form.

### Bảng giá (`#gia-ve`)
Ba thẻ giá ngang hàng: ghế giữa (badge “Phổ biến”, 500k), ghế đầu/cuối (450k), bao nguyên xe (badge “Nhóm”, 4.2tr). Mỗi thẻ có mô tả ngắn + nút “Chọn vé này” (điền hạng ghế vào form và kéo lên form). Dưới cùng một ghi chú: giá hai chiều trong phạm vi chuẩn; ngoài phạm vi có thể phụ thu.

### Lịch xe (`#lich-xe`)
Giới thiệu: 2 chuyến mỗi chiều mỗi ngày, khoảng 6 giờ. Tab chuyển **Hà Nội → Sapa** / **Sapa → Hà Nội**. Trong mỗi tab: hai nút giờ lớn (HN→Sapa: 07:00 sáng & 14:30 chiều; Sapa→HN: 07:30 sáng & 14:30 chiều) — bấm thì điền vào form; bên cạnh là timeline lộ trình dạng cột mốc (đón nội thành → Vĩnh Ngọc → Nội Bài → Lào Cai → Sapa, hoặc ngược lại). Nút “Chọn giờ này và giữ chỗ” kéo về form. Lào Cai chỉ là điểm trên lộ trình, không phải tab đón riêng.

### Điểm đón & trả (`#diem-don`)
Tab **Hà Nội** / **Sapa**. Mỗi điểm là một dòng có số thứ tự, tên địa điểm, icon đồng hồ + giờ đón dự kiến. Hà Nội liệt kê nhiều điểm (Phố Cổ, các VP, Vĩnh Ngọc…). Sapa: khách sạn trong ~5km + VP 697 Điện Biên Phủ. Ghi chú dưới list: giờ thực tế xác nhận trước chuyến; ngoài phạm vi có thể phụ thu.

### Hình ảnh xe (`#anh-xe`)
Lưới ảnh xe thật + caption dưới mỗi ảnh. Desktop: ảnh đầu lớn (cao bằng hai ảnh phụ), các ảnh còn lại xếp lưới; một ảnh chỉ hiện trên mobile để cân layout. Bấm ảnh mở lightbox xem lớn.

### Video YouTube (`#video-xe`)
Khối hẹp hơn, tiêu đề kiểu “Xem xe & hành trình thật”, một đoạn lead ngắn, rồi khung video 16:9 nhúng YouTube (URL chỉnh trong admin). Mục đích tạo niềm tin bằng hình động.

### Vì sao chọn (`#uu-diem`)
Bốn thẻ lý do (số thứ tự 01–04 + icon): xe 10 chỗ riêng tư hơn; lịch cố định theo chiều (HN→Sapa 07:00/14:30, Sapa→HN 07:30/14:30); đón trả tiện (HN + Sapa KS); giữ chỗ nhanh qua form/Zalo. Không phải bảng so sánh dài — chỉ 4 điểm bán.

### Đặt vé 3 bước (`#dat-ve`)
Ba bước ngang/dọc có số: (1) chọn tuyến & giờ & ghế, (2) gửi form giữ chỗ, (3) nhân viên xác nhận & hướng dẫn lên xe. Một nút “Giữ chỗ ngay” kéo lên form.

### FAQ (`#faq`)
Danh sách câu hỏi thường gặp dạng accordion (mở một cái thì đóng cái khác). Desktop xếp 2 cột. Nội dung xoay quanh giá, loại ghế, giờ chạy, điểm đón HN/Sapa, Nội Bài, bao xe, phụ thu.

### CTA cuối trang (`#cta-cuoi`)
Khối nhấn mạnh cuối: tiêu đề “Sẵn sàng giữ chỗ…?”, phụ đề nhắc giờ, nền có thể dùng ảnh featured của trang. Bốn nút: giữ chỗ (scroll form), Zalo, hotline 1, hotline 2.

### Nội dung SEO (tuỳ chọn)
Nếu trang WP có nội dung editor dài, hiện khối bài viết hẹp phía dưới; nếu dài quá thì thu gọn + nút “Xem thêm”. Không bắt buộc cho UX bán vé.

### Thanh dính mobile
Cố định đáy màn hình điện thoại: **Gọi** | **Zalo** | **Giữ chỗ** — luôn gọi được hành động chính khi cuộn dài.

---

## 4. Map kỹ thuật nhanh (thứ tự section)

Banner → Hero+form → Giá → Lịch → Đón trả → Gallery → Video → Why → 3 bước → FAQ → Final CTA → SEO → Sticky mobile + lightbox.

---

## 5. Map file (scaffold khi làm lại)

```
page-template-limousine-sapa-landing.php     # WP Page Template
inc/limo-landing-page.php                    # enqueue, body class, meta box H1, hooks
inc/limo-landing-config.php                  # content + CTA + sections flags
inc/limo-landing-routes.php                  # route map, times filter, settings, YouTube helpers, lead emails
inc/limo-landing-booking.php                 # POST + AJAX booking → email
inc/limo-landing-images-admin.php            # Admin: banner/gallery/captions + email + YouTube URL
inc/annam-limo-landing-schema.php            # JSON-LD Service + FAQ
template-parts/limo-landing/landing-page.php
template-parts/limo-landing/part-form.php
assets/css/limo-landing.css
assets/js/limo-landing.js
assets/img/limo-landing-banner.png           # fallback banner
```

`functions.php` require:

- `limo-landing-page.php`
- `limo-landing-images-admin.php`
- `annam-limo-landing-schema.php`

Shared mail: `inc/annam-lead-mail.php` → `annam_lead_send_notification()`.

---

## 6. Form giữ chỗ

### Fields

| Field | Required | Ghi chú |
|-------|----------|---------|
| Điểm đón / Điểm trả | * | Chỉ `hanoi` ⇄ `sapa` |
| Ngày đi | * | `input[type=date]`, min = hôm nay; **iOS cần CSS chống overflow** |
| Giờ đi | * | Theo chiều: HN→Sapa `07:00`/`14:30`, Sapa→HN `07:30`/`14:30`; **không** có option “Chọn giờ”; mặc định `07:00` (chiều HN→Sapa) |
| Hạng ghế | * | Ghế giữa / Ghế đầu–cuối / Bao xe |
| Số khách | | 1–20 |
| Họ tên, SĐT/Zalo | * | Validate phone |
| Điểm đón mong muốn | | Optional; placeholder đổi theo chiều |
| Điểm trả mong muốn | | Optional; placeholder đổi theo chiều |

Honeypot + timestamp + nonce + rate limit.

### UX JS

- Đổi điểm đón → rebuild điểm trả + giờ + placeholder đón/trả.
- Hôm nay: lọc giờ đã qua (+ lead hours); hết giờ → auto ngày mai + hint.
- AJAX submit + **fresh nonce** (tránh LiteSpeed cache nonce cũ).
- FAQ accordion exclusive open.

### Email lead

Subject dạng: `[LIMO HN-SAPA] Giữ chỗ — A → B — tên — SĐT`  
Body gồm tuyến, ngày/giờ, ghế, khách, tên, SĐT, điểm đón/trả mong muốn nếu có.  
Recipient: option admin `lead_emails`, fallback `admin_email`.

---

## 7. Giá & ghế (niêm yết mẫu)

| Loại | Giá (chiều) |
|------|-------------|
| Ghế giữa | 500.000đ |
| Ghế đầu / cuối | 450.000đ (hero “giá từ”) |
| Bao nguyên xe 10 chỗ | 4.200.000đ |

Áp dụng 2 chiều trong phạm vi đón/trả tiêu chuẩn.

---

## 8. Lịch & lộ trình

- Hà Nội → Sapa: **07:00** và **14:30**.
- Sapa → Hà Nội: **07:30** và **14:30**.
- Nguồn sự thật: `annam_limo_landing_get_schedule_times_map()`.
- Timeline mốc dự kiến (đón nội thành → Vĩnh Ngọc → Nội Bài → Lào Cai → Sapa và ngược lại). Lào Cai chỉ là **mốc lộ trình**, không còn tab điểm đón riêng.
- UI: card lịch + nút giờ lớn + CTA “Chọn giờ này và giữ chỗ” (đẩy data vào form).

---

## 9. Điểm đón/trả (tabs)

- **Hà Nội**: list điểm + giờ đón dự kiến (Phố Cổ, VP Minh Khai, …, Vĩnh Ngọc).
- **Sapa**: KS trong 5km + VP 697 Điện Biên Phủ.
- Card có số thứ tự + icon đồng hồ; note phụ thu ngoài phạm vi.

---

## 10. Media

### Banner

- Full viewport width (breakout khỏi GP container).
- Aspect ~2048/751; không crop bánh xe (`height: auto` / contain).
- Slot admin `hero-banner` + fallback file theme.

### Gallery

- Desktop: ảnh 1 span 2 hàng cao bằng 2 ảnh phải; 4 ảnh phụ; ảnh 6 **chỉ mobile**.
- Caption editable trong admin; **không** để caption trong `<button>` (GP ép chữ trắng).

### Video YouTube

- Section sau gallery.
- URL trong settings admin; placeholder mặc định nếu trống.
- Embed `youtube-nocookie.com`, khung 16:9.

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

- Mở full viewport: override `#page.site.grid-container` max-width.
- Chống GeneratePress `button { color:#fff }` bằng selector `body.annam-limo-landing-page ... !important` cho tabs/time/CTA.
- Date iOS: `min-width:0`, `max-width:100%`, `-webkit-appearance:none` trên field/form.

---

## 12. Admin

Menu: **An Nam Settings → Landing Limousine HN–Sapa**

1. Email nhận lead + **Link YouTube**
2. Ảnh banner + gallery + **caption từng ảnh**

Page editor (meta):

- H1 tùy chọn
- Tắt SEO section
- Featured image → nền Final CTA

---

## 13. SEO / Schema

- JSON-LD: `Service` + `Offer` từ pricing + `FAQPage` + breadcrumb.
- Nội dung SEO dài: lấy `post_content` trang, toggle “Xem thêm”.

---

## 14. Checklist khi clone / làm lại gần giống

1. Copy scaffold file + đổi prefix brand nếu cần.
2. Đổi CTA hotline/Zalo/brand trong `get_cta()`.
3. Điền config: hero, giá, pickup tabs, FAQ, gallery captions.
4. Upload banner đúng tỷ lệ + ảnh gallery thật + YouTube thật.
5. Tạo Page WP → template Limousine → publish.
6. Cấu hình SMTP (form dùng `wp_mail` / lead helper).
7. Test: mobile date field, đổi chiều form, AJAX giữ chỗ, FAQ accordion, LiteSpeed + fresh nonce.
8. Git: commit trong repo theme `minhthangdev93/annam`, không nhầm remote `wp-content`.

---

## 15. Pattern kiến trúc (tái sử dụng)

| Layer | Việc |
|-------|------|
| Config PHP | Nội dung tĩnh + flags section |
| Page template | Chỉ `get_header` + `get_template_part` |
| Template parts | Markup section |
| CSS riêng + body class | Full-bleed, chống theme parent |
| JS nhỏ IIFE | Form/route/tabs/gallery/FAQ/nonce |
| Admin options | Ảnh, caption, email, video URL |
| Booking | Sanitize → validate → email lead |
| Schema | Graph riêng landing |

Landing **cabin VIP** và **thuê xe** cùng monorepo theme dùng pattern tương tự — có thể mirror cấu trúc khi làm tuyến/sản phẩm mới.
