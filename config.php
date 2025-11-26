<?php
session_start();

// Simple file-based storage untuk Vercel
function getMessages() {
    if (!file_exists('messages.json')) {
        file_put_contents('messages.json', '[]');
    }
    return json_decode(file_get_contents('messages.json'), true);
}

function saveMessage($name, $message) {
    $messages = getMessages();
    $messages[] = [
        'name' => htmlspecialchars($name),
        'message' => htmlspecialchars($message),
        'created_at' => date('Y-m-d H:i:s')
    ];
    file_put_contents('messages.json', json_encode($messages));
    return true;
}

function getSlides() {
    // Default love slides
    return [
        [
            'title' => '🌊 The Beginning',
            'content' => 'Yudisthira, from the moment our eyes met, I knew there was something extraordinary about you. You came into my life like a tidal wave of emotions, washing away all the grays and painting my world with vibrant colors...',
            'slide_number' => 1
        ],
        [
            'title' => '💫 Soul Connection',
            'content' => 'I believe our souls recognized each other long before we physically met. Every conversation, every laugh, every glance - it all feels so natural. Like two puzzle pieces finally finding their perfect match...',
            'slide_number' => 2
        ],
        [
            'title' => '🚀 My Inspiration', 
            'content' => 'You are my greatest inspiration, Yudisthira. Watching your strength and passion drives me to become a better man every day. You teach me the true meaning of authenticity and finding beauty in simplicity...',
            'slide_number' => 3
        ],
        [
            'title' => '🌌 Midnight Thoughts',
            'content' => 'In the quiet of the night, my thoughts always wander to you. How did fate bring together two souls that complement each other so perfectly? You are the answer to all my silent prayers...',
            'slide_number' => 4
        ],
        [
            'title' => '🎯 Purpose Found',
            'content' => 'Before you, I was navigating without a compass. You gave me direction, purpose, and a reason to strive for greatness. With you, every challenge becomes an adventure worth taking...',
            'slide_number' => 5
        ],
        [
            'title' => '🔥 Eternal Bond',
            'content' => 'My love for you burns like an eternal flame. The more challenges we face, the stronger it grows. These 4 months are just the beginning of our lifelong journey together...',
            'slide_number' => 6
        ]
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_message'])) {
    $name = $_POST['name'];
    $message = $_POST['message'];
    
    if (!empty($name) && !empty($message)) {
        saveMessage($name, $message);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$slides = getSlides();
$messages = getMessages();
?>
