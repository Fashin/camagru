<?php

  require_once("config/database.php");

  if (!($_SESSION['id']))
    header('Location:connect.php');

  require_once("public/header.php");

?>

<div class="outils">
  <div id="startbutton"></div>
  <input type="color" name="color_picker" class="color_picker">
  <div class="filtre-container">
    <?php
      $filtres = scandir('public/pictures/filtres');
      ?>
      <select class="all_filters">
      <?php
      foreach ($filtres as $k => $v)
      {
        if ($v != '.' && $v != '..')
        {
          ?><option value="<?= $v ?>"><?= explode('.', $v)[0] ?></option><?php
        }
      }
    ?>
      </select>
      <input type="submit" name="apply_filter" value="Apply Filter" class="apply_filter">
      <input type="submit" name="clean_canvas" value="Clean Filter" class="clean_filter">
      <input type="submit" name="apply_filter" value="Apply Background" class="apply_background">
      <input type="submit" name="clean_canvas" value="Clean Background" class="clean_background">
  </div>
  <input class="upload_button" type="submit" name="send" value="Sauvegarder">
</div>

<video id="video"></video>
<canvas id="background_filter"></canvas>
<canvas id="filtres"></canvas>

<canvas id="canvas" style="border: 1px solid black;"></canvas>

<script type="text/javascript">

(function() {

  let video = document.getElementById('video');
  let canvas = document.getElementById('canvas');
  let context = canvas.getContext('2d');
  let f_canvas = document.getElementById('filtres');
  let f_context = f_canvas.getContext('2d');
  let bg_canvas = document.getElementById('background_filter');
  let bg_context = bg_canvas.getContext('2d');
  let img = new Image(80, "auto");
  let prop_img = {
    x: 0,
    y: 0,
    height: 80,
    width: 80,
  };
  let dragging = false;
  let key_pressed = false;
  let upload_button = document.getElementsByClassName('upload_button')[0];
  let apply_filter = document.getElementsByClassName('apply_filter')[0];
  let apply_background = document.getElementsByClassName('apply_background')[0];
  let all_filters = document.getElementsByClassName('all_filters')[0];
  let color_picker = document.getElementsByClassName('color_picker')[0];
  let clean_filter = document.getElementsByClassName('clean_filter')[0];
  let clean_background = document.getElementsByClassName('clean_background')[0];

  img.src = 'public/pictures/filtres/' + all_filters.options[all_filters.selectedIndex].value;
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
        f_context.drawImage(m_img, prop_img.x, prop_img.y, prop_img.width, prop_img.height);
     else if (type == 'resize')
       f_context.drawImage(m_img, prop_img.x, prop_img.y, prop_img.width, prop_img.height);
   }

   function getMousePos(canvas, evt)
   {
     let  rect = f_canvas.getBoundingClientRect();

     return {
       x: (evt.clientX - (rect.left * 1.5)),
       y: (evt.clientY - (rect.top * 1.5))
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
        draw_img(img, prop, 'resize');
      }
   });

   f_canvas.addEventListener('mouseup', (e) => {
     e.preventDefault();
     e.stopPropagation();
     dragging = false;
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
     context.drawImage(bg_canvas, 0, 0, canvas.width, canvas.height);
     context.drawImage(img, prop_img.x, prop_img.y, prop_img.width, prop_img.height);
   });

   /**
    * Here the upload comporment
    */
    upload_button.addEventListener('click', (e) => {
      let xml = new XMLHttpRequest();
      let picture_upload = canvas.toDataURL("image/png");

      xml.onreadystatechange = () => {
        if (xml.readyState == 4 && (xml.status == 200 || xml.status == 0))
        {
          if (xml.response > 0)
            console.log("picture correctly saved");
        }
      }

      xml.open('POST', 'controller/put_pictures.php', true);
      xml.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xml.send("img=" + picture_upload);
    });

    /**
     * Here the apply filter & background comportment
     */
     apply_filter.addEventListener('click', (e) => {
       f_context.clearRect(0, 0, f_canvas.width, f_canvas.height);
       img.src = 'public/pictures/filtres/' + all_filters.options[all_filters.selectedIndex].value;
     });

     apply_background.addEventListener('click', (e) => {
       let selected_color = color_picker.value;
       let color = {
         red: parseInt(selected_color.slice(1, 3), 16),
         green: parseInt(selected_color.slice(3, 5), 16),
         blue: parseInt(selected_color.slice(5, 7), 16),
       };
       bg_context.fillStyle = "rgba(" + color.red + ", " + color.green + ", " + color.blue + ", 0.5)";
       bg_context.fillRect(0, 0, bg_canvas.width, bg_canvas.height);
     });

     clean_filter.addEventListener('click', (e) => {
       f_context.clearRect(0, 0, f_canvas.width, f_canvas.height);
     });

     clean_background.addEventListener('click', (e) => {
       bg_context.clearRect(0, 0, bg_canvas.width, bg_canvas.height);
     });

})();

</script>

<?php

  require_once("public/footer.php");

?>
