<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= base_url('logo/logo.png') ?>" type="image/png">
    <title>Login | LMS SMP 10 Pekalongan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: url('<?= base_url('logo/bg.png') ?>') no-repeat center center fixed;
            background-size: cover;
            z-index: -2;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .login-box {
            width: 100%;
            max-width: 430px;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
        }

        .logo-img {
            width: 100px;
            height: auto;
        }


        .login-box h4 {
            text-align: center;
            margin-bottom: 30px;
        }

        .social-icons {
            text-align: center;
            margin-top: 20px;
        }

        .social-icons a {
            margin: 0 10px;
            color: #555;
            font-size: 20px;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: #0d6efd;
        }

        .input-group-text {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="text-center mb-3">
            <img src="<?= base_url('logo/logo.png') ?>" alt="Logo LMS" class="logo-img">
        </div>
        <h4 class="text-dark">LMS SMP 10 Pekalongan</h4>

        <form action="<?= site_url('login/proses') ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input placeholder="Masukkan Username" type="text" name="username" required class="form-control" id="username" autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" placeholder="Masukkan Password" name="password" required class="form-control" id="password">
                    <span class="input-group-text" onclick="togglePassword()">
                        <i class="bi bi-eye" id="eye-icon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <!-- Tambahkan di bawah tombol login -->
        <div class="my-4 d-flex align-items-center text-muted">
            <hr class="flex-grow-1">
            <span class="mx-2">Ikuti kami di</span>
            <hr class="flex-grow-1">
        </div>

        <div class="d-flex justify-content-center gap-3">
            <a href="#" target="_blank" class="text-primary fs-4"><i class="fab fa-facebook"></i></a>
            <a href="#" target="_blank" class="text-danger fs-4"><i class="fab fa-youtube"></i></a>
            <a href="#" target="_blank" class="text-info fs-4"><i class="fab fa-twitter"></i></a>
            <a href="#" target="_blank" class="text-primary fs-4"><i class="fab fa-instagram"></i></a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (session()->getFlashdata('swal_error')): ?>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '<?= session()->getFlashdata('swal_error') ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('swal_success')): ?>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '<?= session()->getFlashdata('swal_success') ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        <?php endif; ?>

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>

</html>