<main class="login-page">
  <div class="login-box">
    <div class="img-box login-img">
      <img src="<?php echo e(asset('images/banner/main-banner.png')); ?>" alt="BetaBook">
    </div>

    <div class="login-form">
      <?php if (!empty($_SESSION['user'])): ?>
        <?php $currentUser = $_SESSION['user']; ?>
        <h1>Tài khoản</h1>
        <?php if ($loginNotice): ?>
          <p class="login-notice" role="status"><?php echo e($loginNotice); ?></p>
        <?php endif; ?>
        <p class="sub">Bạn đã đăng nhập với tài khoản:</p>
        <p class="login-account-name"><?php echo e((string)($currentUser['full_name'] ?: $currentUser['username'])); ?></p>
        <p><?php echo e($currentUser['email']); ?></p>
        <form action="<?php echo e(url('login')); ?>" method="post">
          <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
          <input type="hidden" name="login_action" value="logout">
          <button type="submit" class="button full">Đăng xuất</button>
        </form>
      <?php else: ?>
        <input type="radio" name="login-mode" id="login-tab" checked>
        <input type="radio" name="login-mode" id="register-tab">
        <div class="tabs">
          <label for="login-tab">Đăng nhập</label>
          <label for="register-tab">Đăng ký</label>
        </div>

        <form id="login-form" class="login-auth-form" action="<?php echo e(url('login')); ?>" method="post">
          <h1>Đăng nhập</h1>
          <p class="sub">Chào mừng bạn quay lại BetaBook.</p>

          <?php if ($loginNotice): ?>
            <p class="login-notice" role="status"><?php echo e($loginNotice); ?></p>
          <?php endif; ?>
          <?php if ($loginError): ?>
            <p class="login-error" role="alert"><?php echo e($loginError); ?></p>
          <?php endif; ?>

          <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
          <div class="field">
            <label for="identifier">Tên đăng nhập, email hoặc số điện thoại</label>
            <input
              type="text"
              id="identifier"
              name="identifier"
              value="<?php echo e($identifier); ?>"
              autocomplete="username"
              required
              autofocus
              placeholder="Nhập tên đăng nhập, email hoặc SĐT">
          </div>
          <div class="field">
            <label for="password">Mật khẩu</label>
            <input
              type="password"
              id="password"
              name="password"
              autocomplete="current-password"
              required
              placeholder="Nhập mật khẩu">
          </div>
          <button type="submit" class="button full">Đăng nhập</button>
          <div class="social-login">
            <button type="button" class="social-button">
              <img class="social-mark" src="<?php echo e(asset('images/google.png')); ?>" alt="">
              <span>Tiếp tục với Google</span>
            </button>
          </div>
        </form>

        <div id="register-form" class="register-form">
          <h1>Tạo tài khoản</h1>
          <p class="sub">Đăng ký để mua sách và nhận ưu đãi từ BetaBook.</p>
          <div class="field">
            <label for="register-username">Username</label>
            <input type="text" id="register-username" placeholder="Nhập username">
          </div>
          <div class="field">
            <label for="register-email">Gmail</label>
            <input type="email" id="register-email" placeholder="Ví dụ: tenban@gmail.com">
          </div>
          <div class="field">
            <label for="register-phone">Số điện thoại</label>
            <input type="tel" id="register-phone" placeholder="Ví dụ: 0912345678">
          </div>
          <div class="field">
            <label for="register-password">Mật khẩu</label>
            <input type="password" id="register-password" placeholder="Ít nhất 6 ký tự">
          </div>
          <button type="button" class="button full">Đăng ký</button>
          <div class="social-login">
            <button type="button" class="social-button">
              <img class="social-mark" src="<?php echo e(asset('images/google.png')); ?>" alt="">
              <span>Tiếp tục với Google</span>
            </button>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>
