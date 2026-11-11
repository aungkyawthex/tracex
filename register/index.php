<?php
    include __DIR__ . '/../src/helpers/url.php';
    include __DIR__ . '/../src/bootstrap.php';
    require_once __DIR__ . '/../src/helpers/isGuest.php';
    require_once __DIR__ . '/../src/helpers/csrf.php';

    $title = "Sign Up - TraceX";
    ob_start();
    $errors = [];
    $success = false;

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSignUp'])){
        verifyCsrf();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $agreeTerms = isset($_POST['terms']) ? true : false;

        // validate inputs
        if (empty($name)) {
            $errors['name'] = '*name is required and cannot be empty.';
        }

        if (empty($email)) {
            $errors['email'] = '*email is required and cannot be empty.';
        } 
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } 
        else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $errors['email'] = 'This email is already registered';
            }
        }

        if(empty($password)) {
            $errors['password'] = '*password is required and cannot be empty';
        } 
        elseif(strlen($password) < 8) {
            $errors['password'] = '*password must be at least 8 characters long';
        }

        if (!empty($password) && !empty($confirmPassword) && $confirmPassword !== $password) {
            $errors['password'] = 'Passwords do not match';
        }

        if($name && $email && $password && $confirmPassword && !$agreeTerms) {
            $errors['terms'] = 'You must agree to the terms and conditions';
        }

        // register
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare("INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $name,
                    $email,
                    $hashedPassword,
                    'user'
                ]);

                $success = true;
                session_regenerate_id(true);
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $name;

                header("Location: ../public/index.php");
                exit();
            } catch (PDOException $e) {
                logThrowable($e, 'registration failed');

                if ($e->getCode() === '23000') {
                    $errors['email'] = 'This email is already registered';
                } else {
                    $errors['database'] = 'Registration failed. Please try again';
                }
            }
        }
    }
?>
<?php if($success): ?>
    <?php $type = 'success'; $message = 'Registration successful. Redirecting...'; include __DIR__ . '/../views/components/alert.php'; ?>
<?php elseif (isset($errors['database'])): ?>
    <?php $type = 'error'; $message = $errors['database']; include __DIR__ . '/../views/components/alert.php'; ?>
<?php endif ?>

<h1 class="text-xl font-bold text-gray-900">Create your account</h1>
<p class="mt-1 text-sm text-gray-500">Start tracking your expenses today.</p>

<form method="POST" action="" class="mt-6">
    <?= csrfField() ?>
    <div class="space-y-4">
        <?php
        $id = 'name';
        $fieldName = 'name';
        $type = 'text';
        $label = 'Full name';
        $placeholder = 'John Doe';
        $icon = 'fa-regular fa-user';
        $autocomplete = 'name';
        $value = $name ?? '';
        $error = $errors['name'] ?? null;
        include __DIR__ . '/../views/components/form-field.php';

        $id = 'email';
        $fieldName = 'email';
        $type = 'email';
        $label = 'Email';
        $placeholder = 'johndoe123@example.com';
        $icon = 'fa-regular fa-envelope';
        $autocomplete = 'email';
        $value = $email ?? '';
        $error = $errors['email'] ?? null;
        include __DIR__ . '/../views/components/form-field.php';

        $id = 'password';
        $fieldName = 'password';
        $type = 'password';
        $label = 'Password';
        $placeholder = 'At least 8 characters';
        $icon = 'fa-solid fa-lock text-xs';
        $autocomplete = 'new-password';
        $minlength = 8;
        $value = '';
        $toggle = true;
        $error = $errors['password'] ?? null;
        include __DIR__ . '/../views/components/form-field.php';

        $id = 'confirm-password';
        $fieldName = 'confirm_password';
        $type = 'password';
        $label = 'Confirm password';
        $placeholder = 'Re-enter your password';
        $icon = 'fa-solid fa-lock text-xs';
        $autocomplete = 'new-password';
        $minlength = null;
        $value = '';
        $toggle = true;
        $error = $errors['confirm_password'] ?? null;
        include __DIR__ . '/../views/components/form-field.php';
        ?>
    </div>

    <div class="mt-4">
        <div class="flex items-start">
            <input id="terms" type="checkbox" name="terms" required class="h-4 w-4 mt-0.5 accent-blue-600 cursor-pointer">
            <label for="terms" class="ml-2 block text-sm text-gray-600 select-none">
                I agree to the <a href="<?= url('terms-and-conditions.php') ?>" target="_blank" class="font-semibold text-blue-600 hover:text-blue-500">Terms &amp; Conditions</a>
            </label>
        </div>
        <?php if (isset($errors['terms'])): ?>
            <p class="text-red-600 text-xs mt-1"><?= htmlspecialchars($errors['terms']) ?></p>
        <?php endif; ?>
    </div>

    <button
        type="submit"
        name="btnSignUp"
        class="mt-6 w-full rounded-lg bg-blue-600 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors cursor-pointer">
        Create account
    </button>
</form>

<div class="mt-6 text-center text-sm text-gray-500">
    Already have an account?
    <a href="<?= url('login/index.php') ?>" class="font-semibold text-blue-600 hover:text-blue-500">Sign in</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../views/components/auth_layout.php';
