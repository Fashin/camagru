<?php

  require_once("config/database.php");

  require_once("public/header.php");

  $page[] = "homepage";
  $page[] = array(0, 10);

  require_once("controller/get_pictures.php");

?>

<script type="text/javascript">

  

</script>

<?php

  require_once("public/footer.php");

?>
