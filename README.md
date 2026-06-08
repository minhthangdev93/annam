# An Nam Discovery — GeneratePress Child Theme

Child theme WordPress cho [annamdiscovery.com](https://annamdiscovery.com), xây trên [GeneratePress](https://generatepress.com).

Repository: [github.com/minhthangdev93/annam](https://github.com/minhthangdev93/annam)

## Yêu cầu

| Thành phần | Ghi chú |
|------------|---------|
| WordPress | 6.x+ |
| PHP | 8.0+ khuyến nghị |
| Theme cha | **GeneratePress** (cài riêng, không nằm trong repo này) |
| WooCommerce | Tour / sản phẩm du lịch |
| Rank Math SEO | Tùy chọn (có hook tùy chỉnh trong theme) |

## Cài đặt

1. Clone repo vào thư mục theme:

   ```bash
   cd wp-content/themes
   git clone https://github.com/minhthangdev93/annam.git generatepress_child
   ```

   Hoặc đổi tên thư mục sau clone thành `generatepress_child` nếu cần khớp `Template: generatepress` trong `style.css`.

2. Cài và kích hoạt theme cha **GeneratePress** trong WordPress.

3. Vào **Giao diện → Giao diện** → kích hoạt child theme **An Nam Discovery**.

4. Cấu hình plugin phụ thuộc (WooCommerce, Rank Math, v.v.) trên môi trường staging/production.

## Cấu trúc chính

```
generatepress_child/
├── assets/          # CSS, JS, font, icon, schema JSON
├── inc/             # Logic PHP (hook, CPT, landing, WooCommerce)
├── template-parts/  # Partial template (home, blog, tour, landing…)
├── woocommerce/     # Override WooCommerce template
├── page-template-*.php
├── functions.php
└── style.css        # Khai báo theme + style cơ bản
```

### Page templates

| Template | File |
|----------|------|
| Trang chủ | `page-template-trang-chu.php` |
| Giới thiệu | `page-template-gioi-thieu.php` |
| Liên hệ | `page-template-lien-he.php` |
| Cẩm nang blog | `page-template-cam-nang-blog.php` |
| Landing Cabin VIP | `page-template-cabin-vip-landing.php` |
| Hub thuê xe | `page-template-thue-xe-hub.php` |
| Landing thuê xe (theo loại xe) | `page-template-thue-xe-landing.php` |

### Landing thuê xe

- Cấu hình giá: `inc/car-rental-pricing-data.php`
- Ảnh từng page (hero, CTA, gallery, QR, hành trình…): meta box **Ảnh Landing thuê xe** khi sửa Page
- Assets: `assets/css/car-rental-landing.css`, `assets/js/car-rental-landing.js`

### Admin tùy chỉnh

- **An Nam Settings** (`inc/annam-settings.php`) — hero, ecosystem, v.v.
- **Landing Cabin VIP** — ảnh landing cabin (`inc/cabin-landing-images-admin.php`)

## Deploy

Chi tiết cPanel (Git, ZIP, SSH): **[DEPLOY-CPANEL.md](DEPLOY-CPANEL.md)**

Checklist trước commit: **[PRE-COMMIT-CHECKLIST.md](PRE-COMMIT-CHECKLIST.md)**

1. Đích trên server: `wp-content/themes/generatepress_child/`
2. **Không** commit `uploads/`, plugin bên thứ ba, hay `wp-config.php`
3. Media (logo, QR, ảnh landing): sync `uploads/` hoặc gán lại trong meta box Page
4. Gói ZIP upload: `.\scripts\package-theme.ps1` → file trong `dist/`
5. cPanel Git: sửa `YOUR_CPANEL_USERNAME` trong `.cpanel.yml`, rồi **Deploy HEAD Commit**
6. Sau deploy: flush permalink, purge cache, kiểm tra trang chủ + 1 landing thuê xe

### Bảo mật wp-config

Xem gợi ý snippet: `inc/security-wp-config-snippet.txt` (chép thủ công, không tự động ghi `wp-config.php`).

## Đưa code lên GitHub (lần đầu)

Repo đích: [minhthangdev93/annam](https://github.com/minhthangdev93/annam)

### Cách 1 — Theme là root của repo (khuyến nghị)

```powershell
# Clone repo trống
git clone https://github.com/minhthangdev93/annam.git
cd annam

# Copy toàn bộ file từ theme local (trừ .git nếu có)
# Ví dụ PowerShell:
Copy-Item -Path "C:\path\to\wp-content\themes\generatepress_child\*" -Destination . -Recurse -Force

git add .
git status
git commit -m "Initial commit: An Nam Discovery child theme"
git branch -M main
git push -u origin main
```

### Cách 2 — Init git ngay trong thư mục theme

```powershell
cd "C:\path\to\wp-content\themes\generatepress_child"
git init
git add .
git commit -m "Initial commit: An Nam Discovery child theme"
git branch -M main
git remote add origin https://github.com/minhthangdev93/annam.git
git push -u origin main
```

> **Lưu ý:** Nếu `wp-content` đã là một git repo khác, nên dùng **Cách 1** hoặc tách theme sang repo `annam` riêng để tránh nested repository.

## Phát triển

- Text domain: `generatepress_child`
- Design tokens: `assets/css/design-tokens.css`
- Classic Editor (Gutenberg tắt cho post/page trong `functions.php`)

## Giấy phép

Mã nguồn thuộc An Nam Discovery. Phân phối lại theo thỏa thuận nội bộ / giấy phép riêng của dự án.
