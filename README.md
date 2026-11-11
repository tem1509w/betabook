# BetaBook

BetaBook là dự án học tập xây dựng website nhà sách bằng PHP và MySQL/MariaDB,
được phát triển xuyên suốt môn Lập trình web. Project thực hành từng phần từ
giao diện public, truy vấn database đến đăng nhập và khu vực quản trị; đây là
sản phẩm môn học đang trong quá trình hoàn thiện, không phải website thương mại
đã sẵn sàng triển khai thực tế.

## Chạy trên máy local

### Yêu cầu

- XAMPP hoặc môi trường tương đương có Apache, PHP và MySQL/MariaDB.
- PHP cần bật extension `PDO MySQL` (`pdo_mysql`).

### Cài đặt

1. Đặt project trong thư mục web server, ví dụ `C:\xampp\htdocs\betabook`.
2. Khởi động Apache và MySQL/MariaDB.
3. Import file [`database/betabook_bt03_deploy.sql`](database/betabook_bt03_deploy.sql)
   bằng phpMyAdmin hoặc MySQL client. File tạo/chọn database `betabook`, tạo
   schema và dữ liệu mẫu; không xóa các bảng hiện có.
4. Cấu hình database nếu cần. Mặc định trong
   [`config/database.php`](config/database.php) là host `127.0.0.1`, port `3306`,
   database `betabook`, user `root`, mật khẩu rỗng. Có thể ghi đè bằng biến môi
   trường `BETABOOK_DB_HOST`, `BETABOOK_DB_PORT`, `BETABOOK_DB_NAME`,
   `BETABOOK_DB_USER`, `BETABOOK_DB_PASSWORD` và `BETABOOK_DB_CHARSET`.
5. Mở `http://localhost/betabook/` nếu project nằm tại
   `C:\xampp\htdocs\betabook`. Nếu đặt trong thư mục con khác, thay URL theo
   đường dẫn tương ứng. Có thể đặt `BETABOOK_BASE_URL` để cấu hình URL gốc.

### Tài khoản mẫu

Các tài khoản sau được tạo khi import file triển khai:

| Username | Mật khẩu | Vai trò |
|---|---|---|
| `demo` | `BetaBook2026!` | Khách hàng |
| `admin_demo` | `BetaBook123` | Quản trị viên |
| `staff_demo` | `BetaBook123` | Nhân viên |
| `customer_demo` | `BetaBook123` | Khách hàng |

Đây là thông tin đăng nhập dùng cho môi trường demo/local; không dùng các mật
khẩu mẫu khi triển khai thật.

## Các trang và chức năng

### Public

| Trang | URL mẫu |
|---|---|
| Trang chủ | `/` |
| Giới thiệu | `/?route=introduction` |
| Danh sách sách | `/?route=booklist` |
| Chi tiết sách | `/?route=product-detail&id=33` |
| Sự kiện/khuyến mãi | `/?route=discount-event` |
| Giỏ hàng | `/?route=cart` |
| Liên hệ | `/?route=contact` |
| Đăng nhập | `/?route=login` |
| Tài khoản | `/?route=account` |

Các đường dẫn trên tính từ URL gốc của project. Có thể dùng `index.php?route=...`
thay cho dạng rút gọn; `index.php?page=...` cũng được hỗ trợ. Trang chủ là
trang mặc định. Icon tài khoản trên header mở trang tài khoản khi đã đăng nhập,
hoặc trang đăng nhập khi chưa đăng nhập.
Các route không tồn tại và URL không trỏ đến file có thật sẽ hiển thị trang 404.
Để xử lý URL lạ bằng Apache, cần bật `mod_rewrite` và cho phép cấu hình `.htaccess`
(`AllowOverride`).

Danh sách và chi tiết sách đọc dữ liệu từ database. Giỏ hàng của khách lưu
trong session; khi đăng nhập, giỏ khách được gộp vào giỏ riêng của tài khoản.
Có thể đặt hàng bằng COD hoặc chuyển khoản chờ xác nhận; hệ thống lưu đơn và
chi tiết đơn vào database.

Trang tài khoản hỗ trợ cập nhật username, họ tên, email, số điện thoại; đổi mật
khẩu sau khi nhập mật khẩu hiện tại; chọn avatar trong
`assets/images/pfp-icon-set/`; xem lịch sử đơn hàng; và đăng xuất.

### Admin

| Trang | URL mẫu |
|---|---|
| Tổng quan | `/admin/` |
| Quản lý sách | `/admin/?page=books` |
| Thêm/sửa sách | `/admin/?page=book-form` |
| Quản lý danh mục | `/admin/?page=categories` |
| Thêm/sửa danh mục | `/admin/?page=category-form` |
| Khuyến mãi/sự kiện | `/admin/?page=promotions` |
| Quản lý tài khoản | `/admin/?page=users` |
| Quản lý đơn hàng | `/admin/?page=orders` |

