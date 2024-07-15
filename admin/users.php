<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

/**
* Page that allows admins to verify or delete new (unverified) users
**/
$userrole = 'Admin';
$title = 'User Management';
require '../login/misc/pagehead.php';

if (isset($_GET['f'])) {
    $userfunction = $_GET['f'];
} else {
    $userfunction = "manageactive";
}
?>

<script type="text/javascript" src="../login/js/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="../login/js/loadingoverlay.min.js"></script>
<link rel="stylesheet" type="text/css" href="../login/js/DataTables/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="css/usermanagement.css"/>

</head>
<body>
Working
  <?php require '../login/misc/pullnav.php'; ?>
  Working
  <div class="container-fluid">


    <ul class="nav nav-tabs">
      <li class="nav-item <?php if ($userfunction === "manageactive") {
    echo "active";
}; ?>">
        <a class="nav-link" href="?f=manageactive">Manage Active Users</a>
      </li>
      <li class="nav-item <?php if ($userfunction === "verification") {
    echo "active";
}; ?>">
        <a class="nav-link" href="?f=verification">Verify/Delete New Users</a>
      </li>
      <li>

      </li>
    </ul>


    <div class="row">
      <div class="col-sm-12" id="tablecontainer">

      <?php
        switch ($userfunction) {
          case "manageactive":
            include "partials/activeuserstable.php";
            break;
          case "verification":
            include "partials/verifydeleteusers.php";
            break;
          default:
            include "partials/activeuserstable.php";
            break;
        }
      ?>

      </div>

      </div>
    </div>
  </div>


</body>
</html>
