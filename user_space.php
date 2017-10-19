<?php

  require_once("config/database.php");

  if (!($_SESSION['id']))
    header('Location:connect.php');

  require_once("public/header.php");

?>

<video id="video"></video><br>
<div id="startbutton"></div>
<canvas id="canvas" height="480" width="640"></canvas>

<script type="text/javascript">

(function() {

  let video = document.getElementById('video');
  let canvas = document.getElementById('canvas');
  let context = canvas.getContext('2d');

  if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia)
    navigator.mediaDevices.getUserMedia({video: true}).then((stream) => {
      video.src = window.URL.createObjectURL(stream);
      video.play();
    });
  document.getElementById('startbutton').addEventListener('click', () => {
    context.drawImage(video, 0, 0, 640, 480);
  });

})();

</script>

<?php

  require_once("public/footer.php");

?>
