# Kế hoạch bàn giao: Audit và redesign landing Limousine Hà Nội ⇄ Hạ Long

**Trang:** http://annamwp.local/xe-limousine-ha-noi-ha-long/ (page ID 15460, template `page-template-limousine-halong-landing.php`)

## 0. Bối cảnh

Trang đã chạy được trên module dùng chung `limo-landing`: route profile, form AJAX, schema, tracking. Mục tiêu đợt này:

1. Sửa các lỗi ảnh hưởng trực tiếp tới chuyển đổi và tới chính sách quảng cáo của Google Ads.
2. Làm lại giao diện cho hiện đại, mang đúng chất ngành vận tải, và **dùng đúng màu thương hiệu An Nam Discovery** (hiện landing đang dùng bảng màu riêng, lệch thương hiệu).

**Phạm vi:** chỉ sửa giao diện, markup và nội dung. Không đụng logic booking, route profile hay tracking đã có, trừ khi ghi rõ bên dưới.

**Đã chốt với chủ dự án:**
- **Chỉ áp dụng cho landing Hạ Long.** Trang Sapa giữ nguyên giao diện, màu sắc và section. Mọi CSS mới phải scope dưới class `.annam-limo-landing--halong`, mọi section mới bật bằng cờ config của route `halong`.
- **Ảnh nằm ngoài phạm vi.** Chủ dự án tự thay toàn bộ ảnh sau (banner, gallery, ảnh nền). Dev giữ nguyên các khối ảnh và cơ chế ảnh hiện có, không xử lý nội dung ảnh.
- **Đơn được xác nhận tức thì** vì tổng đài trực 24/7. Mọi câu chữ "sale gọi lại", "xác nhận trong giờ làm việc" đổi thành "Xác nhận tức thì · Tổng đài trực 24/7".
- **Đánh giá:** lấy từ trang Google Maps của công ty (https://maps.app.goo.gl/fRM5drrkoftTc2139). Cách làm ở mục 3.9.

**Cách audit:** đọc HTML render thực tế (`curl` trang local), file CSS/template và ảnh trong theme. Session này không có công cụ trình duyệt nên **chưa có ảnh chụp màn hình**. Dev cần chụp màn hình trước/sau ở hai khổ 390px và 1440px để đối chiếu.

---

## 1. Kết quả audit

### P0: chặn go-live hoặc chạy Ads

| # | Vấn đề | Bằng chứng | Rủi ro |
|---|---|---|---|
| A1 | **Màn hình đầu trên mobile không thấy form.** Thứ tự hiện tại: banner → H1 dài → subtitle 3 câu → 4 badge → 2 nút → form. | `.annam-limo-hero__grid` 1 cột dưới 900px | Tỷ lệ chuyển đổi trên mobile thấp. Mobile chiếm phần lớn traffic Ads. |

### P1: thiết kế và thương hiệu

| # | Vấn đề | Bằng chứng |
|---|---|---|
| B1 | **Landing dùng bảng màu riêng**: xanh `#0f766e` và cam `#ea580c`. Màu thương hiệu thật là teal `#01AA90`, teal đậm `#0F4C4C` và cam `#F58220` (`design-tokens.css`). | `limo-landing.css:5-14` |
| B2 | Hero chỉ là gradient xanh nhạt + chữ, nhạt và thiếu điểm nhấn, chưa truyền được cảm giác "limousine cao cấp". | `.annam-limo-hero` (`limo-landing.css:105`) |
| B3 | Bảng giá là 3 thẻ giống nhau, **có tới 2 thẻ "highlight"** (ghế cuối, ghế giữa). Không có sơ đồ ghế nên khách không hình dung được "ghế đầu/giữa/cuối" nằm ở đâu. | `#gia-ve` |
| B4 | Lịch xe chỉ là 2 nút giờ, chưa thể hiện được **lộ trình**: điểm đón → cao tốc 5B → Hạ Long, mất khoảng 2h30. | `#lich-xe` |
| B5 | Không có **bằng chứng xã hội**: đánh giá, số khách đã phục vụ. | Không có từ "đánh giá" hay review nào trong HTML |
| B6 | Trang có nhiều lối thoát làm phân tán khách từ Ads: menu header, carousel logo "hệ sinh thái" 6 thương hiệu ở footer (có cả thương hiệu limousine khác). | `.annam-ecosystem`, `#masthead` |
| B7 | Nút Zalo dùng xanh `#0068ff` cạnh cam và teal, nên trong hero có 3 màu nút cạnh tranh nhau. | `.annam-limo-btn--zalo` |

### P2: kỹ thuật

| # | Vấn đề |
|---|---|
| C2 | Trang tải 12 stylesheet và 13 script. Cần dequeue các asset không dùng trên landing (WooCommerce, slider trang chủ...). |
| C3 | Độ tương phản: chữ trắng trên `#F58220` chỉ đạt khoảng 2.9:1, chữ trắng trên `#01AA90` khoảng 2.9:1, cả hai **không đạt WCAG AA**. Khi đổi sang màu thương hiệu bắt buộc xử lý (xem mục 3.1). |

### Điểm làm tốt, cần giữ

- Form rút gọn chỉ cần số điện thoại, phần chi tiết chuyến đi ẩn sau nút "Sửa chuyến".
- Có sticky bar trên mobile.
- Bấm chọn giờ hoặc ghế thì form được điền sẵn.
- FAQ và schema đầy đủ.
- Có hidden field gclid/utm.

---

## 2. Các hướng thiết kế

| Hướng | Mô tả | Ưu | Nhược |
|---|---|---|---|
| **A. "Transit Premium" (khuyến nghị)** | Hero nền teal đậm `#0F4C4C`. Widget đặt vé kiểu vé xe/vé máy bay (HN ⇄ HL có nút đảo chiều). CTA màu cam thương hiệu. Phần dưới nền sáng. | Đúng chất ngành vận tải (giống các nền tảng đặt vé lớn). Nổi bật trên mobile. Dùng màu thương hiệu làm chủ đạo. | Khối lượng CSS vừa phải. |
| B. "Light Booking" | Giữ bố cục hiện tại, chỉ đổi token sang màu thương hiệu, thêm sơ đồ ghế và trust bar. | Làm nhanh, rủi ro thấp. | Vẫn nhạt, ít khác biệt so với đối thủ. |
| C. "Vịnh Hạ Long" | Hero ảnh vịnh/du thuyền toàn màn hình, nhấn mạnh trải nghiệm du lịch. | Cảm xúc tốt, hợp khách quốc tế. | Lệch trọng tâm "bán vé xe". Chữ trên ảnh khó đọc. LCP nặng. |

**Phần còn lại của kế hoạch triển khai theo hướng A.**

---

## 3. Đặc tả redesign (hướng A)

### 3.1. Design token: dùng màu thương hiệu (chỉ trang Hạ Long)

**Không sửa** khối biến gốc ở `limo-landing.css:5-14`, vì trang Sapa dùng chung. Thay vào đó:

1. Trong `annam_limo_landing_body_class()` (`inc/limo-landing-page.php`), thêm class `annam-limo-landing--{route}`.
2. Ghi đè giá trị biến bên dưới class `.annam-limo-landing--halong`. Toàn bộ style mới cũng đặt dưới class này, có thể tách sang file riêng `assets/css/limo-landing-halong.css` và chỉ enqueue khi route là `halong`.

```css
.annam-limo-landing--halong {
  --annam-limo-ink:        #0F4C4C;  /* teal đậm thương hiệu: nền hero, tiêu đề */
  --annam-limo-ink-2:      #0A3636;  /* nền footer CTA, hover */
  --annam-limo-green:      #01AA90;  /* teal thương hiệu: icon, viền, chip; KHÔNG dùng làm nền chữ trắng */
  --annam-limo-green-dark: #0F4C4C;
  --annam-limo-orange:     #F58220;  /* cam thương hiệu: nền CTA chính */
  --annam-limo-orange-dark:#E0771C;
  --annam-limo-on-orange:  #1A1206;  /* chữ trên nút cam, tương phản ~8:1 */
  --annam-limo-amber:      #FAA519;  /* giá, sao đánh giá */
  --annam-limo-mint:       #E6FBF7;  /* nền section xen kẽ */
  --annam-limo-text:       #1E293B;
  --annam-limo-muted:      #64748B;
  --annam-limo-border:     #E2E8F0;
  --annam-limo-radius:     16px;
  --annam-limo-shadow:     0 10px 30px rgba(15, 76, 76, .12);
}
```

**Quy tắc màu:**
- Nút chính: nền cam, chữ `--annam-limo-on-orange`, đậm 700.
- Trên nền teal đậm: chữ trắng (khoảng 9.9:1).
- Teal `#01AA90` chỉ dùng cho icon, đường viền và chữ cỡ lớn trên nền tối.
- Nút Zalo: dạng outline trắng/teal, có icon Zalo. Bỏ nền xanh `#0068ff` để hero chỉ còn **một** màu nút nổi bật là cam.

**Typography:** giữ Be Vietnam Pro.
- H1: `clamp(1.75rem, 4vw, 2.75rem)`, weight 800.
- Số giá và giờ dùng `font-variant-numeric: tabular-nums` để hiển thị đều như bảng giờ tàu xe.

### 3.2. Header và footer riêng cho landing (giảm lối thoát)

`inc/limo-landing-page.php`, khi `annam_limo_landing_is_template()` đúng:

- **Header rút gọn:** logo An Nam bên trái; hotline `1900 8164` (nút gọi) và nút Zalo bên phải. Ẩn menu điều hướng bằng cách thêm body class và CSS, hoặc hook GeneratePress `generate_header`.
- **Footer:** ẩn `.annam-ecosystem` (carousel logo thương hiệu khác) và chỉ giữ dòng pháp lý: tên công ty, địa chỉ văn phòng, hotline, email. Google Ads yêu cầu landing có thông tin doanh nghiệp, nên **không ẩn toàn bộ footer**.

### 3.3. Hero mới (sửa A1, B2)

Giữ nguyên khối banner `.annam-limo-banner` phía trên hero. Chỉ làm lại phần hero bên dưới.

Bố cục trên desktop từ 900px trở lên (2 cột, nền `--annam-limo-ink`):

```
┌──────────────────────────────────────────────────────────────┐
│ [chip] Limousine 11 chỗ · Ghế massage                          │
│ H1: Limousine Hà Nội ⇄ Hạ Long                  ┌───────────┐ │
│     chỉ từ 300.000đ                             │ WIDGET    │ │
│ ✓ Đón tận khách sạn Phố Cổ & TP. Hạ Long        │ ĐẶT VÉ    │ │
│ ✓ 2h–2h30 qua cao tốc 5B                        │ (3.4)     │ │
│ ✓ Đổi vé miễn phí · Hủy trước 12h hoàn 100%     └───────────┘ │
│ ★ 4.9 · 1.200+ khách  |  1900 8164                             │
└──────────────────────────────────────────────────────────────┘
```

Trên mobile dưới 900px, **form phải nằm trong màn hình đầu ở khổ 390×844**:

1. Chip và H1 ngắn: "Limousine Hà Nội ⇄ Hạ Long · từ 300K". Tối đa 2 dòng.
2. Một dòng gom 3 lợi ích, dạng icon nhỏ, cuộn ngang được.
3. **Widget đặt vé ngay sau đó.**
4. Bỏ 2 nút "Gọi" và "Zalo" trong hero mobile, vì sticky bar đã có.

Viết lại `hero.subtitle` trong config Hạ Long cho ngắn, chỉ 1 câu. Lịch giờ đã có ở widget và section lịch xe nên không nhắc lại ở đây.

### 3.4. Widget đặt vé dạng "vé xe"

Sửa markup trong `template-parts/limo-landing/part-form.php` và CSS. **Giữ nguyên `name`/`id` của mọi input** để JS, booking và tracking không phải sửa.

```
┌ ĐẶT VÉ ───────────────────────────────┐
│ [Hà Nội]  ⇄  [Hạ Long]                 │  ← nút ⇄ đảo from/to
│ [Hôm nay][Ngày mai][📅 Chọn ngày]      │  ← chip ngày, map vào input date
│ Giờ: [07:00] [08:00]                   │  ← chip giờ, map vào select time (ẩn select)
│ Ghế: Cuối 300K · Đầu 320K · Giữa 340K  │  ← segmented control, map vào select seat
│ Khách: [–] 1 [+]                       │
│ ────────── đường cắt răng cưa ──────── │
│ Tạm tính: 340.000đ                     │  ← giá × số khách, cập nhật realtime
│ [SĐT / Zalo / WhatsApp            ]    │
│ [ GIỮ CHỖ NGAY — XÁC NHẬN TỨC THÌ ]   │  ← nút cam full width
│ 🔒 Không thanh toán online · Đổi miễn phí │
└───────────────────────────────────────┘
```

- **Bỏ trạng thái ẩn "Sửa chuyến".** Các trường chuyến đi hiện sẵn dưới dạng chip, chỉ 1 chạm mỗi lựa chọn. Chỉ ô số điện thoại cần gõ.
- Chip giờ dùng lại logic lọc giờ hiện có. Chip giờ hết hạn thì `disabled`, kèm tooltip "Đã quá giờ đặt".
- "Tạm tính" dùng `seatPrices` đã localize sẵn. Ghế "thuê nguyên xe" hiển thị 3.000.000đ/xe.
- Dòng phụ dưới nút: "Tổng đài trực 24/7 · Xác nhận tức thì". Đổi luôn các chuỗi `form.subtitle`, `form.footer_note`, `form.success_message` và `steps` trong config Hạ Long cho đồng bộ.
- Hiệu ứng vé: `border-radius` 16px, 2 khuyết tròn ở hai bên bằng `radial-gradient` mask, đường kẻ đứt `border-top: 2px dashed`.

### 3.5. Thanh tin cậy ngay dưới hero

Một dải 4 ô trên nền trắng, nổi lên đè một phần hero:
- Đánh giá Google ★ x.x
- Số khách / số chuyến đã chạy
- "Đơn vị lữ hành An Nam Discovery", kèm số giấy phép lữ hành nếu có
- "Hoàn 100% nếu hủy trước 12h"

Số liệu lấy từ config mới `trust_items` trong `inc/limo-landing-config-halong.php`. **Không được để số liệu giả**: nếu chưa có số thật thì ẩn ô đó.

### 3.6. Bảng giá thành sơ đồ ghế (B3)

Section `#gia-ve`:
- **Trái:** SVG sơ đồ xe 11 chỗ nhìn từ trên xuống (tài xế + 1 ghế đầu, 3 hàng × 2 ghế giữa, 3 ghế cuối). Mỗi nhóm ghế tô màu theo hạng. Hover hoặc chạm vào nhóm ghế thì highlight thẻ giá tương ứng và gọi `data-annam-pick-seat`.
- **Phải:** 3 hàng giá dạng list (tên ghế, số ghế, mô tả 1 dòng, giá, nút "Chọn"). **Chỉ 1 badge**: "Phổ biến" cho ghế giữa. Ghế cuối dùng nhãn nhỏ "Rẻ nhất".
- Khối "Thuê nguyên xe": giữ `.annam-limo-charter`, đổi sang nền teal đậm và nút cam.
- SVG đặt inline trong template-part mới `template-parts/limo-landing/part-seat-map.php`. Chỉ render khi config có `seat_map => true` (Hạ Long bật, Sapa tắt).

### 3.7. Lịch xe thành dòng lộ trình (B4)

Thay card giờ hiện tại bằng **timeline ngang**, trên mobile chuyển thành timeline dọc:

```
● Phố Cổ HN  ───── Cao tốc 5B ───── ● TP. Hạ Long / Tuần Châu
  07:00 · 08:00        ~2h–2h30          ~09:30 · 10:30
```

Tab chiều đi giữ nguyên. Nút giờ thành chip lớn, dùng `tabular-nums`. Hiển thị giờ đến dự kiến bằng cách cộng 2h30: thêm khóa `eta_minutes => 150` vào profile.

### 3.8. Điểm đón/trả

- Chia 2 cột Hà Nội / Hạ Long, mỗi điểm là 1 thẻ: icon loại điểm (văn phòng / TTTM / cảng / khách sạn), tên, địa chỉ, nút "Chỉ đường".
- Gắn tag **"Cảng du thuyền"** nổi bật cho Tuần Châu và Cảng Quốc tế.
- Có thể nhúng ảnh tĩnh bản đồ tuyến (SVG tự vẽ, không dùng iframe Google Maps vì nặng).

### 3.9. Các section còn lại

| Section | Thay đổi |
|---|---|
| Ảnh xe `#anh-xe` | Giữ nguyên, không xử lý (ảnh do chủ dự án thay). |
| Tiện ích | Lưới icon 4 cột (2 cột trên mobile), icon line màu teal, nền mint. |
| Chính sách | Dạng "ticket stub": 3 thẻ Đổi vé / Hủy vé / Hành lý. Hủy vé hiển thị thanh mốc thời gian ≥12h 100% → 3–12h 50% → <3h 0%, tô 3 màu. |
| Vì sao chọn | Gộp vào thanh tin cậy (3.5), hoặc rút còn 3 thẻ để trang ngắn lại. |
| Đặt vé 3 bước | Stepper ngang có số lớn, nối bằng đường kẻ. |
| Đánh giá (mới) | 6 đánh giá thật trên Google Maps, mỗi đánh giá gồm tên, ngày, sao, nội dung. Có nút "Xem tất cả đánh giá trên Google" trỏ tới https://maps.app.goo.gl/fRM5drrkoftTc2139. Mảng `reviews` rỗng thì ẩn section. Cách lấy dữ liệu xem bên dưới bảng này. |
| FAQ | Accordion `<details>`, icon +/−, mặc định mở câu đầu tiên. |
| CTA cuối | Nền teal đậm (giữ cơ chế ảnh nền hiện có), nút cam "Giữ chỗ ngay" và số hotline cỡ lớn. |
| Sticky mobile | 4 nút: Gọi · Zalo · WhatsApp · **Giữ chỗ** (nút cam, rộng gấp đôi). Ẩn khi widget form đang nằm trong viewport (dùng IntersectionObserver). |

**Lấy đánh giá Google.** Trang Google Maps được dựng bằng JavaScript nên không đọc tự động được. Chọn một trong hai cách:

1. **Nhập tay (khuyến nghị cho đợt này).**
   - Mở link bản đồ, chọn 6 đánh giá 5 sao gần đây, ưu tiên các đánh giá nhắc tới xe, tài xế, đón tận nơi hoặc tuyến Hạ Long.
   - Chép đúng nguyên văn vào mảng `reviews` trong `inc/limo-landing-config-halong.php`, mỗi phần tử có `author`, `rating`, `date` (Y-m), `text`.
   - Đồng thời điền điểm trung bình và tổng số đánh giá vào `trust_items`.
   - **Không sửa nội dung đánh giá.** Có thể cắt bớt, khi cắt thì thêm dấu "…".
2. **Tự động qua Google Places API (giai đoạn 2).**
   - Gọi Place Details với place ID của công ty (`0x3135ab005b3f19bf:0xce344276f19dadff`; dùng Find Place để đổi sang dạng `ChIJ…`), trường `rating,user_ratings_total,reviews`.
   - Lưu cache bằng transient 24 giờ.
   - API trả tối đa 5 đánh giá và cần API key.

Đánh giá phải là **đánh giá thật**. Không thêm schema `Review`/`AggregateRating` tự viết cho đánh giá lấy từ Google, vì Google không tính và có thể coi là spam.

### 3.10. Thứ tự section mới

Hero + widget → Thanh tin cậy → Bảng giá + sơ đồ ghế → Lịch xe/lộ trình → Điểm đón/trả → Ảnh xe → Tiện ích → Đánh giá → Chính sách → FAQ → CTA cuối.

Cập nhật `anchors` theo thứ tự này. Trên mobile, thanh anchor dạng sticky cuộn ngang.

---

## 4. File cần sửa

| File | Việc |
|---|---|
| `assets/css/limo-landing-halong.css` (mới) | Token màu (3.1) và style hero, widget, sơ đồ ghế, timeline, ticket stub, trust bar, reviews. Mọi selector nằm dưới `.annam-limo-landing--halong`. **Không sửa `limo-landing.css`.** |
| `template-parts/limo-landing/landing-page.php` | Hero mới; trust bar; reviews; thứ tự section; render có điều kiện theo cờ `sections`. |
| `template-parts/limo-landing/part-form.php` | Markup widget vé (giữ `name`/`id`). |
| `template-parts/limo-landing/part-seat-map.php` (mới) | SVG sơ đồ ghế. |
| `inc/limo-landing-config-halong.php` | Subtitle ngắn; `trust_items`, `reviews`, `seat_map`, loại điểm đón. |
| `inc/limo-landing-route-profiles.php` | `eta_minutes`. |
| `inc/limo-landing-page.php` | Body class `annam-limo-landing--{route}`; enqueue `limo-landing-halong.css` khi route là `halong`; header/footer rút gọn; dequeue asset thừa (C2). Tất cả chỉ áp dụng cho Hạ Long. |
| `assets/js/limo-landing.js` | Nút đảo chiều; chip ngày/giờ/ghế đồng bộ với select ẩn; stepper số khách; tạm tính; sơ đồ ghế; ẩn sticky khi form trong viewport. **Giữ nguyên các sự kiện tracking hiện có.** |

**Bảo vệ trang Sapa:**
- Các section mới (`seat_map`, `trust`, `reviews`, header/footer rút gọn) bật bằng cờ trong config. Sapa để tắt.
- Toàn bộ CSS mới, gồm cả token màu, nằm dưới `.annam-limo-landing--halong`.
- Markup form mới (widget vé) chỉ render khi `form.style === 'ticket'` (Hạ Long). Sapa vẫn dùng form cũ.
- JS mới chỉ chạy khi tìm thấy markup widget vé.

---

## 5. Kiểm thử

1. **Ảnh chụp trước/sau** ở 390×844, 768×1024 và 1440×900.
2. **Mobile 390×844:** H1, widget và nút "Giữ chỗ" đều thấy được mà không cần cuộn. Không cuộn ngang.
3. **Widget đặt vé:**
   - Đảo chiều thì giờ đổi theo (07/08 ⇄ 12/13).
   - Chip giờ quá hạn bị disable.
   - Tạm tính đúng, ví dụ 3 khách × ghế giữa = 1.020.000đ.
   - Gửi form, email nhận đủ thông tin như trước.
4. **Tracking:** GTM Preview vẫn bắt được `limo_booking_success` (có value) và các click Gọi, Zalo, WhatsApp, Maps.
5. **Độ tương phản:** axe DevTools hoặc Lighthouse Accessibility không có lỗi contrast.
6. **PageSpeed mobile:** LCP < 2,5s, CLS < 0,1. Số request CSS/JS giảm so với 12/13 hiện tại.
7. **Trang Sapa không thay đổi:** so sánh HTML và ảnh chụp trước/sau, phải giống hệt (cùng màu, cùng section, cùng form). Form Sapa vẫn gửi được.

---

## 6. Còn lại cần chủ dự án

- [ ] Chọn 6 đánh giá trên Google Maps cho dev nhập, hoặc để dev tự chọn theo tiêu chí ở 3.9.
- [ ] Số liệu tin cậy thật: số khách hoặc số chuyến đã chạy, số giấy phép lữ hành. Ô nào chưa có số thì ẩn.
