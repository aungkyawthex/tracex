<?php
    include __DIR__ . '/../src/helpers/url.php';
    include __DIR__ . '/../src/bootstrap.php';
    require_once __DIR__ . '/../src/helpers/isGuest.php';
    require_once __DIR__ . '/../src/helpers/csrf.php';

    $title = "Sign In - TraceX";
    ob_start();
    $errors = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSignIn'])) {
        verifyCsrf();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember-me']) ? true : false;

        if(empty($email)) {
            $errors['email'] = "*email is required and cannot be empty";
        }

        if(empty($password)) {
            $errors['password'] = "*password is required and cannot be empty";
        }

        if(empty($errors)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if(!empty($user) && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];

                if($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + 60 * 60 * 24 * 30; // 30 days

                    // A failed remember-me token must not block an otherwise valid sign-in.
                    try {
                        $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
                        $stmt->execute([hash('sha256', $token), date('Y-m-d H:i:s', $expiry), $user['id']]);

                        setcookie('remember_token', $token, [
                            'expires' => $expiry,
                            'path' => '/',
                            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                            'httponly' => true,
                            'samesite' => 'Lax',
                        ]);
                    } catch (PDOException $e) {
                        logThrowable($e, 'failed to store remember-me token');
                    }
                }

                header("Location: ../public/index.php");
                exit();
            }

            $errors['login'] = "Invalid email or password";
        }
    }

    if(!isset($_SESSION['user_id'])) { //if not logged in
        if(isset($_COOKIE['remember_token'])) { // if user clicked remember me before
            $token = $_COOKIE['remember_token'];

            $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ? AND token_expiry > NOW()");
            $stmt->execute([hash('sha256', $token)]);
            $user = $stmt->fetch();

            if($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];

                header("Location: ../public/index.php");
                exit();
            }
        }
    }
?>
<?php if (!empty($email) && !empty($password) && isset($errors['login'])): ?>
    <?php $message = $errors['login']; include __DIR__ . '/../views/components/alert.php'; ?>
<?php endif; ?>

<h1 class="text-xl font-bold text-gray-900">Sign in to your account</h1>
<p class="mt-1 text-sm text-gray-500">Welcome back. Please enter your details.</p>

<form method="POST" action="" class="mt-6">
    <?= csrfField() ?>
    <div class="space-y-4">
        <?php
        $id = 'email';
        $fieldName = 'email';
        $type = 'email';
        $label = 'Email';
        $placeholder = 'yourname@example.com';
        $icon = 'fa-regular fa-envelope';
        $autocomplete = 'email';
        $value = $email ?? '';
        $error = $errors['email'] ?? null;
        include __DIR__ . '/../views/components/form-field.php';

        $id = 'password';
        $fieldName = 'password';
        $type = 'password';
        $label = 'Password';
        $placeholder = 'Enter your password';
        $icon = 'fa-solid fa-lock text-xs';
        $autocomplete = 'current-password';
        $value = '';
        $toggle = true;
        $labelExtra = '<a href="#" class="text-xs font-medium text-blue-600 hover:text-blue-500">Forgot password?</a>';
        $error = $errors['password'] ?? null;
        include __DIR__ . '/../views/components/form-field.php';
        ?>
    </div>

    <div class="flex items-center mt-4">
        <input name="remember-me" id="remember-me" type="checkbox" class="h-4 w-4 accent-blue-600 cursor-pointer">
        <label for="remember-me" class="ml-2 block text-sm text-gray-600 select-none">
            Keep me signed in for 30 days
        </label>
    </div>

    <button
        class="mt-6 w-full rounded-lg bg-blue-600 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors cursor-pointer"
        type="submit"
        name="btnSignIn">
        Sign In
    </button>
</form>

<div class="mt-6 text-center text-sm text-gray-500">
    Don't have an account?
    <a href="<?= url('register/index.php') ?>" class="font-semibold text-blue-600 hover:text-blue-500">Sign up</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../views/components/auth_layout.php';
