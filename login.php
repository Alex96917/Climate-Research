<?php
require __DIR__ . '/config.php';

// Already logged in? Go straight to the homepage.
if (isset($_SESSION["member_id"])) {
    header("Location: home.php");
    exit;
}

$errors = [];
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!csrf_verify()) {
        $errors[] = "Security check failed. Please refresh the page and try again.";
    } elseif ($email === "" || $password === "") {
        $errors[] = "Enter both email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, password_hash FROM members WHERE email = ?");
        $stmt->execute([$email]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($member && password_verify($password, $member["password_hash"])) {
            session_regenerate_id(true);
            unset($_SESSION["csrf_token"]);   // new token after login
            $_SESSION["member_id"]   = $member["id"];
            $_SESSION["member_name"] = $member["first_name"] . " " . $member["last_name"];
            header("Location: home.php");
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Member Login</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth">

    <section class="auth-left">
        <h2>WELCOME BACK</h2>
        <div class="sub">To Climate Research</div>
        <p>Log in to access the research homepage and see the latest information about the project.</p>
        <p>Not a member yet? Register and join the team.</p>
    </section>

    <section class="auth-right">
        <div class="auth-form">
            <h1>Member Login</h1>
            <p class="tagline">Enter your email and password to continue.</p>

            <?php if (isset($_GET["registered"])): ?>
                <div class="alert success">Registration successful! Please log in.</div>
            <?php endif; ?>
            <?php if (isset($_GET["loggedout"])): ?>
                <div class="alert success">You have been logged out.</div>
            <?php endif; ?>
            <?php if ($errors): ?>
                <div class="alert error">
                    <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php" novalidate>
                <?= csrf_field() ?>

                <label><span class="sr-only">Email Address</span>
                    <input type="email" name="email" placeholder="Email address" autocomplete="email" value="<?= e($email) ?>" required>
                </label>

                <label><span class="sr-only">Password</span>
                    <div class="pw-wrap">
                        <input type="password" name="password" placeholder="Password" autocomplete="current-password" required>
                        <button type="button" class="toggle-pw" aria-label="Show or hide password">👁</button>
                    </div>
                </label>

                <button type="submit">Log In</button>
                <p class="link">Not registered yet? <a href="register.php">Create an account</a></p>
            </form>
        </div>
    </section>

</div>
<script src="script.js"></script>
</body>
</html>