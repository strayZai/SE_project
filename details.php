<?php

$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "cinemaBooking";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$is_search = false;
$search_param = null;
$param_type = null;

if (isset($_GET['movie_id']) && is_numeric($_GET['movie_id'])) {
    $search_param = (int)$_GET['movie_id'];
    $param_type = "i"; 
    if ($search_param <= 0) {
        die('Invalid movie ID.');
    }
} elseif (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = "%" . trim($_GET['search']) . "%"; 
    $search_param = $search_query;
    $param_type = "s"; 
    $is_search = true;
} else {
    die('Movie not found (Missing ID or Search term).');
}

$movie_title        = "";
$movie_description  = "";
$movie_poster_url   = "images/poster_placeholder.jpg";
$movie_duration     = "";
$movie_language     = "";
$genre_name         = "";
$average_rating     = "No ratings yet";
$trailer_embed_url  = "";
$reviews_data       = [];
$movie_id           = 0; 

$sql_movie = "
    SELECT 
        m.movie_id,
        m.title,
        m.description,
        m.duration,
        m.language,
        m.poster,
        m.trailer_id,
        g.genreName,
        AVG(r.score) AS avg_rating
    FROM movies m
    LEFT JOIN Genres g ON m.genre_id = g.genre_id
    LEFT JOIN ratings r ON r.movie_id = m.movie_id
    WHERE " . ($is_search ? "m.title LIKE ?" : "m.movie_id = ?") . " 
    GROUP BY m.movie_id
    LIMIT 1 
";

$stmt = $conn->prepare($sql_movie);

$stmt->bind_param($param_type, $search_param);

$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    
    $movie_id            = $row['movie_id']; 
    $movie_title         = $row['title'];
    $movie_description   = $row['description'];

    if (!empty($row['poster'])) {
        $movie_poster_url = "images/" . $row['poster'];
    }

    if (!empty($row['duration'])) {
        $movie_duration = $row['duration'] . " minutes";
    }

    $movie_language = $row['language'];
    $genre_name     = $row['genreName'];

    if ($row['avg_rating'] !== null) {
        $average_rating = number_format($row['avg_rating'], 1);
    }

    if (!empty($row['trailer_id'])) {
        $trailer_embed_url = "https://www.youtube.com/embed/" . $row['trailer_id'];
    }
} else {
    die('Movie not found.');
}

$stmt->close();

$sql_reviews = "
    SELECT 
        u.name  AS name,
        r.score AS score,
        r.comment
    FROM ratings r
    INNER JOIN users u ON r.user_id = u.user_id
    WHERE r.movie_id = ?
    ORDER BY r.rating_id DESC
";

$stmt = $conn->prepare($sql_reviews);
$stmt->bind_param("i", $movie_id); 
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $reviews_data[] = [
        'name'    => $row['name'],
        'score'   => (int)$row['score'],
        'comment' => $row['comment']
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie_title); ?> - Movie Details</title>
    <link rel="stylesheet" href="movie_light.css">
</head>

<body>

<header class="top-header">
    <h1 class="page-title"><?php echo strtoupper(htmlspecialchars($movie_title)); ?></h1>
</header>

<main class="movie-details">

    <section class="movie-summary">
        <div class="poster">
            <img src="<?php echo htmlspecialchars($movie_poster_url); ?>" 
                 alt="<?php echo htmlspecialchars($movie_title); ?> Poster">
        </div>

        <div class="summary-info">
            <h1><?php echo htmlspecialchars($movie_title); ?></h1>

            <p class="genre-tag"><?php echo htmlspecialchars($genre_name); ?></p>

            <div class="rating-box">
                ⭐ <span><?php echo htmlspecialchars($average_rating); ?></span>
            </div>

            <div class="quick-details">
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($movie_duration); ?></p>
                <p><strong>Language:</strong> <?php echo htmlspecialchars($movie_language); ?></p>
            </div>

            <a href="book.php?movie_id=<?php echo $movie_id; ?>" class="btn btn-primary btn-lg">
                Book Now
            </a>
        </div>
    </section>

    <hr>

    <section class="movie-content">
        <h2>Synopsis</h2>
        <p><?php echo nl2br(htmlspecialchars($movie_description)); ?></p>

        <h2>Trailer</h2>
        <div class="trailer-container">
            <?php if (!empty($trailer_embed_url)): ?>
                <iframe 
                    width="560" 
                    height="315"
                    src="<?php echo htmlspecialchars($trailer_embed_url); ?>"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            <?php else: ?>
                <p>No trailer available.</p>
            <?php endif; ?>
        </div>
    </section>

    <hr>

    <section class="reviews-section">
        <h2>User Reviews</h2>

        <div class="user-reviews-list">
            <?php if (!empty($reviews_data)): ?>
                <?php foreach ($reviews_data as $review): ?>
                    <div class="review-item">
                        <p><strong><?php echo htmlspecialchars($review['name']); ?></strong></p>
                        <p>Score: <?php echo (int)$review['score']; ?>/5</p>
                        <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews yet.</p>
            <?php endif; ?>
        </div>
    </section>

</main>

<footer>
    <p>&copy; 2025 Cinema Booking System</p>
</footer>

</body>
</html>