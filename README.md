<<<<<<< HEAD
# BetaBook PHP
*Note: 
Cắt ghép layout cho 3 trang để phục vụ bài tập 3, không phải kết quả cuối cùng / 100% các trang đã đảm nhiệm.
Database mang tính chất phát triển thêm, chưa thể phân quyền trong phần code PHP.
Trang Admin có thể chỉnh sửa được thông tin vầ tương tác với Database, hiển thị tương ứng trên trang public, nhưng dữ liệu trên trang public không thể submit form theo yêu cầu.
Bởi vì chưa thể tương tác trên trang public, nên chưa thể add sản phẩm vào giỏ hàng để tiến hành thanh toán(lưu đơn hàng để quản lý đơn hàng). Do đó chưa cần tạo trang quản lý đơn hàng ở admin.
## Chạy trên máy local

Ứng dụng đang được đặt tại `C:\xampp\htdocs\betabook`. Với cấu hình mặc định,
mở trang tại:

- Trang chủ: <http://localhost/betabook/>
- Admin: <http://localhost/betabook/admin/>

## Các trang public

Các trang public được ghép chung layout trong `layouts/public/`:

| Trang | Đường dẫn |
|---|---|
| Trang chủ | <http://localhost/betabook/> |
| Chi tiết sách | <http://localhost/betabook/index.php?page=product-detail&id=33> |
| Giỏ hàng | <http://localhost/betabook/index.php?page=cart> |

`id=33` trong đường dẫn chi tiết là ví dụ; thay bằng ID sách cần xem.
Trang chủ là route mặc định (`page=home`).

## Các trang admin

Các màn hình admin dùng layout chung trong `layouts/admin/` (header, sidebar
và footer):

| Trang | Đường dẫn |
|---|---|
| Tổng quan | <http://localhost/betabook/admin/> |
| Quản lý sách | <http://localhost/betabook/admin/?page=books> |
| Thêm sách | <http://localhost/betabook/admin/?page=book-form> |
| Sửa sách | <http://localhost/betabook/admin/?page=book-form&id=33> |
| Quản lý danh mục | <http://localhost/betabook/admin/?page=categories> |
| Thêm danh mục | <http://localhost/betabook/admin/?page=category-form> |
| Sửa danh mục | <http://localhost/betabook/admin/?page=category-form&id=1> |

Các ID trong đường dẫn thêm/sửa chỉ là ví dụ.

## Cách ghép layout

- Public router: `index.php`; danh sách route được khai báo trong `$routes`.
- Public trang: `pages/<tên-trang>/index.php`; dữ liệu và tiêu đề thường được
  chuẩn bị trong `pages/<tên-trang>/action.php`.
- Public layout: `layouts/public/header.php` và `layouts/public/footer.php`.
- Admin router: `admin/index.php`; danh sách màn hình được khai báo trong
  `$routes`.
- Admin trang: `admin/pages/<tên-trang>/`; layout chung gồm
  `layouts/admin/header.php`, `layouts/admin/sidebar.php` và
  `layouts/admin/footer.php`.

Các thao tác lưu/xóa trong admin là endpoint nhận `POST`, không phải trang
để mở trực tiếp:

- `admin/?action=save-book`
- `admin/?action=delete-book`
- `admin/?action=save-category`
- `admin/?action=delete-category`

## Vai trò và quyền menu admin

Vai trò có ID số trong `admin_roles`; quyền hiển thị menu và truy cập trang
được tra qua `admin_menu_permissions.role_id`. Danh sách vai trò hiện tại:

| role_id | Vai trò | Quyền menu mặc định |
|---:|---|---|
| 1 | Quản lý | Tổng quan, Sách, Danh mục, Public |
| 2 | Nhân viên | Tổng quan, Sách, Public |
| 3 | User | Không có menu admin |

Để xem ID hiện có, chạy
`SELECT role_id, role_key, role_name FROM admin_roles ORDER BY role_id;`.
Thay đổi `can_view` trong `admin_menu_permissions` sẽ cập nhật cả menu sidebar
và kiểm tra quyền truy cập route. Các trang mới vẫn cần được đăng ký trong
`admin/index.php`.

Đã áp dụng migration `database/migrations/20261030_admin_role_ids.sql` cho
database hiện tại. Đây là migration một lần dành cho database cũ; database mới
cần dùng `database/betabook_bt03_deploy.sql`.

Hiện dự án chưa có luồng đăng nhập; admin router mặc định dùng vai trò Quản lý.
Khi bổ sung đăng nhập, cần gán `$_SESSION['admin_role_id']` theo vai trò của
tài khoản đã xác thực.
=======
# betabook
newbie project for Web Developing subject
>>>>>>> 335abc49947ed6ed6616664aa99ba314fe4fa5dc
