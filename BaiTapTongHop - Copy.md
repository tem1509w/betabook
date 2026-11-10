**HỌC PHẦN LẬP TRÌNH WEB  •  511014** 

# **BÀI TẬP 04 — PHÂN TÍCH VÀ HIỆN THỰC MỘT CHỨC NĂNG TRỌNG TÂM (PHP/MYSQL)** 

**Học phần:** Lập trình Web (511014) **Hình thức:** CÁ NHÂN; tiếp tục đúng đề tài nhóm và phần việc đã được phân công **Thời điểm giao:** Sau BT03 và các nội dung PHP liên quan đã học **Hạn nộp:** Theo thông báo LMS 

**CLO:** 014-K2, 014-K3, 014-S4, 014-A1; đối chiếu phối hợp nhóm qua minh chứng 014-A4. 

# **1. Căn cứ và nguyên tắc** 

- Tiếp nối **BT03 – Chuyển đổi Project sang PHP/MySQL (Public + Admin)** . Mỗi sinh viên chọn **01 chức năng chính, có ý nghĩa nghiệp vụ** , thuộc đúng cụm màn hình/module bản thân đã được giao và thực hiện; không chọn lại một chức năng không liên quan để làm thay. 

- Mục tiêu là **giải thích được trọn luồng từ thao tác trên Page → dữ liệu → bảng/khóa → PHP function → câu SQL → kết quả trả về** , đồng thời hiện thực hoặc hoàn thiện chức năng đó trên source cá nhân có thể chạy, kiểm tra được. 

- Có thể chọn chức năng đọc dữ liệu (tìm kiếm, lọc, phân trang, xem chi tiết) hoặc chức năng thay đổi dữ liệu/trạng thái (thêm/sửa, giỏ/danh sách chọn, đặt hàng, cập nhật trạng thái). **Không bắt buộc tất cả sinh viên cùng làm CRUD, giỏ hàng, AJAX hoặc transaction** nếu không phù hợp với đề tài. 

- Được dùng khung Router, class Database, layout dùng chung; **phân tích, câu SQL, function nghiệp vụ và minh chứng phải gắn với chức năng cá nhân** . Không nộp lại nguyên MiniShop mẫu hoặc chỉ mô tả giao diện mà không chỉ ra dữ liệu thực. 

# **2. Yêu cầu bắt buộc** 

# **2.1. Chọn chức năng và xác định phạm vi (Y01)** 

- Ghi tên đề tài, tên module, Page/URL, khu vực Public hay Admin, Actor có quyền dùng và tên **01 chức năng trọng tâm** ; nêu vì sao quan trọng đối với nghiệp vụ. 

- Chỉ ra màn hình/file từ BT03 tương ứng, phần việc được phân công và 01–02 ảnh chụp giao diện đang có. Nếu điều chỉnh phân công phải ghi lý do và xác nhận của nhóm trưởng. 

# **2.2. Phân tích chức năng trên Page (Y02)** 

- Đánh dấu trên ảnh màn hình **các ô dữ liệu, nút bấm, bảng, liên kết** liên quan; mô tả trình tự người dùng thao tác, đầu vào bắt buộc/tùy chọn, đầu ra và thông báo lỗi/thành công. 

- Với mỗi dữ liệu nhập hoặc hiển thị: ghi tên nhãn trên giao diện, tên biến/thuộc tính name hoặc tham số URL, kiểu giá trị dự kiến, điều kiện kiểm tra, và nơi lấy hoặc lưu giá trị. Phân biệt dữ liệu nhập từ GET/POST, Session, dữ liệu CSDL và dữ liệu tính toán. 

# **2.3. Phân tích bảng dữ liệu, trường và quan hệ (Y03)** 

- Liệt kê **tất cả các bảng có tham gia trực tiếp** vào chức năng, các trường cần đọc/ghi, kiểu dữ liệu quan trọng; chỉ ra **Primary Key (khóa chính)** , **Foreign Key (khóa ngoại)** và quan hệ liên kết. Trích sơ đồ ERD phần liên quan, không yêu cầu vẽ lại toàn bộ CSDL của nhóm. 

- Lập bảng ánh xạ **thành phần giao diện → tham số PHP → bảng.trường nguồn/đích → ý nghĩa** , chỉ rõ các trường suy ra (SUM, số lượng × đơn giá, tồn khả dụng…) hoặc lấy từ Session và **không tồn tại thành cột CSDL** . 

