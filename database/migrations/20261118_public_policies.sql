-- Add public policy content and its admin menu entry. Safe to run repeatedly.
CREATE TABLE IF NOT EXISTS policies (
    policy_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    page_group VARCHAR(50) DEFAULT 'Chính sách',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO policies
    (policy_id, title, slug, page_group, sort_order, is_active, content)
VALUES
    (1, 'Chính sách thanh toán', 'chinh-sach-thanh-toan', 'Chính sách', 1, 1,
     'Ba cách thanh toán, chọn cách nào tiện nhất với bạn.

BetaBook hỗ trợ các phương thức thanh toán sau khi bạn đặt hàng tại trang giỏ hàng:
- Thanh toán khi nhận hàng (COD): Trả tiền mặt cho nhân viên giao hàng khi nhận sách.
- Chuyển khoản: Chuyển đúng số tiền vào tài khoản của BetaBook, ghi nội dung: số điện thoại + họ tên.
- Ví điện tử: Quét mã QR hiển thị sau khi đặt hàng để thanh toán.

Lưu ý khi thanh toán:
- Giá trên website đã bao gồm thuế VAT, chưa gồm phí vận chuyển.
- Đơn chuyển khoản hoặc ví điện tử được xử lý sau khi BetaBook xác nhận đã nhận tiền.
- Mã giảm giá được nhập ở bước giỏ hàng và không áp dụng đồng thời nhiều mã trên một đơn.

Chuyển nhầm hoặc chuyển dư? Liên hệ hotline 0123 456 789 kèm ảnh giao dịch, BetaBook sẽ hoàn lại trong 3 ngày làm việc.'),
    (2, 'Chính sách vận chuyển', 'chinh-sach-van-chuyen', 'Chính sách', 2, 1,
     'Giao hàng toàn quốc, theo dõi được đơn của bạn.

Phí và thời gian giao hàng:
- Giao tiêu chuẩn: 3 - 5 ngày
- Giao nhanh: 1 - 2 ngày

Quy trình giao hàng:
1. Xác nhận đơn: BetaBook gọi hoặc nhắn tin xác nhận trong vòng 24 giờ.
2. Đóng gói và bàn giao: Sách được bọc kỹ rồi giao cho đơn vị vận chuyển.
3. Nhận hàng: Bạn kiểm tra kiện hàng trước khi thanh toán hoặc ký nhận.

Điều cần biết:
- Thời gian giao tính từ lúc đơn được xác nhận, không tính Chủ nhật và ngày lễ.
- Vùng xa hoặc hải đảo có thể lâu hơn 1 - 2 ngày so với thời gian dự kiến.
- Đơn dùng mã FREESHIP được miễn phí vận chuyển theo điều kiện của mã.'),
    (3, 'Chính sách bảo mật', 'chinh-sach-bao-mat', 'Chính sách', 3, 1,
     'BetaBook thu thập ít nhất có thể và không bán dữ liệu của bạn.

Thông tin chúng tôi thu thập:
- Họ tên, số điện thoại, email và địa chỉ giao hàng khi bạn đăng ký hoặc đặt hàng.
- Lịch sử đơn hàng và sách bạn đã xem để gợi ý sách phù hợp hơn.

Chúng tôi dùng thông tin để:
- Xử lý và giao đơn hàng, hỗ trợ bạn khi có vấn đề.
- Gửi thông tin ưu đãi, nếu bạn đồng ý nhận.

Cam kết của BetaBook:
- Không bán hoặc trao đổi thông tin cá nhân cho bên thứ ba vì mục đích thương mại.
- Chỉ chia sẻ thông tin cần thiết cho đơn vị vận chuyển để giao hàng cho bạn.
- Bạn có quyền xem, sửa hoặc yêu cầu xóa thông tin bất cứ lúc nào.

Muốn xóa tài khoản hoặc hủy nhận email? Gửi yêu cầu đến mkt.betabook@gmail.com, BetaBook xử lý trong vòng 48 giờ.'),
    (4, 'Chính sách đổi trả', 'chinh-sach-doi-tra', 'Chính sách', 4, 1,
     'Đổi trả trong vòng 7 ngày kể từ khi nhận sách.

Được đổi trả khi:
- Sách bị lỗi in ấn, thiếu trang, rách hoặc hư hỏng do vận chuyển.
- BetaBook giao sai tên sách hoặc sai số lượng so với đơn đặt.

Không áp dụng khi:
- Sách đã qua sử dụng, viết vẽ, ướt hoặc mất bao bọc, tem nhãn.
- Quá 7 ngày kể từ ngày nhận hàng.
- Sản phẩm thuộc chương trình xả kho, trừ khi bị lỗi từ nhà xuất bản.

Cách đổi trả:
1. Liên hệ BetaBook qua hotline hoặc biểu mẫu liên hệ, kèm mã đơn và ảnh sản phẩm.
2. Gửi sách về theo hướng dẫn. BetaBook chịu phí gửi nếu lỗi thuộc về BetaBook.
3. Nhận sách mới hoặc hoàn tiền trong 3 - 5 ngày làm việc sau khi BetaBook nhận được sách.');

INSERT INTO admin_menu_items
    (menu_key, label, page_key, active_pages, icon_class, is_external, sort_order, is_active)
VALUES
    ('policies', 'Quản lý chính sách', 'policies', 'policies,policy-form',
     'bi bi-file-earmark-text', 0, 50, 1)
ON DUPLICATE KEY UPDATE
    label = VALUES(label),
    page_key = VALUES(page_key),
    active_pages = VALUES(active_pages),
    icon_class = VALUES(icon_class),
    is_external = VALUES(is_external),
    sort_order = VALUES(sort_order),
    is_active = VALUES(is_active);

INSERT INTO admin_menu_permissions (role_id, menu_id, can_view)
SELECT r.role_id, m.menu_id, 1
FROM roles r
JOIN admin_menu_items m ON m.menu_key = 'policies'
WHERE r.role_key = 'admin'
ON DUPLICATE KEY UPDATE can_view = 1;

DELETE p
FROM admin_menu_permissions p
JOIN roles r ON r.role_id = p.role_id
JOIN admin_menu_items m ON m.menu_id = p.menu_id
WHERE r.role_key = 'staff'
  AND m.menu_key = 'policies';
