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

Danh sách và chi tiết sách đọc dữ liệu từ database. Giỏ hàng thêm sách từ trang
chi tiết, lưu trong session hiện tại và cho phép xóa sản phẩm. Hiện chưa có
checkout, thanh toán hoặc lưu đơn hàng vào database.

Trang tài khoản hỗ trợ cập nhật username, họ tên, email, số điện thoại; đổi mật
khẩu sau khi nhập mật khẩu hiện tại; chọn avatar trong
`assets/images/pfp-icon-set/`; và đăng xuất. Lịch sử đơn hàng hiển thị trên
trang hiện là nội dung mẫu, chưa lấy từ đơn hàng lưu trong database.

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

Các màn hình quản trị cần người dùng đăng nhập và có role ID `1` hoặc `2`;
trên database mới, thứ tự role là `1` quản trị viên, `2` nhân viên và `3` khách
hàng. Mã hiện tại dựa vào các ID này; nếu database cũ có ID khác, cần điều
chỉnh ID dữ liệu hoặc logic phân quyền cho khớp. Quyền xem từng menu/trang và
quyền thực hiện action được kiểm tra dựa trên `admin_menu_permissions`. Menu
được lấy từ `admin_menu_items`, nhưng mỗi route và action vẫn phải được đăng ký
trong `admin/index.php`.

Quản lý tài khoản hiện dành cho quản trị viên: tìm kiếm, tạo và cập nhật tài
khoản, vai trò, trạng thái và avatar; không hỗ trợ xóa tài khoản. Nhân viên
không có quyền menu này theo dữ liệu triển khai mặc định.

## Database và migrations

File `database/betabook_bt03_deploy.sql` dùng để tạo database mới. Các nhóm dữ
liệu chính gồm:

- `products`, `product_images`, `categories`, `publishers`: sách và danh mục.
- `users`, `roles`: tài khoản và vai trò; `users.role_id` tham chiếu `roles`.
- `admin_menu_items`, `admin_menu_permissions`: cấu hình menu và quyền menu
  admin.
- `home_content`, `home_product_ratings`, `product_rating_breakdown`,
  `product_reviews`, `home_reviews`, `home_coupons`, `home_events`: nội dung
  trang chủ và dữ liệu hiển thị mẫu.
- `contact_messages`, `settings`: tin nhắn liên hệ và cấu hình.

Schema hiện tại chưa có bảng đơn hàng. Đánh giá mẫu sản phẩm được lưu theo sản
phẩm/người đánh giá, chưa liên kết với tài khoản đăng nhập.

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
- `database/migrations/admin_account_management.sql`: thêm menu và quyền menu
  Quản lý tài khoản cho role `admin` sau khi đã có bảng `roles` và menu admin.

Các migration chuyển đổi cấu trúc như role, `address` và avatar chỉ chạy khi
database còn ở trạng thái cần chuyển đổi; không chạy lại nếu thay đổi đã có.
Migration `admin_account_management.sql` có thể chạy lặp lại. Database mới tạo
từ file deploy đã có schema `roles`, `users.role_id`, `avatar_image` và menu tài
khoản.

## Cấu trúc source chính

- `index.php`: router public và layout public.
- `pages/<page>/`: giao diện và xử lý dữ liệu của từng trang public.
- `action.php`: endpoint JSON cho thao tác public như thêm vào giỏ.
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
| Giỏ hàng và đặt mua | Có thể thêm/xóa sách, nhưng giỏ chỉ lưu trong PHP session, không lưu vào database. Chưa có checkout, thanh toán hoặc tạo đơn hàng. | `action.php` lưu giỏ vào `$_SESSION['cart']`; schema trong `database/betabook_bt03_deploy.sql` chưa có bảng đơn hàng. |
| Lịch sử đơn hàng | Đang hiển thị dữ liệu minh họa, không lấy từ giao dịch thực. | Các khối đơn hàng được viết sẵn trong `pages/account/index.php`. |
| Liên hệ | Form trên trang liên hệ hiện hiển thị thông báo cảm ơn nhưng không lưu nội dung gửi. | `pages/contact/action.php` không ghi database; dù schema có `contact_messages` và `action.php` có endpoint JSON riêng, form hiện tại chưa nối vào endpoint đó. |
| Đánh giá sản phẩm | Có dữ liệu đánh giá để hiển thị, nhưng chưa gắn với tài khoản người dùng đã đăng nhập. | Bảng `product_reviews` lưu tên người đánh giá, không có `user_id`. |
| Kiểm thử và vận hành | README chỉ hướng dẫn chạy local; project chưa được mô tả là đã triển khai production. | Cấu hình mặc định trong `config/database.php` dùng database local và tài khoản `root` không mật khẩu. |

Một số phần đã hoạt động như đọc danh sách/chi tiết sách, quản lý dữ liệu trong
admin, đăng nhập bằng username, cập nhật hồ sơ và đổi mật khẩu. Tuy vậy, các
chức năng còn thiếu ở trên khiến luồng mua hàng, chăm sóc khách hàng và vận hành
chưa khép kín. Dữ liệu demo và thông tin đăng nhập mẫu chỉ phục vụ học tập, thử
nghiệm trên máy local; cần bổ sung bảo mật, kiểm thử, cấu hình triển khai và
hoàn thiện các quy trình trước khi cân nhắc sử dụng thực tế.
