<?php
include 'config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Handle actions
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete_message' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        mysqli_query($conn, "DELETE FROM guest_messages WHERE id = $id");
    }
    elseif ($_GET['action'] == 'delete_slide' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        mysqli_query($conn, "DELETE FROM love_slides WHERE id = $id");
    }
    header("Location: admin.php");
    exit();
}

// Get data for admin
$slides_result = mysqli_query($conn, "SELECT * FROM love_slides ORDER BY slide_number");
$messages_result = mysqli_query($conn, "SELECT * FROM guest_messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Love Story</title>
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
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-header {
            background: rgba(139, 38, 53, 0.4);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .admin-header h1 {
            font-size: 2.2em;
            background: linear-gradient(45deg, #ff6b6b, #ff3838);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
        }

        .section {
            background: rgba(139, 38, 53, 0.25);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .section h2 {
            color: #ffd1dc;
            margin-bottom: 20px;
            font-size: 1.6em;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(255, 107, 107, 0.3);
            color: #ffd1dc;
            font-weight: 600;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-family: 'Playfair Display', serif;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .btn-danger {
            background: linear-gradient(45deg, #ff3838, #ff6b6b);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 56, 56, 0.4);
        }

        .btn-primary {
            background: rgba(255, 255, 255, 0.2);
            color: #ffd1dc;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-primary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .logout {
            background: rgba(255, 209, 220, 0.2);
            color: #ffd1dc;
            border: 1px solid #ffd1dc;
        }

        .logout:hover {
            background: rgba(255, 209, 220, 0.3);
        }

        .header-actions {
            float: right;
        }

        .stats {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            flex: 1;
        }

        .stat-number {
            font-size: 2em;
            color: #ff6b6b;
            font-weight: bold;
        }

        .stat-label {
            font-size: 0.9em;
            opacity: 0.8;
        }

        .empty-message {
            text-align: center;
            padding: 40px;
            opacity: 0.7;
            font-style: italic;
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
            font-size: 16px;
            opacity: 0.4;
        }
        
        @keyframes floatHearts {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="floating-hearts" id="floatingContainer"></div>
    
    <div class="admin-container">
        <div class="admin-header">
            <h1>🔧 Love Story Admin Panel</h1>
            <div class="header-actions">
                <a href="index.php" class="btn btn-primary">🏠 View Website</a>
                <a href="?logout" class="btn logout">🚪 Logout</a>
            </div>
            
            <?php
            $total_messages = mysqli_num_rows($messages_result);
            $total_slides = mysqli_num_rows($slides_result);
            mysqli_data_seek($messages_result, 0);
            mysqli_data_seek($slides_result, 0);
            ?>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_slides; ?></div>
                    <div class="stat-label">Love Slides</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_messages; ?></div>
                    <div class="stat-label">Guest Messages</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>📊 Love Slides Management</h2>
            <?php if (mysqli_num_rows($slides_result) > 0): ?>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Content Preview</th>
                    <th>Slide #</th>
                    <th>Actions</th>
                </tr>
                <?php while ($slide = mysqli_fetch_assoc($slides_result)): ?>
                <tr>
                    <td><strong><?php echo $slide['title']; ?></strong></td>
                    <td><?php echo substr($slide['content'], 0, 80) . '...'; ?></td>
                    <td><?php echo $slide['slide_number']; ?></td>
                    <td>
                        <a href="?action=delete_slide&id=<?php echo $slide['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this love slide?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
                <div class="empty-message">No love slides found. Add some romantic messages! 💖</div>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>💬 Guest Love Messages</h2>
            <?php if (mysqli_num_rows($messages_result) > 0): ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Message Preview</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                <?php while ($message = mysqli_fetch_assoc($messages_result)): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($message['name']); ?></strong></td>
                    <td><?php echo substr(htmlspecialchars($message['message']), 0, 60) . '...'; ?></td>
                    <td><?php echo date('M j, Y', strtotime($message['created_at'])); ?></td>
                    <td>
                        <a href="?action=delete_message&id=<?php echo $message['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this love message?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
                <div class="empty-message">No guest messages yet. Share your love story with friends! 💕</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Floating hearts for admin panel
        function createHeart() {
            const heart = document.createElement('div');
            const hearts = ['💖', '💕', '💗'];
            heart.innerHTML = hearts[Math.floor(Math.random() * hearts.length)];
            heart.classList.add('heart');
            heart.style.left = Math.random() * 100 + 'vw';
            heart.style.animationDuration = (Math.random() * 6 + 4) + 's';
            heart.style.fontSize = (Math.random() * 14 + 10) + 'px';
            
            document.getElementById('floatingContainer').appendChild(heart);
            setTimeout(() => heart.remove(), 10000);
        }

        // Create initial floating hearts
        for (let i = 0; i < 8; i++) {
            setTimeout(createHeart, i * 400);
        }
        setInterval(createHeart, 1000);
    </script>
</body>
</html>
<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
mysqli_close($conn);
?>