# Checklist trước commit & deploy cPanel

Dùng danh sách này trước `git commit` và trước khi đưa lên hosting.

## Repo sạch

- [ ] Không có `.env`, `wp-config.php`, credential FTP/SSH trong code
- [ ] Không có `*.bak`, `debug.log`, file ZIP trong staging
- [ ] Không commit `assets/font/Noto_Sans/` (font không dùng, ~48MB)
- [ ] Không commit `wp-content/uploads/` — media sync riêng trên server

## Theme WordPress

- [ ] `style.css`: Theme Name, Version, `Template: generatepress` đúng
- [ ] `functions.php` load đủ file `inc/` mới (landing thuê xe, meta box ảnh…)
- [ ] Test local: trang chủ, 1 tour, hub thuê xe, 1 landing xe, section #uy-tin

## Git

- [ ] `git status` — chỉ file theme, không lẫn plugin/uploads
- [ ] Remote: `https://github.com/minhthangdev93/annam.git`
- [ ] Branch `main` (khuyến nghị) thay vì `master`

```powershell
cd "...\wp-content\themes\generatepress_child"
git remote -v
git status
```

## Commit (khi sẵn sàng)

```powershell
git add .
git commit -m "Initial release: An Nam Discovery child theme v1.0.0"
git branch -M main
git remote add origin https://github.com/minhthangdev93/annam.git
git push -u origin main
```

## Sau push — cPanel

- [ ] Sửa `YOUR_CPANEL_USERNAME` trong `.cpanel.yml`
- [ ] Deploy theo [DEPLOY-CPANEL.md](DEPLOY-CPANEL.md)
- [ ] Kích hoạt theme, flush permalink, purge cache
- [ ] Upload/sync `uploads/` (logo, QR.jpg, ảnh landing nếu cần)
- [ ] Gán lại ảnh meta box trên Page nếu migrate server mới

## Gói ZIP thủ công (tùy chọn)

```powershell
.\scripts\package-theme.ps1
```

File trong `dist/` — upload qua File Manager.
