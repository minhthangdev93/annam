# Triển khai lên cPanel — An Nam Discovery Child Theme

Hướng dẫn đưa theme `generatepress_child` lên hosting cPanel (production/staging).

## Trước khi upload

| Việc | Ghi chú |
|------|---------|
| Theme cha | Cài **GeneratePress** trên server (không nằm trong repo này) |
| Plugin | WooCommerce, Rank Math, cache (LiteSpeed / WP Rocket…) theo môi trường live |
| Media | Ảnh trong `wp-content/uploads/` **không** đi kèm theme — upload/sync riêng |
| QR thuê xe | Đảm bảo có `uploads/2026/06/QR.jpg` trên server (hoặc gán ảnh QR trong meta box Page) |
| Tên thư mục | Bắt buộc: `wp-content/themes/generatepress_child` (khớp `Template: generatepress`) |

## Cách 1 — Git Version Control (cPanel, khuyến nghị)

1. **GitHub:** Push repo [minhthangdev93/annam](https://github.com/minhthangdev93/annam) lên `main`.
2. **cPanel → Git Version Control → Create**
   - Clone URL: `https://github.com/minhthangdev93/annam.git`
   - Repository Path: ví dụ `repositories/annam` (không đặt trực tiếp trong `public_html` nếu dùng deployment).
3. **Pull or Deploy → Manage → Deploy HEAD Commit**
   - **Deploy path:**  
     `public_html/wp-content/themes/generatepress_child`  
     (hoặc `public_html/ten-mien/wp-content/themes/generatepress_child` nếu WordPress trong subfolder).
4. Sửa file **`.cpanel.yml`** ở root repo: thay `YOUR_CPANEL_USERNAME` bằng user cPanel thật, rồi commit + deploy lại.
5. Sau deploy: kích hoạt theme trong **Giao diện → Giao diện**.

## Cách 2 — Upload ZIP (File Manager / FTP)

Trên máy local (PowerShell):

```powershell
cd "C:\path\to\wp-content\themes\generatepress_child"
.\scripts\package-theme.ps1
```

File tạo ra: `dist/annam-theme-generatepress_child.zip` (không chứa `.git`, font thừa, file tạm).

1. cPanel → **File Manager** → `public_html/wp-content/themes/`
2. Upload ZIP → **Extract**
3. Đảm bảo cấu trúc: `themes/generatepress_child/style.css` (không lồng thêm một cấp `generatepress_child/generatepress_child/`).

## Cách 3 — Git pull trực tiếp trên server (SSH)

```bash
cd ~/public_html/wp-content/themes/generatepress_child
git pull origin main
```

Chỉ dùng khi thư mục theme đã là clone git và có quyền SSH.

## Sau khi deploy

1. **Giao diện → Giao diện** — child theme **An Nam Discovery** đang active.
2. **Cài đặt → Permalink** — Save lại (flush rewrite).
3. Xóa cache: plugin cache + LiteSpeed Cache (nếu có).
4. Kiểm tra nhanh:
   - Trang chủ
   - 1 trang tour WooCommerce
   - Hub thuê xe + 1 landing xe (form, bảng giá, section #uy-tin, QR)
5. **Ảnh Landing thuê xe:** meta box trên từng Page — gán lại nếu server mới chưa có attachment ID cũ.

## Không đưa lên server / không commit

- `.git/`, `.env`, `node_modules/`, `*.bak`, `*.zip`
- `wp-config.php`, database, `uploads/`
- Plugin bên thứ ba (cài riêng trên WP)
- `assets/font/Noto_Sans/` — font TTF không dùng (~48MB), đã loại khỏi repo

## Cập nhật lần sau

| Phương thức | Thao tác |
|------------|----------|
| cPanel Git | **Pull or Deploy** → Deploy HEAD Commit |
| ZIP | Chạy lại `package-theme.ps1`, upload đè file đã đổi (hoặc extract full sau backup) |
| SSH | `git pull` trong thư mục theme |

## Xử lý lỗi thường gặp

**Site trắng / lỗi PHP**  
Bật `WP_DEBUG_LOG` tạm thời, xem `wp-content/debug.log`. Kiểm tra PHP ≥ 8.0.

**Theme cha không tìm thấy**  
Cài và kích hoạt GeneratePress; tên thư mục child phải là `generatepress_child`.

**Ảnh / QR không hiện**  
Upload media lên `uploads/` hoặc chọn lại ảnh trong meta box Page trên admin live.

**Font / CSS lệch**  
Hard refresh (Ctrl+F5); purge cache CDN/hosting.

## Bảo mật

Snippet gợi ý cho `wp-config.php`: `inc/security-wp-config-snippet.txt` (chép thủ công).
