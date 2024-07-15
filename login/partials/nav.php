    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapsed" aria-expanded="false">
            <span class="glyphicon glyphicon-menu-hamburger"></span>
        </button>

    <?php
    use PHPLogin\I18n;
    //SITE LOGO (IF SET) OR SITE NAME
    if (str_replace(' ', '', $this->mainlogo) == '') {
        //No logo, just renders site name as link
        echo '<ul class="nav navbar-nav navbar-left"><li class="sitetitle"><a class="navbar-brand" href="'.$this->base_url.'">'.$this->site_name.'</a></li></ul>';
    } else {
        //Site main logo as link
        echo '<ul class="nav navbar-nav navbar-left"><li class="mainlogo"><a class="navbar-brand" href="'.$this->base_url.'"><img src="'.$this->mainlogo.'" height="36px"></a></li></ul>';
    }

    ?>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="navbar-collapsed">

    <!-- BOOTSTRAP NAV LINKS GO HERE. USE <li> items with <a> links inside of <ul> -->

    <?php
    // SIGN IN / USER SETTINGS BUTTON
    $auth = new PHPLogin\AuthorizationHandler;


    if (!is_array($barmenu)) {
        // If no menu array is specified as override, try to fallback on menu file
        $menu_file = dirname(__FILE__) . "/barmenu.php";
        if (file_exists($menu_file)) {
            include $menu_file;
        }
    }


    if (is_array($barmenu)) {
        echo '<ul class="nav navbar-nav">';


        foreach ($barmenu as $title => $cfg) {
            $url = $cfg["url"];
            $role = $cfg["role"];
            if ($auth->hasRole($role)) {
                echo "<li><a href=\"" . (PHPLogin\MiscFunctions::isAbsUrl($url) ? $url : $this->base_url . '/' . $url) . "\">$title</a></li>";
            }
        }

        echo "</ul>";
    }

    // Pulls either username or first/last name (if filled out)
    if ($auth->isLoggedIn()) {

        $usr = PHPLogin\ProfileData::pullUserFields($_SESSION['uid'], array('firstname', 'lastname'));
        if ((is_array($usr)) && (array_key_exists('firstname', $usr) && array_key_exists('lastname', $usr))) {
            $user = $usr['firstname']. ' ' .$usr['lastname'];
        } else {
            $user = $_SESSION['username'];
        } ?>

        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <?php echo $user; ?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                    <!-- <li><a href="<?php echo $this->base_url; ?>/user/profileedit.php">Profil</a></li> -->
                    <li><a href="<?php echo $this->base_url; ?>/user/accountedit.php"><?php echo I18n::PAGENAME_ACCOUNT ?></a></li>
                    <li role="separator" class="divider"></li>

                    <!-- Superadmin Controls -->
                    <?php if ($auth->isSuperAdmin()): ?>
                    <li><a href="<?php echo $this->base_url; ?>/admin/config.php"><?php echo I18n::PAGENAME_PAGE_SETTINGS ?></a></li>
                    <li><a href="<?php echo $this->base_url; ?>/admin/permissions.php"><?php echo I18n::PAGENAME_PERMISSIONS ?></a></li>
                    <li role="separator" class="divider"></li>
                    <?php endif; ?>
                    <!-- Admin Controls -->
                    <?php if ($auth->isAdmin()): ?>
                    <li><a href="<?php echo $this->base_url; ?>/admin/users.php"><?php echo I18n::PAGENAME_USER ?></a></li>
                    <li><a href="<?php echo $this->base_url; ?>/admin/roles.php"><?php echo I18n::PAGENAME_ROLE ?></a></li>
                    <li><a href="<?php echo $this->base_url; ?>/admin/mail.php"><?php echo I18n::PAGENAME_MAIL_LOG ?></a></li>
                    <li role="separator" class="divider"></li>
                    <?php endif; ?>

                    <li><a href="<?php echo $this->base_url; ?>/login/logout.php"><?php echo I18n::PAGENAME_LOGOUT ?></a></li>
                </ul>
            </li>
        </ul>

    <?php
    } else {
       //User not logged in?>
        <ul class="nav navbar-nav navbar-right">
        <li class="dropdown"><a href="<?php echo $this->base_url; ?>/login/index.php" role="button" aria-haspopup="false" aria-expanded="false"><?php echo \PHPLogin\I18n::COMMON_KEYWORD_LOGIN ?>
        </a>
        </li>
        </ul>

    <?php
        };

    ?>

    </div><!-- /.navbar-collapse -->
    </div>
    </div>
    </nav>
