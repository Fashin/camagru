<?php

  session_start();

  if (!($_SESSION['id']))
    header('Location:connect.php');

  require_once("config/database.php");
  require_once("public/header.php");

?>

<video id="video"></video>
<button id="startbutton">Prendre une photo</button>
<canvas id="canvas"></canvas>
<img src="http://placekitten.com/g/320/261" id="photo" alt="photo">


<script type="text/javascript">
(function() {


  var streaming = false,
   video       	= document.querySelector('#video'),
   cover       	= document.querySelector('#cover'),
   canvas      	= document.querySelector('#canvas'),
   photo       	= document.querySelector('#photo'),
   startbutton 	= document.querySelector('#startbutton'),
   saveButton	= document.querySelector('#save'),
   addFilter 	= document.querySelector('#addfilter'),
   gS_check		= document.querySelector('#greyScale_checkBox'),
   how2Use		= document.querySelector('#howToUse'),
   helpBox		= document.querySelector('#helpBox'),
   close			= document.querySelector('#close'),
   gS_checked	= false,
   width 		= 500,
   height 		= 0,
   mousePos 		= {
     x: 0,
     y: 0
   };

 navigator.getMedia = (navigator.getUserMedia ||
           navigator.webkitGetUserMedia ||
           navigator.mozGetUserMedia ||
           navigator.msGetUserMedia);

 navigator.getMedia(
 {
   video: true,
   audio: false
 }, function(stream) {
   if (navigator.mozGetUserMedia) {
   video.mozSrcObject = stream;
   } else {
     var vendorURL = window.URL || window.webkitURL;
     video.src = vendorURL.createObjectURL(stream);
   }
     video.play();
 }, function(err) {
   console.log("An error occured! " + err);
 }
 );

})();
</script>

<?php

  require_once("public/footer.php");

?>