Các màn hình quản trị cần người dùng đăng nhập và có role ID `1` hoặc `2`;
trên database mới, thứ tự role là `1` quản trị viên, `2` nhân viên và `3` khách
hàng. Quyền xem từng menu/trang và quyền thực hiện action được kiểm tra dựa
trên `admin_menu_permissions`. Admin và Staff được cấp quyền quản lý đơn hàng;
chỉ Admin được xóa tài khoản. Menu được lấy từ `admin_menu_items`, nhưng mỗi
route và action vẫn phải được đăng ký trong `admin/index.php`.

Quản lý tài khoản dành cho Admin: tìm kiếm, tạo, cập nhật và xóa tài khoản,
vai trò, trạng thái và avatar. Không thể xóa tài khoản đang đăng nhập hoặc
Admin hoạt động cuối cùng. Staff có thể xem, lọc và cập nhật trạng thái đơn,
cũng như xóa đơn; đơn đang chờ/xác nhận được xóa kèm hoàn tồn kho.

## Database và migrations

File `database/betabook_bt03_deploy.sql` dùng để tạo database mới. Các nhóm dữ
liệu chính gồm:

- `products`, `product_images`, `categories`, `publishers`: sách và danh mục.
- `users`, `roles`: tài khoản và vai trò; `users.role_id` tham chiếu `roles`.
- `admin_menu_items`, `admin_menu_permissions`: cấu hình menu và quyền menu
  admin.
- `orders`, `order_items`, `user_cart_items`: đơn hàng, chi tiết đơn và giỏ
  hàng lưu riêng theo tài khoản.
- `home_content`, `home_reviews`, `home_coupons`, `home_events`: nội dung
  trang chủ và dữ liệu hiển thị mẫu.
- `contact_messages`, `settings`: tin nhắn liên hệ và cấu hình.

`home_reviews` lưu lời nhận xét về BetaBook được hiển thị trên trang chủ.

Nếu đang dùng database cũ, sao lưu database trước rồi chỉ chạy các migration
phù hợp với schema hiện có. Đối với database cũ còn dùng `admin_roles` và
`users.role`, thứ tự chuyển đổi vai trò là:

1. `database/migrations/20261030_admin_role_ids.sql`
2. `database/migrations/20261110_users_role_id.sql`
3. `database/migrations/20261110_unify_roles.sql`

Các migration cập nhật schema người dùng:

- `database/migrations/20261110_remove_users_address.sql`: bỏ cột `address`
  nếu database cũ còn cột này.
- `database/migrations/20261110_users_avatar_image.sql`: thêm
  `users.avatar_image` nếu chưa có.
- `database/migrations/20261111_users_default_avatar.sql`: đặt avatar Pyrois
  làm mặc định cho tài khoản chưa có avatar và các tài khoản tạo mới.
- `database/migrations/admin_account_management.sql`: thêm menu và quyền menu
  Quản lý tài khoản cho role `admin` sau khi đã có bảng `roles` và menu admin.
- `database/migrations/20261111_remove_product_ratings.sql`: xóa dữ liệu và
  bảng rating/đánh giá sản phẩm khỏi database hiện có.
- `database/migrations/20261111_orders_checkout.sql`: tạo bảng đơn hàng, chi tiết
  đơn, giỏ hàng theo tài khoản và thêm quyền menu Quản lý đơn hàng cho role
  `admin` và `staff`. Có thể chạy lại trên database đã cập nhật.
- `database/migrations/20261111_order_retention.sql`: thêm mốc lưu trữ đơn 30
  ngày và quyền Quản lý đơn hàng cho Staff. Chỉ chạy một lần sau migration đơn
  hàng nếu database đã có bảng `orders`; file deploy mới đã có sẵn thay đổi này.

Các migration chuyển đổi cấu trúc như role, `address` và avatar chỉ chạy khi
database còn ở trạng thái cần chuyển đổi; không chạy lại nếu thay đổi đã có.
Migration `admin_account_management.sql` có thể chạy lặp lại. Database mới tạo
từ file deploy đã có schema `roles`, `users.role_id`, avatar mặc định và menu tài
khoản.

## Cấu trúc source chính

- `index.php`: router public và layout public.
- `pages/<page>/`: giao diện và xử lý dữ liệu của từng trang public.
- `action.php`: endpoint JSON cho thao tác public như thêm vào giỏ.
- `helpers/cart.php`, `helpers/orders.php`: nghiệp vụ giỏ hàng theo session,
  tạo đơn và cập nhật trạng thái đơn hàng.
- `scripts/cleanup_orders.php`: tác vụ CLI xóa đơn đủ điều kiện sau 30 ngày.
- `admin/index.php`: xác thực quyền, router và layout admin.
- `admin/pages/`, `admin/actions/`: màn hình và thao tác quản trị.
- `layouts/`: layout dùng chung public/admin.
- `helpers/`, `classes/`, `config/`: helper, kết nối database và cấu hình.
- `assets/`: CSS, hình ảnh và font.
- `database/`: file tạo database và migrations.

