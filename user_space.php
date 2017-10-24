<?php

  require_once("config/database.php");

  if (!($_SESSION['id']))
    header('Location:connect.php');

  require_once("public/header.php");

?>

<div class="edition">
  <video id="video"></video>
  <canvas id="filtres"></canvas>
</div>

<div id="startbutton"></div>

<canvas id="canvas" style="border: 1px solid black;"></canvas>

<script type="text/javascript">

(function() {

  let video = document.getElementById('video');
  let canvas = document.getElementById('canvas');
  let context = canvas.getContext('2d');
  let f_canvas = document.getElementById('filtres');
  let f_context = f_canvas.getContext('2d');
  let img = new Image(80, "auto");
  let prop_img = {
    x: 0,
    y: 0,
    height: 80,
    width: 80,
  };
  let dragging = false;
  let key_pressed = false;

  img.src = 'public/pictures/filtres/flocon.png';
  img.onload = () => {
    f_context.drawImage(img, 0, 0, 80, 80);
  };

  /**
   * Here the drag and drop comportement
   */

   function draw_img (m_img, img_pro, type)
   {
     let tmp = video;

     f_context.clearRect(0, 0, f_canvas.width, f_canvas.height);
     if (type == 'drag')
        f_context.drawImage(m_img, img_pro.x, img_pro.y, prop_img.width, prop_img.height);
     else if (type == 'resize')
       f_context.drawImage(m_img, prop_img.x, prop_img.y, img_pro.x, img_pro.y);
   }

   function getMousePos(canvas, evt)
   {
     let rect = f_canvas.getBoundingClientRect();

     return {
       x: (evt.pageX - (rect.left * 1.5)),
       y: (evt.pageY - (rect.top * 1.5))
     };
   }

   f_canvas.addEventListener('mousedown', (e) => {
     e.preventDefault();
     e.stopPropagation();
     if (!(key_pressed))
      dragging = true;
   });

   f_canvas.addEventListener('mousemove', (e) => {
     let prop;

     if (dragging || key_pressed)
        prop = getMousePos(canvas, e, img)
      if (dragging)
      {
        prop_img.x = prop.x;
        prop_img.y = prop.y;
        draw_img(img, prop, 'drag');
      }
      else if (key_pressed)
      {
        prop_img.width = prop.x;
        prop_img.height = prop.y;
        img.width = prop.x;
        draw_img(img, prop, 'resize');
      }
   });

   f_canvas.addEventListener('mouseup', (e) => {
     e.preventDefault();
     e.stopPropagation();
     dragging = false;
     //console.log("mouse up " + img.x);
   });

   document.addEventListener('keydown', (evt) => {
     if (evt.key == 'Alt')
       key_pressed = true;
   });

   document.addEventListener('keyup', (evt) => {
     key_pressed = false;
   });

   /**
    * Here add the camera comportemenet
    */
   if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia)
   {
     navigator.mediaDevices.getUserMedia({video: true}).then((stream) => {
       video.src = window.URL.createObjectURL(stream);
       video.play();
     });
   }
   else
    console.log("your navigator don't support camera");
   document.getElementById('startbutton').addEventListener('click', () => {
     context.drawImage(video, 0, 0, canvas.width, canvas.height);
     context.drawImage(img, prop_img.x, prop_img.y, prop_img.width, prop_img.height);
   });


})();

</script>

<?php

  require_once("public/footer.php");

?>
