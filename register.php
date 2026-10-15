<?php
require __DIR__ . '/config.php';

$cities = ["Nairobi", "Mombasa", "Kisumu", "Nakuru", "Eldoret"];
$errors = [];
$old = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old = [
        "first_name" => trim($_POST["first_name"] ?? ""),
        "last_name"  => trim($_POST["last_name"] ?? ""),
        "email"      => trim($_POST["email"] ?? ""),
        "phone"      => trim($_POST["phone"] ?? ""),
        "gender"     => $_POST["gender"] ?? "",
        "age"        => trim($_POST["age"] ?? ""),
        "city"       => $_POST["city"] ?? "",
    ];
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";

    // 1. CSRF check first
    if (!csrf_verify()) {
        $errors[] = "Security check failed. Please refresh the page and try again.";
    } else {
        // 2. Validation
        if ($old["first_name"] === "") $errors[] = "First name is required.";
        if ($old["last_name"] === "")  $errors[] = "Last name is required.";
        if (!filter_var($old["email"], FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email address.";
        if (!preg_match('/^\+?[0-9]{9,15}$/', $old["phone"])) $errors[] = "Enter a valid phone number (9-15 digits).";
        if (!in_array($old["gender"], ["Male", "Female", "Other"])) $errors[] = "Select a gender.";
        if (!ctype_digit($old["age"]) || $old["age"] < 18 || $old["age"] > 100) $errors[] = "Age must be between 18 and 100.";
        if (!in_array($old["city"], $cities)) $errors[] = "Select a city.";
        if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
        if ($password !== $confirm) $errors[] = "Passwords do not match.";

        // 3. Duplicate email check
        if (!$errors) {
            $stmt = $pdo->prepare("SELECT id FROM members WHERE email = ?");
            $stmt->execute([$old["email"]]);
            if ($stmt->fetch()) $errors[] = "That email is already registered.";
        }

        // 4. Save
        if (!$errors) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO members (first_name, last_name, email, phone, gender, age, city, password_hash)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $old["first_name"], $old["last_name"], $old["email"], $old["phone"],
                $old["gender"], $old["age"], $old["city"], $hash
            ]);
            unset($_SESSION["csrf_token"]);   // fresh token for the next form
            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Climate Research Registration</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth">

    <section class="auth-left">
        <h2>WELCOME</h2>
        <div class="sub">To Climate Research</div>
        <p>We are a newly launched company researching the causes of climate change across the country.</p>
        <p>Register to become a research member and be part of the movement.</p>
    </section>

    <section class="auth-right">
        <div class="auth-form">
            <h1>Climate Research Registration</h1>
            <p class="tagline">Fill in the form to get onboard as one of our research members.</p>

            <?php if ($errors): ?>
                <div class="alert error">
                    <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form id="register-form" method="post" action="register.php" novalidate>
                <?= csrf_field() ?>

                <div class="row">
                    <label><span class="sr-only">First Name</span>
                        <input type="text" name="first_name" placeholder="First name" autocomplete="given-name" value="<?= e($old['first_name'] ?? '') ?>" required>
                        <small class="hint" data-for="first_name"></small>
                    </label>
                    <label><span class="sr-only">Last Name</span>
                        <input type="text" name="last_name" placeholder="Last name" autocomplete="family-name" value="<?= e($old['last_name'] ?? '') ?>" required>
                        <small class="hint" data-for="last_name"></small>
                    </label>
                </div>

                <label><span class="sr-only">Email Address</span>
                    <input type="email" name="email" placeholder="Email address" autocomplete="email" value="<?= e($old['email'] ?? '') ?>" required>
                    <small class="hint" data-for="email"></small>
                </label>

                <label><span class="sr-only">Phone Number</span>
                    <input type="tel" name="phone" placeholder="Phone number" autocomplete="tel" value="<?= e($old['phone'] ?? '') ?>" required>
                    <small class="hint" data-for="phone"></small>
                </label>

                <fieldset>
                    <legend>Gender</legend>
                    <?php foreach (["Male", "Female", "Other"] as $g): ?>
                        <label class="inline">
                            <input type="radio" name="gender" value="<?= $g ?>"
                                <?= ($old['gender'] ?? '') === $g ? 'checked' : '' ?>> <?= $g ?>
                        </label>
                    <?php endforeach; ?>
                    <small class="hint" data-for="gender"></small>
                </fieldset>

                <div class="row">
                    <label><span class="sr-only">Age</span>
                        <input type="number" name="age" min="18" max="100" placeholder="Age" value="<?= e($old['age'] ?? '') ?>" required>
                        <small class="hint" data-for="age"></small>
                    </label>
                    <label><span class="sr-only">Residential City</span>
                        <select name="city" required>
                            <option value="">City</option>
                            <?php foreach ($cities as $c): ?>
                                <option value="<?= $c ?>" <?= ($old['city'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="hint" data-for="city"></small>
                    </label>
                </div>

                <label><span class="sr-only">Password</span>
                    <div class="pw-wrap">
                        <input type="password" name="password" placeholder="Password" autocomplete="new-password" required>
                        <button type="button" class="toggle-pw" aria-label="Show or hide password">👁</button>
                    </div>
                    <div class="strength"><span></span></div>
                    <small class="strength-text"></small>
                    <small class="hint" data-for="password"></small>
                </label>

                <label><span class="sr-only">Confirm Password</span>
                    <div class="pw-wrap">
                        <input type="password" name="confirm_password" placeholder="Confirm password" autocomplete="new-password" required>
                        <button type="button" class="toggle-pw" aria-label="Show or hide password">👁</button>
                    </div>
                    <small class="hint" data-for="confirm_password"></small>
                </label>

                <button type="submit">Register</button>
                <p class="link">Already registered? <a href="login.php">Log in</a></p>
            </form>
        </div>
    </section>

</div>
<script src="script.js"></script>
</body>
</html>