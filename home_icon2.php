<?php
// Josh's code
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Determine dashboard path based on user type
if (isset($_SESSION['user_type'])) {
    $dashboard_path = "/351delta/" . strtolower($_SESSION['user_type']) . "_dashboard.php";
} else {
    $dashboard_path = "/351delta/login.php"; // fallback if user_type is missing
}
?>

<!-- Home Button -->
<style>
    .home-button {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 8px;
        z-index: 1000;
    }

    .home-button a {
        display: inline-block;
    }

    .home-button img {
        height: 32px;
        width: 32px;
        object-fit: contain;
    }
</style>

<div class="home-button">
    <a href="<?php echo $dashboard_path; ?>">
        <img src="/351delta/home_icon2.png" alt="Home">
    </a>
</div>
