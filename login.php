<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    $result = mysqli_query($conn, "SELECT * FROM admin_users WHERE username = '$username'");
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Simple password check (in real app, use password_verify)
        if ($password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin.php");
            exit();
        }
    }
    
    $error = "Invalid credentials!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Love Story</title>
    <style>
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
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: rgba(139, 38, 53, 0.3);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            font-size: 2.2em;
            background: linear-gradient(45deg, #ff6b6b, #ff3838);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .login-header p {
            opacity: 0.8;
            font-style: italic;
        }

        .login-form input {
            width: 100%;
            padding: 14px;
            margin: 12px 0;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff5f7;
            font-size: 1em;
            font-family: 'Playfair Display', serif;
            transition: all 0.3s ease;
        }

        .login-form input:focus {
            outline: none;
            border-color: #ff6b6b;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 10px rgba(255, 107, 107, 0.3);
        }

        .login-form input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .login-form button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(45deg, #ff3838, #ff6b6b);
            color: #fff5f7;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 15px;
            font-family: 'Playfair Display', serif;
            font-size: 1.1em;
            transition: all 0.3s ease;
        }

        .login-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 56, 56, 0.4);
        }

        .error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 15px;
            padding: 12px;
            background: rgba(255, 107, 107, 0.2);
            border-radius: 8px;
            border: 1px solid #ff6b6b;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #ffd1dc;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: #ff6b6b;
        }

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
            font-size: 18px;
            opacity: 0.6;
        }
        
        @keyframes floatHearts {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-40px) rotate(180deg); }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="floating-hearts" id="floatingContainer"></div>
    
    <div class="login-container">
        <div class="login-header">
            <h2>🔐 Admin Login</h2>
            <p>Love Story Management</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error">💔 <?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login to Love Portal</button>
        </form>
        
        <div class="back-link">
            <a href="index.php">← Back to Love Story</a>
        </div>
        
        <div style="text-align: center; margin-top: 20px; opacity: 0.7; font-size: 0.9em;">
            <p>Default: admin / admin123</p>
        </div>
    </div>

    <script>
        // Floating hearts for login page
        function createHeart() {
            const heart = document.createElement('div');
            const hearts = ['💖', '💕', '💗'];
            heart.innerHTML = hearts[Math.floor(Math.random() * hearts.length)];
            heart.classList.add('heart');
            heart.style.left = Math.random() * 100 + 'vw';
            heart.style.animationDuration = (Math.random() * 6 + 4) + 's';
            heart.style.fontSize = (Math.random() * 16 + 12) + 'px';
            
            document.getElementById('floatingContainer').appendChild(heart);
            setTimeout(() => heart.remove(), 10000);
        }

        // Create initial floating hearts
        for (let i = 0; i < 10; i++) {
            setTimeout(createHeart, i * 300);
        }
        setInterval(createHeart, 800);
    </script>
</body>
</html>