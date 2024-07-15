<?php
use PHPLogin\I18n;

$userrole = 'loginpage';
include 'misc/pagehead.php';
$title = I18n::PAGENAME_LOGIN;
?>
</head>
<body>
  <?php require 'misc/pullnav.php'; ?>
    <div class="container logindiv">
        <div class="col-sm-4"></div>
        <div class="col-sm-4">
            <form class="text-center" name="loginform" method="post" action="ajax/checklogin.php">
                <h3 class="form-signin-heading"><?php echo $title;?></h3>
                <br>
                <div class="form-group">
                    <input name="myusername" id="myusername" type="text" class="form-control input-lg" placeholder="<?php echo I18n::COMMON_KEYWORD_USERNAME ?>" autofocus>
                    <input name="mypassword" id="mypassword" type="password" class="form-control input-lg" placeholder="<?php echo I18n::COMMON_KEYWORD_PASSWORD ?>"> </div>
                <div class="form-group">
                    <button name="Submit" id="submit" class="btn btn-lg btn-primary btn-block" type="submit"><?php echo I18n::COMMON_KEYWORD_LOGIN ?></button>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <input id="remember"  type="checkbox"> <?php echo I18n::COMMON_KEYWORD_REMEMBER ?></input>
                    </div>
                </div>
            </form>
            <div id="message"></div>
            <p class="text-center"><a href="forgotpassword.php"><?php echo I18n::COMMON_KEYWORD_FORGOT_PASSWORD ?>?</a></p>
            <p class="text-center">or <a href="signup.php"><?php echo I18n::COMMON_KEYWORD_CREATE_ACCOUNT ?></a></p>
        </div>
        <div class="col-sm-4"></div>
    </div>
    <!-- The AJAX login script -->
    <script src="js/login.js"></script>
</body>
</html>
