<?php

session_start();

require_once 'includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT user_id, username, password
         FROM users
         WHERE username = ?"
    );

    $stmt->bind_param("s", $username);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];

            header("Location: dashboard.php");
            exit();

        } else {

            $error = "❌ Invalid username or password.";

        }

    } else {

        $error = "❌ Invalid username or password.";

    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Vehicle Pro | Login</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {

    font-family: Arial, Helvetica, sans-serif;

    min-height: 100vh;

    background:
        linear-gradient(135deg, #0b1220, #111827, #172554);

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;
}


/* =========================
   BACKGROUND DECORATION
========================= */

.circle {
    position: fixed;
    border-radius: 50%;
    filter: blur(2px);
    opacity: 0.15;
}

.circle.one {
    width: 400px;
    height: 400px;
    background: #2563eb;
    top: -180px;
    left: -120px;
}

.circle.two {
    width: 350px;
    height: 350px;
    background: #60a5fa;
    bottom: -150px;
    right: -100px;
}


/* =========================
   MAIN CONTAINER
========================= */

.container {

    width: 1000px;
    min-height: 600px;

    background: rgba(255,255,255,0.97);

    border-radius: 25px;

    display: grid;
    grid-template-columns: 1.1fr 0.9fr;

    overflow: hidden;

    box-shadow:
        0 25px 70px rgba(0,0,0,0.5);

    position: relative;
    z-index: 2;
}


/* =========================
   LEFT SIDE
========================= */

.left {

    background:
        linear-gradient(
            rgba(15,23,42,0.85),
            rgba(15,23,42,0.92)
        ),
        linear-gradient(135deg,#1d4ed8,#0f172a);

    color: white;

    padding: 55px;

    display: flex;
    flex-direction: column;
    justify-content: center;

    position: relative;
    overflow: hidden;
}


/* Decorative road */

.left::after {

    content: "";

    position: absolute;

    width: 500px;
    height: 500px;

    border: 2px solid rgba(255,255,255,0.08);

    border-radius: 50%;

    right: -250px;
    bottom: -250px;

}


/* LOGO */

.brand {

    display: flex;
    align-items: center;
    gap: 14px;

    margin-bottom: 35px;
}

.brand-icon {

    width: 55px;
    height: 55px;

    background: #2563eb;

    border-radius: 14px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 30px;

    box-shadow:
        0 8px 25px rgba(37,99,235,0.45);
}

.brand-text h2 {

    font-size: 22px;
    letter-spacing: 1px;

}

.brand-text p {

    font-size: 10px;

    color: #93c5fd;

    letter-spacing: 2px;

    margin-top: 3px;
}

/* =========================
   COMPANY INFORMATION
========================= */

.company-info {
    margin-bottom: 25px;
    padding: 15px 18px;
    background: rgba(37, 99, 235, 0.12);
    border-left: 4px solid #60a5fa;
    border-radius: 10px;
}

.company-info h3 {
    font-size: 17px;
    margin-bottom: 6px;
    color: #ffffff;
}

.company-info p {
    font-size: 12px;
    color: #cbd5e1;
    margin: 5px 0;
    line-height: 1.5;
}

.company-info strong {
    color: #ffffff;
}


/* MAIN TEXT */

.left h1 {

    font-size: 42px;

    line-height: 1.15;

    margin-bottom: 18px;

}

.left h1 span {

    color: #60a5fa;

}

.left-description {

    color: #cbd5e1;

    font-size: 15px;

    line-height: 1.7;

    max-width: 430px;

}


/* FEATURES */

.features {

    margin-top: 35px;

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 15px;

}

.feature {

    display: flex;

    align-items: center;

    gap: 10px;

    color: #e2e8f0;

    font-size: 13px;

}

.feature-icon {

    width: 34px;
    height: 34px;

    border-radius: 9px;

    background: rgba(37,99,235,0.25);

    display: flex;

    align-items: center;
    justify-content: center;

}


/* =========================
   CAR ILLUSTRATION
========================= */

.car {

    position: absolute;

    bottom: 25px;

    right: 45px;

    font-size: 90px;

    opacity: 0.18;

    transform: rotate(-2deg);

}


/* =========================
   RIGHT SIDE
========================= */

.right {

    padding: 65px 60px;

    display: flex;

    flex-direction: column;

    justify-content: center;

    background: white;

}


/* LOGIN TITLE */

.login-title {

    margin-bottom: 35px;

}

.login-title h2 {

    font-size: 29px;

    color: #111827;

    margin-bottom: 8px;

}

.login-title p {

    color: #6b7280;

    font-size: 13px;

}


/* =========================
   INPUT
========================= */

.input-group {

    margin-bottom: 22px;

}

.input-group label {

    display: block;

    color: #374151;

    font-size: 13px;

    font-weight: bold;

    margin-bottom: 8px;

}

.input-wrapper {

    position: relative;

}

.input-wrapper .icon {

    position: absolute;

    left: 15px;

    top: 50%;

    transform: translateY(-50%);

    font-size: 17px;

    color: #6b7280;

}

.input-wrapper input {

    width: 100%;

    height: 50px;

    border: 1px solid #d1d5db;

    border-radius: 10px;

    padding: 0 45px;

    font-size: 14px;

    outline: none;

    transition: 0.3s;

}

.input-wrapper input:focus {

    border-color: #2563eb;

    box-shadow:
        0 0 0 4px rgba(37,99,235,0.10);

}


/* PASSWORD EYE */

.eye {

    position: absolute;

    right: 15px;

    top: 50%;

    transform: translateY(-50%);

    cursor: pointer;

    color: #6b7280;

}


/* =========================
   OPTIONS
========================= */

.options {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;

    font-size: 12px;

}

.remember {

    display: flex;

    gap: 7px;

    align-items: center;

    color: #6b7280;

}

.forgot {

    color: #2563eb;

    text-decoration: none;

    font-weight: bold;

}


/* =========================
   LOGIN BUTTON
========================= */

.login-btn {

    width: 100%;

    height: 52px;

    border: none;

    border-radius: 10px;

    background:
        linear-gradient(135deg,#2563eb,#1d4ed8);

    color: white;

    font-size: 15px;

    font-weight: bold;

    cursor: pointer;

    transition: 0.3s;

    box-shadow:
        0 8px 20px rgba(37,99,235,0.25);

}

.login-btn:hover {

    transform: translateY(-2px);

    box-shadow:
        0 12px 25px rgba(37,99,235,0.35);

}


/* =========================
   SECURITY TEXT
========================= */

.security {

    text-align: center;

    margin-top: 25px;

    color: #9ca3af;

    font-size: 11px;

}

.security span {

    color: #16a34a;

}


/* =========================
   FOOTER
========================= */

.footer {

    text-align: center;

    margin-top: 35px;

    font-size: 10px;

    color: #9ca3af;

}


/* =========================
   RESPONSIVE
========================= */

@media(max-width: 850px) {

    .container {

        width: 92%;

        grid-template-columns: 1fr;

    }

    .left {

        display: none;

    }

    .right {

        padding: 50px 35px;

    }

}

</style>

</head>


<body>


<div class="circle one"></div>

<div class="circle two"></div>


<div class="container">


    <!-- =========================
         LEFT SIDE
    ========================= -->

    <div class="left">


        <div class="brand">

            <div class="brand-icon">
                🚘
            </div>

            <div class="brand-text">

                <h2>SRI AYYAPPA</h2>

                <p>BODY BUILDERS</p>

            </div>
            <div class="company-info">

    <h3>🏢 Sri Ayyappa Body Builders</h3>

    <p>📍 <strong>Location:</strong> Ariyalur, Chennai</p>

    <p>📞 <strong>Phone:</strong> 9444454933</p>

    <p>✉️ <strong>Email:</strong> sriayyappabodybuilders@gmail.com</p>

</div>

        </div>



        <h1>

            Keep Every<br>

            <span>Vehicle Moving.</span>

        </h1>


        <p class="left-description">

            A smart workshop management system designed
            to manage vehicles, customers, employees,
            services, quotations and workshop operations
            in one place.

        </p>


        <div class="features">


            <div class="feature">

                <div class="feature-icon">
                    🚗
                </div>

                Vehicle Management

            </div>


            <div class="feature">

                <div class="feature-icon">
                    🔧
                </div>

                Service Tracking

            </div>


            <div class="feature">

                <div class="feature-icon">
                    👨‍🔧
                </div>

                Employee Management

            </div>


            <div class="feature">

                <div class="feature-icon">
                    📊
                </div>

                Smart Reports

            </div>


        </div>


        <div class="car">
            🚙
        </div>


    </div>



    <!-- =========================
         RIGHT SIDE
    ========================= -->

    <div class="right">


        <div class="login-title">

            <h2>Welcome Back 👋</h2>

            <p>
                Sign in to access your workshop dashboard.
            </p>

        </div>


        <form action="login.php" method="POST">


            <!-- USERNAME -->

            <div class="input-group">

                <label>Username</label>

                <div class="input-wrapper">

                    <span class="icon">👤</span>

                    <input
                        type="text"
                        name="username"
                        placeholder="Enter your username"
                        required
                    >

                </div>

            </div>


            <!-- PASSWORD -->

            <div class="input-group">

                <label>Password</label>

                <div class="input-wrapper">

                    <span class="icon">🔒</span>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                    <span
                        class="eye"
                        onclick="togglePassword()"
                    >
                        👁
                    </span>

                </div>

            </div>


            <!-- OPTIONS -->

            <div class="options">

                <label class="remember">

                    <input type="checkbox">

                    Remember me

                </label>


                <a href="#" class="forgot">
                    Forgot password?
                </a>

            </div>


            <!-- BUTTON -->

            <button
                type="submit"
                class="login-btn"
            >

                🔐 &nbsp; Sign In

            </button>


        </form>


        <div class="security">

            🔒 <span>Secure Login</span>
            &nbsp; • &nbsp;
            Authorized workshop users only

        </div>


        <div class="footer">

            Vehicle Workshop Management System © 2026

        </div>


    </div>


</div>


<script>

function togglePassword() {

    const password =
        document.getElementById("password");

    if (password.type === "password") {

        password.type = "text";

    } else {

        password.type = "password";

    }

}

</script>


</body>

</html>