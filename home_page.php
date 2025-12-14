<?php

require_once 'Database_connect.php';


$sqlNow = "SELECT * FROM movies ORDER BY movie_id ASC LIMIT 4";
$resultNow = $conn->query($sqlNow);


$nowMovies = $resultNow->fetch_all(MYSQLI_ASSOC);


$sqlSoon = "SELECT * FROM movies ORDER BY movie_id ASC LIMIT 4 OFFSET 4";
$resultSoon = $conn->query($sqlSoon);
$soonMovies = $resultSoon->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_home.css">
    <title>Home Page</title>
</head>
<body>

<header>
    <div class="header-content">
        <h1>BOOK NOW!</h1>
    </div>
    <div class="right-icons">
        <a href="signup.php" class="signup-link">Signup</a> 
        <a href="profile.php" class="profile-link">My Profile</a>
    </div>
</header>

<div class="search-container">
    <form action="details.php" method="get">
    <input type="text" name="search" id="search-bar" placeholder="Search for a movie..." required>
    <button type="submit">Search</button>
</form>
</div>

<main>
    <section id="now-showing">
        <h2>Now Showing!</h2>
        <div class="movie-list">
            <?php foreach ($nowMovies as $movie): ?>
                <div class="movie-item">
                    <a href="details.php?movie_id=<?= $movie['movie_id'] ?>">
                        <div class="poster-wrapper"></div>
                        <img src="images/<?= htmlspecialchars($movie['poster']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                        <h3><?= htmlspecialchars($movie['title']) ?></h3>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="comming-soon">
        <h2>Coming Soon</h2>
        <div class="movie-list">
            <?php foreach ($soonMovies as $movie): ?>
                <div class="movie-item">
                    <a href="details.php?movie_id=<?= $movie['movie_id'] ?>">
                        <div class="poster-wrapper"></div>
                        <img src="images/<?= htmlspecialchars($movie['poster']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                        <h3><?= htmlspecialchars($movie['title']) ?></h3>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<footer>
    <div id="contact-us">
        <h2>Contact Us</h2>
        <p><strong>Email:</strong> cinema.booking@gmail.com</p>
        <p><strong>Phone:</strong> +966 50 988 7375</p>
    </div>
    <p>&copy; 2025 All rights reserved.</p>
</footer>

</body>
</html>