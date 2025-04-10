<?php
require_once __DIR__ . '/../../Controller/UserController.php';
$database = new Database();
$conn = $database->getConnection();
$controller = new UserController($conn);
$message = $controller->registerUser();

$roles = $controller->getRoles();
?>
<?php include '../shares/adminhd.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản | Premium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(145deg, #5e60ce, #6930c3);
            --accent-color: #64dfdf;
            --dark-color: #2b2d42;
            --light-color: #f8f9fa;
            --info-color: #3498db;
            --accent-color: #3498db;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(46, 54, 80, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Poppins', 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 70px 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(94, 96, 206, 0.2), rgba(100, 223, 223, 0.1));
            z-index: -1;
        }

        body::after {
            content: "";
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(72, 191, 227, 0.2), rgba(94, 96, 206, 0.1));
            z-index: -1;
        }

        .container {
            max-width: 550px;
            width: 100%;
        }

        .card {
            border: none;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
            overflow: hidden;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transform: translateY(0);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(46, 54, 80, 0.15);
        }

        .card-header {
            background: var(--primary-gradient);
            padding: 1.8rem 2rem;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .card-header::after {
            content: "";
            position: absolute;
            bottom: -30%;
            left: -30%;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .card-header h2 {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 1.75rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
            margin-right: 45px;
        }

        .card-body {
            padding: 2.5rem 2rem;
        }

        .floating-label {
            position: relative;
            margin-bottom: 1.8rem;
        }

        .floating-label input,
        .floating-label select {
            height: 58px;
            padding-left: 55px;
            padding-top: 20px;
            /* Added padding to the top to make room for label */
            width: 100%;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            background-color: white;
            font-size: 1rem;
            transition: var(--transition);
        }

        .floating-label input:hover,
        .floating-label select:hover {
            border-color: #c8d0e9;
        }

        .floating-label label {
            position: absolute;
            top: 6px;
            /* Reduced from top: 0 to position higher */
            left: 55px;
            font-size: 0.85rem;
            /* Smaller font for label */
            pointer-events: none;
            transition: var(--transition);
            color: #858796;
            font-weight: 500;
            opacity: 0.8;
        }

        .floating-label input:focus,
        .floating-label select:focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(94, 96, 206, 0.15);
            border-color: var(--primary-color);
        }

        /* Removed the transform effect on labels */
        .floating-label input:focus+label,
        .floating-label select:focus+label {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Added this to show label by default */
        .floating-label input::placeholder {
            color: transparent;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1.2rem;
            z-index: 10;
            opacity: 0.8;
            transition: var(--transition);
        }

        .floating-label input:focus~.input-icon,
        .floating-label select:focus~.input-icon {
            color: var(--primary-color);
            opacity: 1;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            font-weight: 600;
            padding: 0.9rem 1.5rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            box-shadow: 0 5px 15px rgba(94, 96, 206, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.1));
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(145deg, #6930c3, #5e60ce);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(94, 96, 206, 0.4);
        }

        .btn-primary:hover::after {
            transform: translateX(100%);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .alert {
            border-radius: var(--border-radius);
            margin-bottom: 1.8rem;
            border: none;
            background-color: rgba(72, 191, 227, 0.15);
            border-left: 4px solid var(--secondary-color);
            color: var(--dark-color);
            padding: 1rem 1.5rem;
            font-weight: 500;
        }

        .alert-info {
            background-color: rgba(72, 191, 227, 0.15);
            border-left-color: var(--secondary-color);
        }

        .form-select {
            background-position: right 1rem center;
            cursor: pointer;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #858796;
            cursor: pointer;
            z-index: 10;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-weight: 500;
            color: #6c757d;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
            transition: var(--transition);
        }

        .login-link a:hover {
            color: #6930c3;
            text-decoration: underline;
        }

        /* Animation effects */
        .form-control-animated {
            animation: fadeInUp 0.6s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        /* Progress indicator */
        .progress-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .progress-line {
            position: absolute;
            top: 15px;
            height: 3px;
            background: #e4e8f0;
            width: 100%;
            z-index: 1;
        }

        .progress-line-active {
            position: absolute;
            top: 15px;
            height: 3px;
            background: var(--primary-gradient);
            width: 0%;
            z-index: 2;
            transition: width 0.6s ease;
        }

        .progress-step {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: white;
            border: 3px solid #e4e8f0;
            z-index: 3;
            position: relative;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-step.active {
            border-color: var(--primary-color);
            background: white;
        }

        .progress-step.completed {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        /* Password strength meter */
        .password-strength {
            height: 5px;
            margin-top: 8px;
            border-radius: 5px;
            background: #e9ecef;
            overflow: hidden;
            transition: var(--transition);
        }

        .password-strength-meter {
            height: 100%;
            width: 0;
            border-radius: 5px;
            transition: var(--transition);
        }

        .strength-text {
            font-size: 0.75rem;
            margin-top: 5px;
            display: none;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card animate__animated animate__fadeIn">
            <div class="card-header text-center">
            <a href="index.php" class="back-button">
            <i class="fas fa-arrow-left" style="background-color: #00b8f2;color: white;border-radius: 5px;font-size: 30px;"></i></a>
                <h2><i class="fas fa-user-plus me-2"></i>Đăng ký tài khoản</h2>
            </div>
            <div class="card-body">
                <div class="progress-indicator position-relative mb-4">
                    <div class="progress-line"></div>
                    <div class="progress-line-active" id="progress-line"></div>
                    <div class="progress-step active" id="step1"><i class="fas fa-user-alt fa-sm"></i></div>
                    <div class="progress-step" id="step2"><i class="fas fa-lock fa-sm"></i></div>
                    <div class="progress-step" id="step3"><i class="fas fa-check fa-sm"></i></div>
                </div>

                <?php if (isset($message)): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form method="POST" action="/Quanlysukien/Controller/UserController.php?action=registerad" id="registrationForm">
                    <div id="form-step-1" class="form-step">
                        <div class="floating-label form-control-animated" style="animation-delay: 0.1s">
                            <input type="text" name="fullname" id="fullname" class="form-control" placeholder=" " required>
                            <label for="fullname">Họ và tên</label>
                            <span class="input-icon"><i class="fas fa-user"></i></span>
                        </div>

                        <div class="floating-label form-control-animated" style="animation-delay: 0.2s">
                            <input type="text" name="username" id="username" class="form-control" placeholder=" " required>
                            <label for="username">Tên đăng nhập</label>
                            <span class="input-icon"><i class="fas fa-user-tag"></i></span>
                        </div>

                        <div class="floating-label form-control-animated" style="animation-delay: 0.3s">
                            <input type="email" name="email" id="email" class="form-control" placeholder=" " required>
                            <label for="email">Email</label>
                            <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        </div>

                        <div class="d-grid gap-2 mt-4 form-control-animated" style="animation-delay: 0.4s">
                            <button type="button" class="btn btn-primary btn-lg next-step" data-step="1">
                                Tiếp tục <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <div id="form-step-2" class="form-step" style="display: none;">
                        <div class="floating-label form-control-animated">
                            <input type="password" name="password" class="form-control" placeholder=" " required id="password">
                            <label for="password">Mật khẩu</label>
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <span class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-toggle-icon"></i>
                            </span>
                            <div class="password-strength">
                                <div class="password-strength-meter" id="password-strength-meter"></div>
                            </div>
                            <small class="strength-text text-danger" id="strength-poor">Yếu - Hãy thêm ký tự đặc biệt</small>
                            <small class="strength-text text-warning" id="strength-medium">Trung bình - Thêm chữ hoa/số</small>
                            <small class="strength-text text-success" id="strength-strong">Mạnh - Mật khẩu tốt</small>
                        </div>

                        <div class="floating-label form-control-animated">
                            <input type="password" name="confirm_password" class="form-control" placeholder=" " required id="confirm_password">
                            <label for="confirm_password">Xác nhận mật khẩu</label>
                            <span class="input-icon"><i class="fas fa-key"></i></span>
                            <span class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye" id="confirm-password-toggle-icon"></i>
                            </span>
                        </div>

                        <div class="floating-label form-control-animated">
                            <select name="role" id="role" class="form-select" required>
                                <option value="" disabled selected></option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['RoleId']; ?>"><?php echo $role['RoleName']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="role">Vai trò</label>
                            <span class="input-icon"><i class="fas fa-user-shield"></i></span>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-step="2">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-step="2">
                                Tiếp tục <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <div id="form-step-3" class="form-step" style="display: none;">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h4>Xác nhận thông tin</h4>
                            <p class="text-muted">Vui lòng kiểm tra lại thông tin trước khi đăng ký</p>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>Họ và tên:</strong>
                                <span id="summary-fullname"></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>Tên đăng nhập:</strong>
                                <span id="summary-username"></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>Email:</strong>
                                <span id="summary-email"></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>Vai trò:</strong>
                                <span id="summary-role"></span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-step="3">
                                <i class="fas fa-arrow-left me-1"></i> Chỉnh sửa
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check me-1"></i> Hoàn tất đăng ký
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        // Multistep form
        document.addEventListener('DOMContentLoaded', function() {
            const nextButtons = document.querySelectorAll('.next-step');
            const prevButtons = document.querySelectorAll('.prev-step');
            const progressLine = document.getElementById('progress-line');

            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const step = parseInt(this.getAttribute('data-step'));

                    // Validate current step
                    if (validateStep(step)) {
                        // Hide current step
                        document.getElementById(`form-step-${step}`).style.display = 'none';
                        // Show next step
                        document.getElementById(`form-step-${step + 1}`).style.display = 'block';

                        // Update progress steps
                        document.getElementById(`step${step}`).classList.add('completed');
                        document.getElementById(`step${step + 1}`).classList.add('active');

                        // Update progress line
                        progressLine.style.width = `${(step * 50)}%`;

                        // Update summary if going to step 3
                        if (step + 1 === 3) {
                            updateSummary();
                        }
                    }
                });
            });

            prevButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const step = parseInt(this.getAttribute('data-step'));

                    // Hide current step
                    document.getElementById(`form-step-${step}`).style.display = 'none';
                    // Show previous step
                    document.getElementById(`form-step-${step - 1}`).style.display = 'block';

                    // Update progress steps
                    document.getElementById(`step${step}`).classList.remove('active');
                    document.getElementById(`step${step - 1}`).classList.remove('completed');
                    document.getElementById(`step${step - 1}`).classList.add('active');

                    // Update progress line
                    progressLine.style.width = `${((step - 1) * 50)}%`;
                });
            });

            // Password validation and strength meter
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const strengthMeter = document.getElementById('password-strength-meter');
            const strengthPoor = document.getElementById('strength-poor');
            const strengthMedium = document.getElementById('strength-medium');
            const strengthStrong = document.getElementById('strength-strong');

            passwordInput.addEventListener('input', function() {
                const value = this.value;
                const strength = calculatePasswordStrength(value);

                // Update strength meter
                strengthMeter.style.width = `${strength}%`;

                // Hide all strength text
                strengthPoor.style.display = 'none';
                strengthMedium.style.display = 'none';
                strengthStrong.style.display = 'none';

                // Update strength text and color
                if (strength < 40) {
                    strengthMeter.style.backgroundColor = '#dc3545';
                    strengthPoor.style.display = 'block';
                } else if (strength < 80) {
                    strengthMeter.style.backgroundColor = '#ffc107';
                    strengthMedium.style.display = 'block';
                } else {
                    strengthMeter.style.backgroundColor = '#28a745';
                    strengthStrong.style.display = 'block';
                }
            });

            confirmPasswordInput.addEventListener('input', function() {
                if (this.value !== passwordInput.value) {
                    this.setCustomValidity('Mật khẩu không khớp');
                } else {
                    this.setCustomValidity('');
                }
            });
        });

        // Toggle password visibility
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(inputId === 'password' ? 'password-toggle-icon' : 'confirm-password-toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Calculate password strength
        function calculatePasswordStrength(password) {
            let strength = 0;

            if (password.length > 6) {
                strength += 20;
            }

            if (password.length > 10) {
                strength += 20;
            }

            if (/[A-Z]/.test(password)) {
                strength += 20;
            }

            if (/[0-9]/.test(password)) {
                strength += 20;
            }

            if (/[^A-Za-z0-9]/.test(password)) {
                strength += 20;
            }

            return strength;
        }

        // Validate each step
        function validateStep(step) {
            if (step === 1) {
                const fullname = document.getElementById('fullname');
                const username = document.getElementById('username');
                const email = document.getElementById('email');

                return fullname.checkValidity() && username.checkValidity() && email.checkValidity();
            } else if (step === 2) {
                const password = document.getElementById('password');
                const confirmPassword = document.getElementById('confirm_password');
                const role = document.getElementById('role');

                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Mật khẩu không khớp');
                    confirmPassword.reportValidity();
                    return false;
                }

                return password.checkValidity() && confirmPassword.checkValidity() && role.checkValidity();
            }

            return true;
        }

        // Update summary before submission
        function updateSummary() {
            document.getElementById('summary-fullname').textContent = document.getElementById('fullname').value;
            document.getElementById('summary-username').textContent = document.getElementById('username').value;
            document.getElementById('summary-email').textContent = document.getElementById('email').value;

            const roleSelect = document.getElementById('role');
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            document.getElementById('summary-role').textContent = selectedOption.text;
        }
    </script>
</body>

</html>