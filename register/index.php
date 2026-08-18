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
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">Registration successful. Redirecting...</span>
    </div>
<?php elseif (isset($errors['database'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline"><?= htmlspecialchars($errors['database']) ?></span>
    </div>
<?php endif ?>

<div class="bg-white rounded-lg shadow-xl p-8 mb-0">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-indigo-600">TraceX </h1>
    </div>
    
    <form method="POST" action="index.php">
        <?= csrfField() ?>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Name
            </label>
            <input class="appearance-none border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?= isset($errors['name']) ? 'border-red-500' : '' ?>" 
                id="name" 
                type="text" 
                name="name" 
                placeholder="John Doe"
                value="<?= htmlspecialchars($name ?? '') ?>">
                <?php if (isset($errors['name'])): ?>
                    <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['name']) ?></p>
                <?php endif; ?>
        </div>
            
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                Email
            </label>
            <input class="appearance-none border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?= isset($errors['email']) ? 'border-red-500' : '' ?>"
            id="email" 
            name="email" 
            type="email" 
            placeholder="johndoe123@example.com"
            value="<?= htmlspecialchars($email ?? '') ?>">
            <?php if (isset($errors['email'])): ?>
                <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['email']) ?></p>
            <?php endif; ?>
        </div>
            
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                Password
            </label>
            <input class="appearance-none border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?= isset($errors['password']) ? 'border-red-500' : '' ?>" 
                id="password" 
                type="password" 
                name="password" 
                placeholder="password">
                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
        </div>
            
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">
                Confirm Password
            </label>
            <input class="appearance-none border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?= isset($errors['confirm_password']) ? 'border-red-500' : '' ?>" 
            id="confirm-password" 
            type="password" 
            name="confirm_password" 
            placeholder="Confirm password">
            <?php if (isset($errors['confirm_password'])): ?>
                <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['confirm_password']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="mb-6">
            <div class="flex items-center">
                <input id="terms" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded <?= isset($errors['terms']) ? 'border-red-500' : '' ?>" name="terms">
                <label for="terms" class="ml-2 block text-sm text-gray-700">
                    I agree to the <a href="<?= url('terms-and-conditions.php') ?>" class="text-indigo-600 hover:text-indigo-700 cursor-pointer">Terms & Conditions</a>
                </label>
            </div>
            <?php if (isset($errors['terms'])): ?>
                <p class="text-red-500 text-xs italic mt-1"><?= htmlspecialchars($errors['terms']) ?></p>
            <?php endif; ?>
        </div>
        
        <button 
            type="submit" 
            name="btnSignUp"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-150 cursor-pointer">
            Sign Up
        </button>
    </form>
    
    <div class="mt-6 text-center">
        <p class="text-gray-600 text-sm">
            Already have an account? 
            <a href="<?= url('login/index.php') ?>" class="text-indigo-600 hover:text-indigo-500 font-medium">Sign in</a>
        </p>
    </div>
</div>
<?php include __DIR__ . '/../views/components/copyright.php'; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../views/components/auth_layout.php';
