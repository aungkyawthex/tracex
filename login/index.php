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
                    $expiry = time() + 60 * 60 * 24; // one day

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
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline"><?= htmlspecialchars($errors['login']) ?></span>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-xl p-8">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-indigo-600">TraceX</h1>
    </div>
    
    <form method="POST" action="">
        <?= csrfField() ?>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                Email
            </label>
            <input class="bg-gray-50 py-2 px-3 border border-gray-300 text-gray-900 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 block w-full <?= isset($errors['email']) ? 'border-red-500' : '' ?>" 
                id="email" 
                type="email" 
                placeholder="yourname@example.com"
                name="email"
                value="<?= htmlspecialchars($email ?? '') ?>">
                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                Password
            </label>
            <input class="bg-gray-50 py-2 px-3 border border-gray-300 text-gray-900 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 block w-full <?= isset($errors['password']) ? 'border-red-500' : '' ?>" 
                id="password" 
                type="password" 
                placeholder="Enter your password"
                name="password">
                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
        </div>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <input name="remember-me" id="remember-me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                    Remember me
                </label>
            </div>
            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">
                Forgot password?
            </a>
        </div>
        <button 
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-150 cursor-pointer" 
            type="submit"
            name="btnSignIn">
            Sign In
        </button>
    </form>
    
    <div class="mt-6 text-center">
        <p class="text-gray-600 text-sm">
            New to TraceX? 
            <a href="<?= url('register/index.php') ?>" class="text-indigo-600 hover:text-indigo-500 font-medium">Sign up</a>
        </p>
    </div>
</div>
<?php include __DIR__ . '/../views/components/copyright.php'; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../views/components/auth_layout.php';