- Giải thích: dữ liệu này lấy từ bảng nào, vì sao cần bảng đó, dùng khóa nào để tìm/liên kết, điều kiện nào xác định bản ghi hợp lệ; nếu chỉ dùng một bảng thì nêu lý do. 

Bộ bài tập và minh chứng · MonWeb_PHP  |  1 

**HỌC PHẦN LẬP TRÌNH WEB  •  511014** 

# **2.4. Phân tích luồng xử lý và xây dựng function (Y04)** 

- Vẽ sơ đồ luồng thực tế: **Page/Form hoặc nút → URL/Router → action.php → function nghiệp vụ → class Database/Session → SQL → dữ liệu trả về → Page/JSON** . Nêu rõ vị trí mỗi file đang dùng trong source của mình; nếu cấu trúc khác mẫu BT03 thì giải thích tương đương. 

- Lập danh sách **các function cần dùng** , ghi tên + đường dẫn file, tham số đầu vào, dữ liệu trả về, function nào gọi function nào và nhiệm vụ từng function. Phân biệt function xử lý nghiệp vụ với hàm chung của class Database. 

- Xây dựng hoặc hoàn thiện **ít nhất 01 function nghiệp vụ cốt lõi hoạt động thật** , các function phụ trợ cần thiết và liên kết từ màn hình. Nếu dùng AJAX, chỉ rõ JavaScript fetch() gửi gì, API trả JSON gì; nếu dùng Session, chỉ ra cấu trúc lưu và lúc nào truy vấn MySQL. 

# **2.5. Viết và phân tích câu lệnh SQL thực tế (Y05)** 

- Trích đúng câu SQL được function cốt lõi sử dụng, có đủ **tên bảng, tên trường, điều kiện, tham số** ; kèm giá trị tham số kiểm thử, kết quả truy vấn thực tế và ảnh chụp MySQL/phpMyAdmin/HeidiSQL. 

- Giải thích theo chức năng từng phần được dùng: SELECT lấy trường nào; FROM bảng chính; JOIN ... ON ghép theo khóa nào; WHERE lọc gì; GROUP BY/HAVING tổng hợp gì; ORDER BY, LIMIT/OFFSET dùng khi nào. **Mục không sử dụng ghi “không áp dụng”, không thêm SQL giả để đủ mục.** 

- Nếu chức năng **đọc dữ liệu** : có ít nhất 01 câu SELECT hoạt động; nếu có phân trang, trình bày truy vấn đếm và truy vấn lấy danh sách dùng điều kiện thống nhất. Nếu chức năng **ghi/thay đổi CSDL** : trình bày cả câu đọc phục vụ chức năng (nếu có) và INSERT/UPDATE/DELETE liên quan, dữ liệu trước/sau. 

- Dữ liệu từ người dùng phải truyền an toàn bằng **Prepared Statement** và tham số; không nối trực tiếp GET/POST vào SQL. Với tên cột sắp xếp, chỉ dùng danh sách cho phép. Không ép tạo JOIN nếu nghiệp vụ thực tế không cần bảng liên quan. 

# **2.6. Kiểm tra tính đúng đắn và an toàn (Y06)** 

- Chạy ít nhất **04 kịch bản** : thao tác hợp lệ; dữ liệu trống/không có kết quả; đầu vào không hợp lệ hoặc tham số bị sửa; kiểm tra dữ liệu/trạng thái sau xử lý. Tùy tính chất chức năng, bổ sung kiểm thử phân quyền, số lượng, trùng dữ liệu, chuyển trang hoặc rollback. 

- Chức năng dùng POST phải kiểm tra dữ liệu tại server, dùng CSRF khi phù hợp, chống SQL Injection; nội dung người dùng hiển thị bằng e()/htmlspecialchars(). Chức năng cập nhật nhiều bảng phải xem xét Transaction; **chỉ dùng khi nghiệp vụ cần bảo đảm đồng nhất dữ liệu** . 

- Mỗi kịch bản có **thao tác, kết quả mong đợi, kết quả thực tế, minh chứng** . Với chức năng ghi dữ liệu, đối chiếu bản ghi MySQL trước và sau; với chức năng chỉ đọc, đối chiếu kết quả và điều kiện lọc. 