## Tình trạng và giới hạn hiện tại

Các dấu hiệu sau trong mã nguồn và database cho thấy project vẫn là bài tập
đang phát triển, chưa phải hệ thống hoàn chỉnh:

| Phần | Tình trạng hiện tại | Dấu hiệu trong project |
|---|---|---|
| Đăng ký tài khoản | Chưa xử lý tạo tài khoản. | Form đăng ký trong `pages/login/index.php` mới có các ô nhập và nút `type="button"`, chưa gửi dữ liệu tới backend. |
| Đăng nhập Google | Chưa tích hợp OAuth hoặc dịch vụ xác thực bên thứ ba. | Nút Google chỉ là nút giao diện, không có luồng xác thực tương ứng. |
| Giỏ hàng và đặt mua | Giỏ khách lưu trong session; giỏ đăng nhập lưu riêng theo tài khoản và được gộp khi đăng nhập. Badge đếm loại sách. Đặt hàng không thu phí vận chuyển; có COD hoặc chuyển khoản chờ xác nhận. | `helpers/cart.php`, `helpers/orders.php`, `pages/cart/` và các bảng `user_cart_items`, `orders`, `order_items`. |
| Quản lý đơn hàng | Admin và Staff có thể xem/tìm/lọc đơn, cập nhật trạng thái đơn và trạng thái thanh toán. Hủy đơn chưa giao sẽ hoàn tồn kho. | `admin/pages/orders/`; menu cấp cho role `admin` và `staff`. |
| Xóa dữ liệu | Admin có thể xóa tài khoản (không tự xóa hoặc xóa Admin hoạt động cuối cùng); Staff và Admin có thể xóa đơn. Đơn pending/confirmed bị xóa sẽ hoàn tồn kho. Đơn hoàn thành + đã thanh toán, đã hoàn tiền hoặc đã hủy tự xóa sau 30 ngày đủ điều kiện. | `admin/actions/delete-user.php`, `helpers/orders.php`, `scripts/cleanup_orders.php`. |
| Lịch sử đơn hàng | Tài khoản đăng nhập xem tối đa 50 đơn gần nhất, thông tin giao hàng, sản phẩm và trạng thái. Khách vẫn có thể đặt nhưng không có lịch sử tài khoản. | `pages/account/index.php` đọc dữ liệu theo `user_id`. |
| Thanh toán trực tuyến / mã giảm | Chưa kết nối cổng thanh toán; chuyển khoản cần admin xác nhận thủ công. Ô mã giảm được giữ nguyên giao diện nhưng chưa xử lý/áp dụng. | Trạng thái thanh toán được quản lý trong admin; discount hiện bằng 0. |
| Avatar trên header | Khi đăng nhập, icon tài khoản hiển thị avatar hiện được chọn trong hồ sơ. | `layouts/public/header.php`. |
| Liên hệ | Form trên trang liên hệ hiện hiển thị thông báo cảm ơn nhưng không lưu nội dung gửi. | `pages/contact/action.php` không ghi database; dù schema có `contact_messages` và `action.php` có endpoint JSON riêng, form hiện tại chưa nối vào endpoint đó. |
| Kiểm thử và vận hành | README chỉ hướng dẫn chạy local; project chưa được mô tả là đã triển khai production. | Cấu hình mặc định trong `config/database.php` dùng database local và tài khoản `root` không mật khẩu. |

Một số phần đã hoạt động như đọc danh sách/chi tiết sách, quản lý dữ liệu trong
admin, đăng nhập bằng username, cập nhật hồ sơ và đổi mật khẩu. Tuy vậy, các
chức năng còn thiếu ở trên khiến luồng mua hàng, chăm sóc khách hàng và vận hành
chưa khép kín. Dữ liệu demo và thông tin đăng nhập mẫu chỉ phục vụ học tập, thử
nghiệm trên máy local; cần bổ sung bảo mật, kiểm thử, cấu hình triển khai và
hoàn thiện các quy trình trước khi cân nhắc sử dụng thực tế.

### Lên lịch tự xóa đơn hàng trên Windows

Tác vụ chỉ xóa đơn đủ 30 ngày kể từ `retention_started_at` và thỏa một trong
các điều kiện: hoàn thành + đã thanh toán, đã hoàn tiền, hoặc đã hủy. Trên
Windows/XAMPP, tạo tác vụ trong Task Scheduler chạy mỗi ngày:

- Program/script: `C:\xampp\htdocs\betabook\betabook\scripts\run_order_cleanup.bat`
- Start in: `C:\xampp\htdocs\betabook\betabook\scripts`

Nếu XAMPP được cài ở đường dẫn khác, sửa `PHP_BIN` trong file `.bat`. Tác vụ
cần chạy bằng tài khoản Windows có quyền đọc project và truy cập MariaDB. Đơn
bị xóa sẽ mất khỏi lịch sử tài khoản/admin; các dòng `order_items` tự xóa theo
khóa ngoại.
