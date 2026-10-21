# DALETIC – hướng dẫn cho Claude

Web bán áo đấu DALETIC (https://daletic.vn): WordPress + WooCommerce, chạy bằng Docker. Chủ shop nói tiếng Việt, trả lời bằng tiếng Việt.

## Cấu trúc

- Theme: `wp-content/themes/lukasports` (PHP cổ điển, tiền tố hàm `lukasports_`, class CSS `sk-*`, biến CSS `--sk-*`).
  - `assets/css/core.css`: token màu/font, nút, dải đầu trang, thẻ sản phẩm. `header.css`, `footer.css`, `home.css` (trang chủ), `woocommerce.css` (danh mục + chi tiết sản phẩm), `pages.css` (giới thiệu, liên hệ, bộ sưu tập, chính sách), `blog.css`, `dark.css` (phông tối, nạp sau cùng).
  - `template-parts/`: `page-band.php` (dải tối đầu mỗi trang), `product/product-card.php`, `product/product-cta.php` (khối Tư vấn ngay + Messenger/Zalo/Gọi), `product/size-guide.php` (ảnh `assets/images/bang-size.webp`), `sections/*` (các khối trang chủ).
  - `inc/`: `helpers.php` (thông tin liên hệ mặc định, màu chấm, ảnh hero), `hooks.php`, `brand-colors.php` (phông sáng/tối, mặc định tối), `enqueue.php`, `seo.php`.
- Plugin `wp-content/plugins/lukasports-core`: trang DALETIC → Settings (hotline, Messenger, Zalo, Facebook, địa chỉ), form tư vấn, bảng leads.
- `bin/`: script WP-CLI, gắn vào container wpcli tại `lukasports-bin/`.
  - `add-daletic-designs.php`: tạo các mẫu DALETIC.VN tự thiết kế (bỏ qua nếu slug đã có), bộ sưu tập `daletic-vn-2026`, giá 360.000đ → 189.000đ, size S–4XL. Thêm mẫu mới = thêm 1 dòng vào mảng `$designs` + ảnh `bin/product-photos/{slug}.jpg` (1200px, JPEG).
  - `apply-product-photos.php`: ảnh `bin/product-photos/{slug}.jpg` → ảnh chính sản phẩm.
  - `apply-category-images.php`: `bin/category-images/thumb-{slug}.jpg` / `banner-{slug}.jpg`.
  - `fill-sizes.php`: mọi sản phẩm áo (trừ `phu-kien`) có đủ S, M, L, XL, XXL, 3XL, 4XL.
  - `set-daletic-prices.php`: đặt giá cho bộ sưu tập DALETIC.VN 2026.
  - `backup.sh`: sao lưu DB + uploads (dùng trên server trước mọi thay đổi dữ liệu).
  - `seed-products.php`: chỉ dùng cho site trống, KHÔNG chạy lại trên site có dữ liệu (tạo trùng sản phẩm).

## Cách làm việc: sửa thẳng lên server, không chạy local

Chủ shop không dùng bản local. Mỗi thay đổi:

1. Sửa code trong repo trên máy (không cần Docker).
2. Commit + push (xem "Quy trình deploy").
3. Trên server: `bash bin/backup.sh && git pull --ff-only`, chạy script WP-CLI nếu cần đổi dữ liệu.
4. Kiểm tra trên https://daletic.vn (curl/ảnh chụp), sửa tiếp nếu lỗi.

Lint PHP trên server: `docker compose exec -T wordpress php -l /var/www/html/wp-content/themes/lukasports/<file>` (máy dev có thể không có `php`). Ảnh mới (sản phẩm, banner) xử lý trên máy rồi commit vào `bin/` hoặc `assets/images/`.

## Server (daletic.vn)

- Oracle Cloud ARM, Ubuntu, IP `161.118.192.202`, user `ubuntu`, code ở `~/LuKaSports`, Caddy lo HTTPS (`/etc/caddy/Caddyfile`).
- Server chạy nhánh `feature/pages-and-codespaces`. Nhánh làm việc: `feature/design-sync`.
- Mật khẩu admin WP nằm trong `~/LuKaSports/.env` trên server (`WP_ADMIN_PASSWORD`). Không in mật khẩu ra.
- CÙNG server có project khác: `pickclub.daletic.vn` (`~/pkl`, các container pkl, khối Caddy riêng, `~/pkl-backup.sh`). KHÔNG đụng vào.
- Không chạy `docker system prune`, `docker compose down -v`, không restart Docker.

Quy trình deploy:

```bash
# trên máy dev
git checkout feature/design-sync && git add -A && git commit -m "..."
git push origin feature/design-sync
git checkout feature/pages-and-codespaces && git merge --ff-only feature/design-sync && git push origin feature/pages-and-codespaces
git checkout feature/design-sync

# trên server
ssh ubuntu@161.118.192.202
cd ~/LuKaSports && bash bin/backup.sh && git pull --ff-only
docker compose run --rm -T wpcli eval-file lukasports-bin/<script>.php   # nếu cần đổi dữ liệu
```

Commit/push/deploy chỉ khi chủ shop yêu cầu. Luôn `bin/backup.sh` trước khi đổi dữ liệu trên server.

## Quyết định đã chốt (giữ nguyên trừ khi chủ shop đổi)

- Shop chỉ tư vấn: không giỏ hàng, không "Thêm vào giỏ/Mua ngay", không icon giỏ. Mọi CTA mở form tư vấn (`data-lukasports-cta="consult"`) hoặc Messenger/Zalo/Gọi.
- Khối tư vấn ở trang sản phẩm giữ kiểu cũ (nền tối vệt đỏ, nút đỏ lớn + 3 nút bằng nhau). Chọn size/màu được gắn vào tin nhắn tư vấn (`assets/js/product-page.js`).
- Giao diện theo bản Claude Design "Daletic Sports – Website": font tiêu đề Barlow Condensed nghiêng (tự host), nền `#0b0e14`, đỏ `#d9141c`. Có nút chuyển sáng/tối; khách mới mặc định phông tối.
- Bộ lọc danh mục: Danh mục, Khoảng giá, Bộ sưu tập (không có "Loại áo", "Thương hiệu").
- 3 trang chính sách là 3 URL riêng, có thanh tab chuyển qua lại.
- Không bịa số liệu/cam kết. Thông tin liên hệ lấy từ DALETIC → Settings: hotline 0819.66.6868, fanpage facebook.com/profile.php?id=61569257930689, địa chỉ showroom: 66 Hồng Tiến, Bồ Đề, Long Biên, Hà Nội.
- Ảnh đăng lên web không được có logo Nike/hãng khác.
- Trang chủ: mục "Mẫu mới nhất" (bộ sưu tập DALETIC.VN 2026) xếp **mới nhất trước**, 18 mẫu = 3 hàng, mẫu cũ ở dưới; mục "Các mẫu đã ra mắt" ngay sau đó lấy tiếp các mẫu chưa hiện, không lặp. Mọi trang danh sách mặc định "Mới nhất". Mẫu mới đăng luôn đứng đầu.

## Thẻ sản phẩm (badge)

- Thẻ chọn tay: wp-admin → sản phẩm → tab General → "Thẻ sản phẩm" (meta `_lukasports_badge`). Luôn thắng thẻ tự động. Gán hàng loạt: `bin/set-product-badges.php`.
- Thẻ tự động (`lukasports_auto_badges()` trong `inc/product-badges.php`, cache 1 giờ, tự làm mới khi sửa sản phẩm / có yêu cầu tư vấn mới), ưu tiên từ cao xuống thấp: Mới (3 sản phẩm mới nhất) → Quan tâm nhiều (3 sản phẩm nhiều yêu cầu tư vấn nhất 30 ngày, tối thiểu 2) → BST mới (bộ sưu tập tạo gần nhất) → Trọn bộ (tên bắt đầu bằng "Bộ ").
- "Bán chạy" chưa có số liệu bán hàng, chỉ dùng khi chủ shop tự quyết. Thêm loại thẻ mới: 1 dòng trong `lukasports_badge_options()` + 1 rule `.sk-badge--*` trong `core.css`.
- Giá theo chất vải và giảm theo số lượng nằm ở `inc/helpers.php` (`lukasports_fabric_tiers()`, `lukasports_team_discounts()`); "Từ 135.000đ" tự tính từ đó.

## Việc còn mở

- Danh mục "Áo Pickleball Thiết Kế Theo Yêu Cầu" (`ao-pickleball-thiet-ke`) chưa có sản phẩm, chờ chủ shop gửi ảnh.
- Tên 5 sản phẩm pickleball cũ còn chữ "Pickerball".
- Con số "10K+ khách hàng" ở trang chủ chưa xác nhận là số thật.
- Bài blog đang ở "Uncategorized" nên chưa có nút lọc chủ đề.
