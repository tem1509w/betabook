<section class="page-banner">
  <h1>Hướng dẫn sử dụng</h1>
  <p>Mọi thứ bạn cần biết để tìm, lưu và đặt mua sách trên BetaBook.</p>
</section>

<main class="container section">
  <div class="policy-layout">
    <aside class="policy-nav">
      <h2>Mục lục hướng dẫn</h2>
      <ul>
        <li><a href="#dang-ky">Đăng ký &amp; Đăng nhập</a></li>
        <li><a href="#tim-sach">Tìm kiếm sách</a></li>
        <li><a href="#xem-chi-tiet">Xem chi tiết sách</a></li>
        <li><a href="#gio-hang">Giỏ hàng &amp; Đặt hàng</a></li>
        <li><a href="#yeu-thich">Sản phẩm yêu thích</a></li>
        <li><a href="#ma-giam">Sử dụng mã giảm giá</a></li>
        <li><a href="#theo-doi">Theo dõi đơn hàng</a></li>
      </ul>
    </aside>

    <div class="policy-content">
      <section class="policy" id="dang-ky">
        <div class="policy-head">
          <div>
            <h2>Đăng ký &amp; Đăng nhập</h2>
            <p>Tạo tài khoản để đặt hàng, dùng mã giảm giá và xem lịch sử đơn.</p>
          </div>
        </div>
        <h3>Đăng ký tài khoản</h3>
        <ol class="steps-list">
          <li>Mở trang <a href="<?php echo e(url('login')); ?>">Đăng nhập / Đăng ký</a> từ biểu tượng tài khoản.</li>
          <li>Chọn tab <b>Đăng ký</b>, nhập tên đăng nhập, email, mật khẩu và xác nhận mật khẩu. Số điện thoại là tùy chọn.</li>
          <li>Mật khẩu cần ít nhất 6 ký tự; số điện thoại nếu nhập phải gồm 10 hoặc 11 chữ số và bắt đầu bằng 0.</li>
        </ol>
        <h3>Đăng nhập</h3>
        <ol class="steps-list">
          <li>Chọn tab <b>Đăng nhập</b> trên trang tài khoản.</li>
          <li>Nhập tên đăng nhập và mật khẩu đã đăng ký.</li>
        </ol>
      </section>

      <section class="policy" id="tim-sach">
        <div class="policy-head">
          <div>
            <h2>Tìm kiếm sách</h2>
            <p>Tìm nhanh sách bằng thanh tìm kiếm hoặc danh mục.</p>
          </div>
        </div>
        <ol class="steps-list">
          <li>Nhập từ khóa vào ô tìm kiếm trên đầu trang và nhấn Enter hoặc nút kính lúp.</li>
          <li>Mở trang <a href="<?php echo e(url('booklist')); ?>">Tủ sách</a> để duyệt danh mục, sắp xếp và lọc sản phẩm.</li>
          <li>Chọn bìa hoặc tên sách để xem thông tin và tình trạng còn hàng.</li>
        </ol>
      </section>

      <section class="policy" id="xem-chi-tiet">
        <div class="policy-head">
          <div>
            <h2>Xem chi tiết sách</h2>
            <p>Kiểm tra thông tin sách trước khi thêm vào giỏ.</p>
          </div>
        </div>
        <ol class="steps-list">
          <li>Trong trang chi tiết, xem ảnh, tác giả, nhà xuất bản, giá, tồn kho và thông tin sản phẩm.</li>
          <li>Chọn số lượng rồi bấm <b>Thêm vào giỏ</b> hoặc <b>Mua ngay</b>.</li>
          <li>Bấm biểu tượng trái tim để lưu sách vào danh sách yêu thích; có thể mở danh sách từ trái tim trên header.</li>
        </ol>
      </section>

      <section class="policy" id="gio-hang">
        <div class="policy-head">
          <div>
            <h2>Giỏ hàng &amp; Đặt hàng</h2>
            <p>Kiểm tra sách và điền thông tin nhận hàng để hoàn tất đơn.</p>
          </div>
        </div>
        <h3>Kiểm tra giỏ hàng</h3>
        <ol class="steps-list">
          <li>Mở biểu tượng giỏ hàng để xem sản phẩm, thay đổi số lượng hoặc xóa sách.</li>
          <li>Đăng nhập để áp dụng mã giảm giá. Mỗi đơn chỉ dùng một mã; mã phải thỏa điều kiện đang hiển thị.</li>
        </ol>
        <h3>Đặt hàng</h3>
        <ol class="steps-list">
          <li>Điền họ tên, số điện thoại, email, địa chỉ, phường/xã, quận/huyện và tỉnh/thành phố.</li>
          <li>Chọn thanh toán khi nhận hàng (COD) hoặc chuyển khoản ngân hàng.</li>
          <li>Kiểm tra tóm tắt đơn, đánh dấu đồng ý điều khoản mua hàng rồi bấm <b>Đặt hàng</b>.</li>
        </ol>
        <div class="note">Phí vận chuyển hiện được tính là 0 trong hệ thống; tùy chọn phí và thời gian giao hàng chưa được triển khai.</div>
      </section>

      <section class="policy" id="yeu-thich">
        <div class="policy-head">
          <div>
            <h2>Sản phẩm yêu thích</h2>
            <p>Lưu lại sách để xem và mua sau.</p>
          </div>
        </div>
        <ol class="steps-list">
          <li>Chọn trái tim tại trang chi tiết để thêm sách vào danh sách yêu thích.</li>
          <li>Mở biểu tượng yêu thích trên header để xem danh sách, bỏ lưu hoặc thêm từng sách còn hàng vào giỏ.</li>
          <li>Có thể thêm tất cả sách còn hàng vào giỏ hoặc xóa danh sách.</li>
        </ol>
        <div class="note">Bạn cần đăng nhập hoặc đăng ký tài khoản để sử dụng danh sách yêu thích.</div>
      </section>

      <section class="policy" id="ma-giam">
        <div class="policy-head">
          <div>
            <h2>Sử dụng mã giảm giá</h2>
            <p>Áp dụng ưu đãi tại trang giỏ hàng.</p>
          </div>
        </div>
        <ol class="steps-list">
          <li>Xem mã tại trang <a href="<?php echo e(url('discount-event')); ?>">Mã giảm - Sự kiện</a>.</li>
          <li>Đăng nhập, mở giỏ hàng, nhập mã vào ô mã giảm và bấm <b>Áp dụng</b>.</li>
          <li>Kiểm tra điều kiện và số tiền giảm trong phần tóm tắt đơn hàng. Mỗi đơn chỉ áp dụng một mã.</li>
        </ol>
      </section>

      <section class="policy" id="theo-doi">
        <div class="policy-head">
          <div>
            <h2>Theo dõi đơn hàng</h2>
            <p>Xem trạng thái và thông tin đơn hàng trong tài khoản.</p>
          </div>
        </div>
        <ol class="steps-list">
          <li>Đăng nhập và mở trang <a href="<?php echo e(url('account')); ?>">Tài khoản</a>.</li>
          <li>Chọn mục <b>Đơn hàng của tôi</b>, mở đơn để xem trạng thái, sách đã đặt và thông tin nhận hàng.</li>
          <li>Khi cần hỗ trợ, gửi mã đơn qua trang <a href="<?php echo e(url('contact')); ?>">Liên hệ</a>.</li>
        </ol>
      </section>

      <div class="policy-contact">
        <div>
          <h2>Vẫn cần hỗ trợ thêm?</h2>
          <p>Hotline 0123 456 789 · mkt.betabook@gmail.com</p>
        </div>
        <a class="button white" href="<?php echo e(url('contact')); ?>">Liên hệ BetaBook</a>
      </div>
    </div>
  </div>
</main>