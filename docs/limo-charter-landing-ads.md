# Landing Ads: Thuê xe Limousine HN–Sapa

## URL

- Final URL: `/thue-xe-limousine-ha-noi-sapa/`
- Template: `Landing Thuê Xe Limousine HN–Sapa`

## Giá (theo chiều)

| Chiều | Giá | Xe |
|-------|-----|----|
| Hà Nội → Sapa | 3.800.000đ/xe/chiều | 9 & 11 chỗ |
| Sapa → Hà Nội | 4.200.000đ/xe/chiều | 9 & 11 chỗ |

## GTM — form lead only

Call / Zalo đã track site-wide. Chỉ thêm conversion form:

| Event (`dataLayer`) | Khi nào |
|---------------------|---------|
| `limo_charter_lead_success` | Form “Nhận báo giá” gửi OK |

Optional nội bộ: `limo_charter_select_hn_sapa` / `limo_charter_select_sapa_hn` khi bấm card giá theo chiều.

GTM: Custom Event trigger → Google Ads conversion (Primary lead form thuê limo).

## Anchors sitelinks

- `#gia-thue`
- `#anh-xe`
- `#nhan-bao-gia`
- `#faq`
- `#goi-y-khac`

## Admin

An Nam Settings → **Landing Thuê Limo HN–Sapa**: ảnh slots, email lead, địa chỉ Maps override.

## Ảnh tạm

Lần load đầu, theme tự gán ảnh từ Media Library (ưu tiên ≥800px rộng) hoặc reuse slot landing vé limo. Thay ảnh xe 9/11 thật trước khi scale Ads.
