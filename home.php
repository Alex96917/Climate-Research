<?php
// ---- stop the browser caching this page (fixes Back button after logout) ----
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

require __DIR__ . '/config.php';

// Must be logged in
if (!isset($_SESSION["member_id"])) {
    header("Location: login.php");
    exit;
}

// Load this member's details for the profile card
$stmt = $pdo->prepare("SELECT first_name, last_name, email, city, created_at FROM members WHERE id = ?");
$stmt->execute([$_SESSION["member_id"]]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

// Account no longer exists? End the session.
if (!$member) {
    header("Location: logout.php");
    exit;
}

$name     = $member["first_name"] . " " . $member["last_name"];
$first    = $member["first_name"];
$initials = strtoupper(mb_substr($member["first_name"], 0, 1) . mb_substr($member["last_name"], 0, 1));
$since    = date("j M Y", strtotime($member["created_at"]));

$cards = [
    ["about.jpg", "About the Project",
     "This initiative brings together members from across the country to research and identify the causes of climate change in our communities."],
    ["forest.jpg", "Deforestation & Land Use",
     "We study how clearing forests and changing land use affect local temperatures, rainfall and biodiversity."],
    ["emissions.jpg", "Emissions",
     "Greenhouse gases from transport, industry and agriculture are a major driver. We collect data to understand local sources."],
    ["weather.jpg", "Weather Patterns",
     "Members record changes in rainfall and temperature so we can track trends over time."],
    ["waste.jpg", "Waste & Pollution",
     "We look at how waste management and pollution contribute to environmental change and what can be done about it."],
    ["next.jpg", "What Happens Next",
     "Our team will contact you via your registered email and phone number with details on how to take part in data collection."],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Climate Research - Home</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="home">

<header class="navbar">
    <a href="home.php" class="brand">
        <img src="images/logo.png" alt="Climate Research">
    </a>
    <a class="logout" href="logout.php">Logout</a>
</header>

<section class="hero">
    <h2>Welcome, <?= e($name) ?>!</h2>
    <p>Thank you for joining our research team, <?= e($first) ?>. Together we can uncover the causes of climate change.</p>
</section>

<!-- ===== Member profile card ===== -->
<section class="profile">
    <div class="avatar"><?= e($initials) ?></div>

    <div class="profile-main">
        <h3><?= e($name) ?></h3>
        <span class="badge">Research Member</span>
    </div>

    <div class="profile-info">
        <div class="info-item">
            <span class="info-label">Email</span>
            <span class="info-value"><?= e($member["email"]) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">City</span>
            <span class="info-value"><?= e($member["city"]) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Member since</span>
            <span class="info-value"><?= e($since) ?></span>
        </div>
    </div>
</section>

<main class="grid">
    <?php foreach ($cards as $i => [$img, $title, $text]): ?>
        <article class="info-card" style="animation-delay: <?= $i * 0.1 ?>s">
            <div class="card-img">
                <img src="images/<?= e($img) ?>" alt="<?= e($title) ?>" loading="lazy"
                     onerror="this.style.display='none'">
            </div>
            <div class="card-body">
                <h3><?= e($title) ?></h3>
                <p><?= e($text) ?></p>
            </div>
        </article>
    <?php endforeach; ?>
</main>

<!-- ===== Footer ===== -->
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <img src="images/logo-icon.png" alt="">
            <div>
                <strong>Climate Research</strong>
                <span>Identifying the causes of climate change in our country.</span>
            </div>
        </div>

        <div class="footer-links">
            <a href="home.php">Home</a>
            <a href="logout.php">Logout</a>
        </div>

        <div class="footer-contact">
            <span>Questions? Email us at</span>
            <a href="mailto:info@climateresearch.co.ke">info@climateresearch.co.ke</a>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?= date("Y") ?> Climate Research. All rights reserved.
    </div>
</footer>

</body>
</html>