# **3. Minh họa chức năng tương đương theo đề tài** 

|**Đề tài**|**Chức năng trọng tâm có thể chọn**|**Dữ liệu / bảng liên quan (gợi ý)**|
|---|---|---|
|Bán hàng|Tìm kiếm – lọc – phân trang sản phẩm; đặt hàng;<br>thay đổi trạng thái đơn|products, categories, orders, order_items, inventory|
|Thư viện|Tìm sách theo danh mục; tạo phiếu mượn; cập<br>nhật trả sách|books, categories, borrow_records, borrow_items|
|Khách sạn|Tìm phòng còn trống; đặt phòng; quản lý trạng<br>thái đặt phòng|rooms, room_types, bookings, guests|
|Đào tạo|Tìm khóa học; đăng ký lớp; quản lý danh sách<br>học viên|courses, classes, enrollments, students|
|Thiết bị|Lọc thiết bị; thêm/cập nhật thiết bị; ghi nhận cấp<br>phát|devices, device_types, assignments|



Bộ bài tập và minh chứng · MonWeb_PHP  |  2 

**HỌC PHẦN LẬP TRÌNH WEB  •  511014** 

Các tên bảng trên chỉ để minh họa. **Dùng tên bảng, khóa và trường đúng với CSDL thật của đề tài** , không bắt buộc sao chép cấu trúc MiniShop hoặc chọn cùng loại chức năng. 

# **4. Các bảng phân tích và minh chứng bắt buộc** 

# **4.1. Bảng ánh xạ Page → dữ liệu (mẫu trình bày)** 

|**Page / thành phần**|**Input / dữ liệu PHP**|**Nguồn hoặc đích dữ liệu**|**Cách sử dụng trong chức năng**|
|---|---|---|---|
|Ô tìm kiếm sản phẩm (ví<br>dụ)|q từ $_GET|Không phải cột; dùng đối chiếu<br>products.product_name|Tạo điều kiện LIKE ?|
|Bộ lọc danh mục (ví dụ)|category từ $_GET|products.category_id ↔<br>categories.category_id|Lọc theo khóa danh mục|
|Tên danh mục hiển thị|Tên trường kết quả<br>SQL|categories.category_name|In kết quả ra giao diện|



Bảng minh họa trên không thay thế bảng phân tích thực tế. Bài nộp phải có hàng cho **mọi dữ liệu và thao tác chính** của chức năng đã chọn. 

# **4.2. Bảng function và liên kết xử lý (mẫu trình bày)** 

|**File / function**|**Tham số vào**|**SQL hoặc trạng thái tác động**|**Giá trị trả về / nơi sử dụng**|
|---|---|---|---|
|pages/.../functions.php ·|Biến|SELECT/UPDATE hoặc|Array/boolean/lỗi; action.php|
|tênHàm(...)|GET/POST/ID/lọc…|Session; ghi rõ bảng|hoặc index.php|
|pages/.../action.php|Request GET/POST|Validate → gọi function nghiệp<br>vụ|HTML/redirect/JSON; xử lý lỗi|



Đi kèm sơ đồ luồng và **đoạn source thực tế có đánh dấu vị trí gọi hàm** . Không chỉ liệt kê tên function mà thiếu đầu vào, đầu ra hoặc nơi được gọi. 

# **4.3. Bảng kiểm thử tối thiểu** 

|**ID**|**Tình huống bắt buộc**|**Kết quả cần đối chiếu**|
|---|---|---|
|T01|Thao tác chính với dữ liệu hợp lệ|Page/JSON và SQL/CSDL đúng theo yêu cầu|
|T02|Không có kết quả, dữ liệu rỗng hoặc trường hợp biên|Hiển thị phù hợp, không gây lỗi PHP/SQL|
|T03|Dữ liệu đầu vào sai hoặc tự sửa GET/POST|Server từ chối hoặc xử lý an toàn, không ghi sai dữ liệu|
|T04|Kiểm tra quan hệ và trạng thái dữ liệu sau xử lý|Kết quả truy vấn khớp giao diện; cập nhật đúng hoặc<br>không thay đổi khi lỗi|



