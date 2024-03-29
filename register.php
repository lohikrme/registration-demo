<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/register.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <title>Document</title>
</head>
<body id="login">
    
<header>

    <div class="header-left">
        <img id="logo" src="images/logo.png">
    </div>
    <div class="header-right">
        <h1>Rome Clan Website</h1>
        <ul class="menu-items-list">
            <li id="main-button"><a href="index.php">Main</a></li>
            <li id="events-button"><a href="events.php">Events</a></li>
            <li id="forum-button"><a href="forum.php">Forum</a></li>
            <?php createLinkToOwnInformation(); ?> <!-- this script creates a link to users own information -->
            <?php createLogInButton(); ?> <!-- this script creates list-item that changes depending logged in or out -->
        </ul>
    </div>

</header>

<form class="registerForm" action="scripts/registration.php" method="post" onsubmit="return checkPasswords()">
    <h2>Register here! </h2>

    <p class="registerTexts">Email Address: </p>

    <div>
        <label><i class="fas fa-envelope"></i></label>
        <input class="inputs" id="email-address-input" name="email" type="email" placeholder="warband.player@gmail.com"> 
    </div>

    <p class="registerTexts">Account name: </p>

    <div>
        <label><i class="fas fa-user"></i></label>
        <input class="inputs" id="account-name-input" name="name" type="text" placeholder="Totenflak"> 
    </div>

    <p class="registerTexts">Password: </p>
    
    <div>
        <label><i class="fas fa-lock"></i></label>
        <input class="inputs" id="password1-input" name="password" type="password" placeholder="12345" onkeyup="checkPasswords()"> 
    </div>

    <p class="registerTexts">Write password again: </p>

    <div>
        <label><i class="fas fa-lock"></i></label>
        <input class="inputs" id="password2-input" type="password" placeholder="12345" onkeyup="checkPasswords()"> 
    </div>

    <p id="captcha-text" class="registerTexts">Before you are registered, write the following code in the last input field: </p>

    <img id="captcha-image" src="scripts/captcha-B.php" alt="CAPTCHA">
    <br>
    <input id="captcha-input" type="text" placeholder="1A2B3C" name="captcha-code">
    <br>

    <button class="buttons1" id="register-button" type="submit">Register</button>
    <button class="buttons1" id="clear-button" type="reset">Clear</button> <br>
    <a class="buttons2" id="return-login-button" href="login.php">Back to login? </a>
</form> <!-- loginForm ends -->



<!-- next script compares that passwords are similar between each others and enough long and complex-->
<!-- adding color hints to users so they know to fix their passwords until they are ok-->
<!-- also the script uses Ajax to stay in the current register.php and only send data to php files-->
<script>
    function checkPasswords() {
        var password1IsValid = false;
        var passwordsAreValid = false;

        var password1 = document.getElementById("password1-input");
        var password2 = document.getElementById("password2-input");

        console.log(password1.value);
        console.log(password2.value);

        if (password1.value == "" && password2.value == "") {
            password1.style.backgroundColor = "transparent";
            password2.style.backgroundColor = "transparent";
        }

        else if (password1.value.length >= 10 && password1.value.match(/[a-z]/) && password1.value.match(/[A-Z]/) && password1.value.match(/[0-9]/)) {
                password1.style.backgroundColor = "green";
                password1IsValid = true;
        } 
        else {
                password1.style.backgroundColor = "red";
                password1IsValid = false;
        }

        if (password1IsValid && password1.value === password2.value) {
            password2.style.backgroundColor = "green";
            passwordsAreValid = true;
        }
        else if (password1IsValid && password1.value !== password2.value) {
            password2.style.backgroundColor = "red";
            passwordsAreValid = false;
        }

        return passwordsAreValid;
    }

    // because we do not want user to open a php file, we dont write here 'return passwordsAreValid'
    // instead we use js ajax to send and receive data to and from the php file.
    // php will send a status to js script, and js uses this server status to alert user.
    // 201 = registration has been succesfull.
    // 401-407 = there is a problem with registration due to inputs or server issue.
    document.getElementById('register-button').addEventListener('click', function(event) {
        if (checkPasswords()) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'scripts/registration-B.php', true);
            var formData = new FormData(document.querySelector('.registerForm'));
            xhr.send(formData);

            xhr.onload = function() {
                // if php file answers that received form is good and user has been added to the database:
                if (xhr.status == 201) {
                    window.location.href = "registration-successful.php";
                } 
                // if there are a variety of problems with user inputs
                else if (xhr.status == 401) {
                    alert("NAME is empty or contains banned special letters. Only allowed special letters for name are dot(.) underline(_) and line(-).");
                }
                else if (xhr.status == 402) {
                    alert("This NAME has already been taken!");
                }
                else if (xhr.status == 403) {
                    alert("EMAIL ADDRESS is empty or contains banned special letters. Only allowed special letters for email are dot(.) underline(_) line(-) and at(@).");
                }
                else if (xhr.status == 404) {
                    alert("This EMAIL ADDRESS has already been taken!");
                }
                else if (xhr.status == 405) {
                    alert("Your password is not following rules. Passwords must be at least length of 10 and they must contain at least 1 number, 1 small letter and 1 big letter.");
                }
                else if (xhr.status == 406) {
                    alert("You have given an incorrect CAPTCHA code.");
                }
                // if the problem is not with user inputs but with server, such as adding user to the database
                else if (xhr.status == 407) {
                    alert("There is a problem with registration due to server issues. Please try again later! But if the problem persists, contact support at 'rome.lh.bl@gmail.com'.");
                }
            }
        }

        // prevent the default way of sending form with POST protocol to avoid opening the php file
        event.preventDefault();
    });
</script>



<!-- Next php script will transform login button to logout button if user has been logged in-->
<?php
function createLogInButton() {
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

    // check if user is logged in (has SESSION variable called user_id):
    if (isset($_SESSION['user_id'])) { 
        // if user is logged in, login button reads log out! and clicking it logs user out
        echo '<li id="login-button"><a href="logged-out.php">Log out!</a></li>';
    } else {
        // user is not logged in, so use the original login-button, which already exists in html code
        echo '<li id="login-button"><a href="login.php">Log in!</a></li>';
    }
}
?>

<!-- Next php script creates an icon user can press to get to see and change their own information -->
<?php
function createLinkToOwnInformation() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['user_id'])) {
        echo "<a id='own-info-button' href='../user/own-information.php'><i class='fas fa-user'></i>Own Information</a>";
    }
}
?>

</body>
</html>