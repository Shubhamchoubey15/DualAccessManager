<?php
include 'db_connect.php';

session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Login
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            $message = "Login successful! Redirecting...";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2000);
                  </script>";
        } else {
            $message = "Invalid username or password.";
        }
        $stmt->close();
    } elseif (isset($_POST['register'])) {
        // Registration
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            $message = "Registration successful! Please log in.";
            echo "<script>
                    setTimeout(function() {
                        document.getElementById('registerModal').style.display = 'none';
                        document.getElementById('loginModal').style.display = 'flex';
                    }, 2000);
                  </script>";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Register</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            background: linear-gradient(115deg, #020024, #090979, #00d4ff);
            font-family: Arial, sans-serif;
            color: #fff;
            perspective: 1000px; /* Perspective to enhance 3D effect */
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .starry-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            transform-style: preserve-3d; /* Preserve 3D transformations */
        }

        .star {
            position: absolute;
            border-radius: 50%;
            background: #fff;
            opacity: 0.8;
            animation: star-move linear infinite;
        }

        @keyframes star-move {
            0% {
                transform: translate3d(0, 0, -1000px) scale(0.5); /* Start far away */
                opacity: 1;
            }
            100% {
                transform: translate3d(100vw, 100vh, 1000px) scale(5); /* Move towards the viewer from the sides */
                opacity: 0; /* Fade out as it gets closer */
            }
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            position: relative;
            color: #333;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error,
        .success {
            margin-top: 10px;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }

        .link {
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }
    </style>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const starField = document.querySelector('.starry-background');
            const numberOfStars = 150; // Number of stars for effect

            for (let i = 0; i < numberOfStars; i++) {
                const star = document.createElement('div');
                star.classList.add('star');
                const size = Math.random() * 3 + 1 + 'px'; // Random size between 1px and 4px
                star.style.width = size;
                star.style.height = size;
                
                // Set initial position around the edges of the screen
                const startX = Math.random() > 0.5 ? Math.random() * 100 + 'vw' : -Math.random() * 100 + 'vw';
                const startY = Math.random() > 0.5 ? Math.random() * 100 + 'vh' : -Math.random() * 100 + 'vh';
                
                star.style.top = startY;
                star.style.left = startX;
                star.style.transform = `translate3d(${Math.random() * -2000}px, ${Math.random() * -2000}px, ${Math.random() * -1500}px)`; // Start further back in 3D space
                star.style.animationDuration = Math.random() * 5 + 2 + 's'; // Duration between 2s and 7s
                star.style.animationDelay = Math.random() * 2 + 's'; // Delay between 0s and 2s
                starField.appendChild(star);
            }
        });

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function toggleModal(showModalId, hideModalId) {
            document.getElementById(showModalId).style.display = 'flex';
            document.getElementById(hideModalId).style.display = 'none';
        }

        // Show login modal by default
        window.onload = function() {
            openModal('loginModal');
        }
    </script>
</head>
<body>
    <div class="starry-background"></div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <h2>Login</h2>
            <?php if ($message): ?>
                <div class="<?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="login.php">
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="submit" name="login" value="Login">
            </form>
            <a href="#" class="link" onclick="toggleModal('registerModal', 'loginModal')">Create New Account</a>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <h2>Register</h2>
            <?php if ($message): ?>
                <div class="<?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php else: ?>
                <form method="POST" action="login.php">
                    <input type="text" name="username" placeholder="Username" required><br>
                    <input type="email" name="email" placeholder="Email" required><br>
                    <input type="password" name="password" placeholder="Password" required><br>
                    <input type="submit" name="register" value="Register">
                </form>
                
                <a href="#" class="link" onclick="toggleModal('loginModal', 'registerModal')">Back to Login</a>
            <?php endif; ?>
        </div>
    </div>
    
</body>
</html>
