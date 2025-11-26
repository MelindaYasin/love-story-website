<?php
include 'config.php';

// Process guest message from anyone
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_message'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $sql = "INSERT INTO guest_messages (name, message) VALUES ('$name', '$message')";
    if (mysqli_query($conn, $sql)) {
        $success = "Love message sent successfully! 💖";
    } else {
        $error = "Failed to send message.";
    }
    header("Location: index.php");
    exit();
}

// Get slideshow data
$slides_result = mysqli_query($conn, "SELECT * FROM love_slides WHERE is_active = TRUE ORDER BY slide_number");
$slides = [];
while ($slide = mysqli_fetch_assoc($slides_result)) {
    $slides[] = $slide;
}

// Get guest messages
$messages_result = mysqli_query($conn, "SELECT * FROM guest_messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4 Months of Eternal Love - Yudisthira & Johnax</title>
    <style>
        /* === COMPACT RED ROMANCE THEME === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Playfair Display', 'Georgia', serif;
            background: linear-gradient(135deg, #2c0b0e 0%, #5d1a26 50%, #8b2635 100%);
            color: #fff5f7;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 15px;
        }

        /* HEADER STYLES - COMPACT */
        .header {
            text-align: center;
            padding: 40px 0;
            background: rgba(139, 38, 53, 0.3);
            border-radius: 20px;
            margin-bottom: 30px;
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff6b6b, #ff4757, #ff3838);
        }

        .couple-photo {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ff6b6b;
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 15px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
            background: linear-gradient(45deg, #ff6b6b, #ff3838, #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }

        .single-love {
            font-size: 2.5em;
            margin: 15px 0;
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .subtitle {
            font-size: 1.2em;
            margin-bottom: 8px;
            opacity: 0.95;
        }

        .days-counter {
            font-size: 1em;
            opacity: 0.8;
            font-style: italic;
            color: #ffd1dc;
        }

        /* SLIDESHOW STYLES - COMPACT */
        .slideshow-container {
            position: relative;
            max-width: 800px;
            margin: 30px auto;
            background: rgba(139, 38, 53, 0.25);
            border-radius: 20px;
            padding: 30px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .slide {
            display: none;
            animation: romanticSlide 1.2s ease-out;
            text-align: center;
            padding: 20px;
        }

        .slide.active {
            display: block;
        }

        @keyframes romanticSlide {
            from { 
                opacity: 0; 
                transform: translateY(20px);
            }
            to { 
                opacity: 1; 
                transform: translateY(0);
            }
        }

        .slide-title {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #ffd1dc;
            font-weight: 600;
        }

        .slide-content {
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 25px;
            color: #fff5f7;
            font-style: italic;
        }

        .slide-signature {
            font-size: 1em;
            color: #ff6b6b;
            font-weight: 500;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 15px;
        }

        /* NAVIGATION BUTTONS - COMPACT */
        .prev, .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 107, 107, 0.4);
            color: white;
            border: none;
            padding: 15px;
            cursor: pointer;
            border-radius: 50%;
            font-size: 1.2em;
            transition: all 0.3s ease;
        }

        .prev:hover, .next:hover {
            background: rgba(255, 107, 107, 0.7);
            transform: translateY(-50%) scale(1.1);
        }

        .prev { left: 10px; }
        .next { right: 10px; }

        /* DOTS INDICATOR */
        .dots-container {
            text-align: center;
            margin-top: 20px;
        }

        .dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin: 0 6px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background: #ff3838;
            transform: scale(1.3);
        }

        /* MESSAGE FORM - COMPACT */
        .message-form {
            background: rgba(139, 38, 53, 0.25);
            padding: 25px;
            border-radius: 15px;
            margin: 25px 0;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .message-form h2 {
            color: #ffd1dc;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.5em;
        }

        .message-form input,
        .message-form textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff5f7;
            font-size: 1em;
            font-family: 'Playfair Display', serif;
        }

        .message-form input:focus,
        .message-form textarea:focus {
            outline: none;
            border-color: #ff6b6b;
            background: rgba(255, 255, 255, 0.15);
        }

        .message-form button {
            background: linear-gradient(45deg, #ff3838, #ff6b6b);
            color: #fff5f7;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: all 0.3s ease;
            font-family: 'Playfair Display', serif;
        }

        .message-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 56, 56, 0.4);
        }

        /* GUEST MESSAGES - COMPACT */
        .message-card {
            background: rgba(255, 255, 255, 0.08);
            padding: 20px;
            margin: 15px 0;
            border-radius: 12px;
            border-left: 4px solid #ff6b6b;
            backdrop-filter: blur(10px);
        }

        .message-card h4 {
            color: #ffd1dc;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .message-card p {
            line-height: 1.5;
            margin-bottom: 8px;
            font-size: 1em;
        }

        .message-card small {
            opacity: 0.7;
            font-size: 0.9em;
            color: #ff6b6b;
        }

        /* ADMIN LINK */
        .admin-link {
            text-align: center;
            margin: 25px 0;
        }

        .admin-link a {
            color: #ffd1dc;
            text-decoration: none;
            padding: 10px 20px;
            border: 1px solid #ffd1dc;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.9em;
        }

        .admin-link a:hover {
            background: rgba(255, 209, 220, 0.2);
        }

        /* FLOATING HEARTS */
        .floating-hearts {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .heart {
            position: absolute;
            animation: floatHearts 8s ease-in-out infinite;
            font-size: 20px;
        }
        
        @keyframes floatHearts {
            0%, 100% { 
                transform: translateY(0) rotate(0deg);
                opacity: 0.6;
            }
            50% { 
                transform: translateY(-40px) rotate(180deg);
                opacity: 1;
            }
        }

        /* AUTOPLAY BUTTON */
        .autoplay-btn {
            background: rgba(255, 107, 107, 0.3);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s ease;
            font-family: 'Playfair Display', serif;
        }

        .autoplay-btn:hover {
            background: rgba(255, 107, 107, 0.5);
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .header h1 { font-size: 2em; }
            .couple-photo { width: 140px; height: 140px; }
            .slideshow-container { padding: 20px 15px; }
            .slide-title { font-size: 1.5em; }
            .slide-content { font-size: 1em; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="floating-hearts" id="floatingContainer"></div>
    
    <div class="container">
        <!-- HEADER WITH PHOTO -->
        <div class="header">
            <!-- Replace johnyudis.jpg with your actual photo file -->
            <img src="johnyudis.jpg" alt="Johnax & Yudisthira" class="couple-photo" onerror="this.style.display='none'">
            <h1>4 Months of Eternal Love</h1>
            <div class="single-love">💖</div>
            <p class="subtitle">Yudisthira Judas Wisesa & Johnax Duskblade</p>
            <p class="days-counter">120 Days of Beautiful Memories</p>
        </div>

        <!-- SLIDESHOW -->
        <div class="slideshow-container">
            <?php foreach ($slides as $index => $slide): ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <div class="slide-title"><?php echo $slide['title']; ?></div>
                <div class="slide-content">"<?php echo $slide['content']; ?>"</div>
                <div class="slide-signature">- Johnax, forever yours</div>
            </div>
            <?php endforeach; ?>

            <button class="prev">❮</button>
            <button class="next">❯</button>
        </div>

        <!-- DOTS INDICATOR -->
        <div class="dots-container" id="dotsContainer"></div>

        <!-- AUTOPLAY CONTROL -->
        <div style="text-align: center; margin: 20px 0;">
            <button id="autoplayBtn" class="autoplay-btn">⏸️ Pause Slideshow</button>
        </div>

        <!-- GUEST MESSAGE FORM -->
        <div class="message-form">
            <h2>💌 Send Your Love Message</h2>
            <?php if (isset($success)): ?>
                <div style="color: #ffd1dc; text-align: center; margin-bottom: 15px; padding: 12px; background: rgba(255, 209, 220, 0.2); border-radius: 8px;">
                    💖 <?php echo $success; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div style="color: #ff6b6b; text-align: center; margin-bottom: 15px; padding: 12px; background: rgba(255, 107, 107, 0.2); border-radius: 8px;">
                    💔 <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="name" placeholder="Your Name" required>
                <textarea name="message" rows="4" placeholder="Share your love and blessings for our journey..." required></textarea>
                <button type="submit" name="submit_message">Send Love Message 💘</button>
            </form>
        </div>

        <!-- GUEST MESSAGES -->
        <div class="message-form">
            <h2>💕 Messages from Friends & Family</h2>
            <?php while ($message = mysqli_fetch_assoc($messages_result)): ?>
                <div class="message-card">
                    <h4>👤 <?php echo htmlspecialchars($message['name']); ?></h4>
                    <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                    <small>📅 <?php echo date('M j, Y • g:i A', strtotime($message['created_at'])); ?></small>
                </div>
            <?php endwhile; ?>
            
            <?php if (mysqli_num_rows($messages_result) == 0): ?>
                <div style="text-align: center; opacity: 0.8; padding: 20px;">
                    <p>No messages yet. Be the first to share your love! 🌸</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- ADMIN LINK -->
        <div class="admin-link">
            <a href="login.php">🔐 Admin Panel</a>
        </div>
    </div>

    <script>
        // === SLIDESHOW FUNCTIONALITY ===
        let currentSlide = 0;
        let slides = document.querySelectorAll('.slide');
        let autoplayInterval;
        let isAutoplay = true;

        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            currentSlide = (n + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            updateDots();
        }

        function nextSlide() { showSlide(currentSlide + 1); }
        function prevSlide() { showSlide(currentSlide - 1); }

        function updateDots() {
            let dots = document.querySelectorAll('.dot');
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        function createDots() {
            let dotsContainer = document.getElementById('dotsContainer');
            dotsContainer.innerHTML = '';
            for (let i = 0; i < slides.length; i++) {
                let dot = document.createElement('span');
                dot.className = 'dot' + (i === 0 ? ' active' : '');
                dot.addEventListener('click', () => showSlide(i));
                dotsContainer.appendChild(dot);
            }
        }

        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, 5000);
        }

        function toggleAutoplay() {
            isAutoplay = !isAutoplay;
            const btn = document.getElementById('autoplayBtn');
            if (isAutoplay) {
                startAutoplay();
                btn.textContent = '⏸️ Pause Slideshow';
            } else {
                clearInterval(autoplayInterval);
                btn.textContent = '▶️ Continue Slideshow';
            }
        }

        // === FLOATING HEARTS ===
        function createHeart() {
            const heart = document.createElement('div');
            const hearts = ['💖', '💕', '💗', '💓', '💘'];
            heart.innerHTML = hearts[Math.floor(Math.random() * hearts.length)];
            heart.classList.add('heart');
            heart.style.left = Math.random() * 100 + 'vw';
            heart.style.animationDuration = (Math.random() * 6 + 4) + 's';
            heart.style.fontSize = (Math.random() * 18 + 12) + 'px';
            heart.style.opacity = Math.random() * 0.5 + 0.3;
            
            document.getElementById('floatingContainer').appendChild(heart);
            setTimeout(() => heart.remove(), 10000);
        }

        // === EVENT LISTENERS ===
        document.querySelector('.next').addEventListener('click', nextSlide);
        document.querySelector('.prev').addEventListener('click', prevSlide);
        document.getElementById('autoplayBtn').addEventListener('click', toggleAutoplay);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') nextSlide();
            if (e.key === 'ArrowLeft') prevSlide();
            if (e.key === ' ') toggleAutoplay();
        });

        // === INITIALIZE ===
        createDots();
        startAutoplay();
        setInterval(createHeart, 600);

        // Create initial floating hearts
        for (let i = 0; i < 15; i++) {
            setTimeout(createHeart, i * 200);
        }

        // Fallback if photo doesn't load
        const photo = document.querySelector('.couple-photo');
        photo.onerror = function() {
            this.style.display = 'none';
        };
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>