<?php
$title = "Details";
$userrole = "Accountant"; // Allow only admins to access this page
include "login/misc/pagehead.php";
?>
</head>
<body>
  <?php require 'login/misc/pullnav.php'; 
  
  $stmt = $conf->conn->query("SELECT * FROM roles");
  while ($row = $stmt->fetch()) {
      echo $row['name']."<br />\n";
  }

  echo "i18n:" . $i18n["PAGENAME_ACCOUNT"];

  ?>
    <div class="container">

        <h2>Arbeitszeiten</h2>
       
    </div>
</body>
</html>
