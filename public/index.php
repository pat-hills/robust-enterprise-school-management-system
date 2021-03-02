<!DOCTYPE html>

<html lang='en'>
    <head>
        <meta charset="UTF-8" /> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Anidrol Education, +233 24 774 5156"/>
        
        <link rel="icon" type="image/ico" href="../photos/site-icon.png"/>

        <title>Anidrol Education</title>

        <link rel="stylesheet" type="text/css" href="../public/css/style.css" />
    </head>
    <body>
        <div id="wrapper">
            <form name="login-form" class="login-form" action="login.php" method="post">
                <div class="header">
                    <h3>NYARKO-SCIENCE-ACADEMY </h3>
                    <span>Login Form</span>
                </div>

                <div class="content">
                    <input name="username" type="text" class="input" placeholder="Username" autocomplete="off" autofocus />
                    <!--<div class="user-icon"></div>-->
                    <input name="password" type="password" class="input password" placeholder="Password" />
                    <!--<div class="pass-icon"></div>-->		
                </div>

                <div class="footer">
                    <input type="submit" name="submit" value="Login" class="button" />
                    <!--<a href="user_registration.php" class="register" style="text-decoration: none;">Register</a>-->
                </div>
            </form>
        </div>
        <div class="gradient"></div>
    </body>
</html>