Nếu chọn chức năng có **phân trang / AJAX / upload / transaction / phân quyền** , phải có ít nhất một kịch bản kiểm thử đúng đặc thù đó trong bốn trường hợp trên hoặc kiểm thử bổ sung. 

Bộ bài tập và minh chứng · MonWeb_PHP  |  3 

**HỌC PHẦN LẬP TRÌNH WEB  •  511014** 

# **5. Hồ sơ nộp cá nhân** 

**NhomXX_MSSV_HoTen_BT04.zip** gồm: 

- **01_BaoCao/** : BaoCao_BT04.pdf hoặc .docx; mô tả chức năng, ảnh Page, bảng ánh xạ dữ liệu, sơ đồ ERD liên quan, bảng function, phân tích SQL, bảng T01–T04 và kết luận. Có họ tên/MSSV, đề tài và trích bảng phân công. 

- **02_SourcePHP/** : source đủ chạy lại **chức năng đã chọn** (Page, action, functions và các thành phần cần thiết); ghi rõ file nào do sinh viên làm/chỉnh. Không đưa mật khẩu thật, khóa API thật hay file cấu hình chứa thông tin nhạy cảm. 

- **03_Database/** : SQL tạo các bảng liên quan và dữ liệu mẫu đủ để chạy kiểm thử; có PK/FK, đồng nhất với câu SQL trong báo cáo. 

- **04_MinhChung/** : ảnh giao diện với dữ liệu thực, ảnh truy vấn SQL và kết quả, ảnh T01–T04, ảnh trước/sau nếu có ghi dữ liệu, và ghi chú đối chiếu từng ảnh với bước xử lý. 

**Không đạt yêu cầu cốt lõi** nếu: chỉ có ảnh giao diện hoặc phần mô tả lý thuyết; SQL không chạy trên CSDL nộp; không truy được nguồn của trường dữ liệu; source chỉ là mã mẫu không gắn chức năng cá nhân; không xác định được đóng góp thực tế. 

# **6. Rubric chi tiết – 10,00 điểm** 

Thang chấm này thể hiện đúng ba nhóm tiêu chí của **Rubric 3 – Bài tập** trong đề cương: Nộp bài (20%) + Trình bày (30%) + Nội dung (50%). 

|**Mã**||**Tiêu chí (minh chứng bắt buộc)**|**Điểm**<br>**tối đa**|
|---|---|---|---|
|P1 ·|Nộp|Đúng hạn (0,50); đủ báo cáo, source, CSDL và minh chứng có thể đối chiếu (1,50).|2,00|
|P2 ·|Trình bày|Mô tả rõ Page, Actor, chức năng, thao tác, dữ liệu vào/ra và ảnh được đánh dấu.|0,75|
|P3 ·|Trình bày|Bảng ánh xạ UI → biến → bảng.trường; ERD liên quan, PK/FK, nguồn dữ liệu dễ theo<br>dõi.|1,50|
|P4 ·|Trình bày|Sơ đồ luồng Router → action → function → SQL → kết quả; tên file và minh chứng<br>nhất quán.|0,75|
|P5 ·|Nội dung|Lựa chọn bảng, trường dữ liệu, kiểu nguồn/đích, khóa và quan hệ chính xác.|1,25|
|P6 ·|Nội dung|Câu SQL chạy đúng; giải thích JOIN/WHERE/param và các mệnh đề liên quan; kết quả<br>đối chiếu thực tế.|1,50|
|P7 ·|Nội dung|Function cốt lõi hoạt động; kết nối Page/action/Database đúng; kiểm tra đầu vào và quy<br>tắc nghiệp vụ.|1,50|
|P8 ·|Nội dung|T01–T04 có kết quả thực tế; xử lý được lỗi/biên và đối chiếu dữ liệu sau xử lý.|0,75|
|||TỔNG|10,00|



**Câu hỏi tự kiểm tra trước khi nộp:** Trang đang hiển thị trường dữ liệu nào, lấy từ bảng/cột nào? Hai bảng nối nhau bởi khóa gì? Tại sao câu SQL cần điều kiện đó? Tham số GET/POST đi vào function nào? Function trả về gì và Page dùng ở đâu? Dữ liệu có thay đổi đúng trong MySQL/Session khi thao tác thành công hoặc thất bại? 

Bộ bài tập và minh chứng · MonWeb_PHP  |  4 

