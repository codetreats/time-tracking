<?php
$title = "Standard User Page";
$userrole = "Standard User"; // Allow only logged in users
include "login/misc/pagehead.php";
?>
</head>
<body>
  <?php require 'login/misc/pullnav.php'; 
  
  
  $stmt = $conf->conn->query("SELECT * FROM roles");
  while ($row = $stmt->fetch()) {
      echo $row['name']."<br />\n";
  }


  
  ?>
    <div class="container">
        <h2><?=$_SESSION["username"]?></h2>
    </div>
</body>
</html>
