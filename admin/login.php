<?php
/**
 * VHRC Admin Login Portal
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// Load connection (it auto-creates DB and credentials if missing)
require_once dirname(__DIR__) . '/includes/db_connect.php';

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error_message = "Please enter both username and password.";
    } else {
        try {
            // Find user in database
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];

                // Redirect to dashboard
                header('Location: index.php');
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error_message = "Login failed: " . $e->getMessage();
        }
    }
}

// Include header layout (it knows page is login.php so it bypasses security checks)
include_once __DIR__ . '/includes/header.php';
?>

<div class="login-body">
    <div class="login-card">
        <div class="login-header">
            <img src="../images/logo.png" alt="VHRC Logo" class="login-logo">
            <h2>VHRC Admin Portal</h2>
            <p>Sign in to manage clinical operations</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="login-alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="login-form">
            <div class="login-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" required autocomplete="username">
            </div>

            <div class="login-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required autocomplete="current-password">
            </div>

            <button type="submit" class="login-btn">Log In</button>
        </form>
    </div>
</div>

<?php
// Include footer layout
include_once __DIR__ . '/includes/footer.php';
?>
