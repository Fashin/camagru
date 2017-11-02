<?php

  session_start();

  if (!($_SESSION['id']))
    header('Location:connect.php');

  require_once("public/header.php");

?>

<div class="outils">
  <div id="startbutton"></div><br><br>
  <input type="color" name="color_picker" class="color_picker"><br>
  <input type="submit" name="apply_filter" value="Apply" class="apply_background">
  <input type="submit" name="clean_canvas" value="Clean" class="clean_background"><br><br>
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
  </select><br>
      <input type="submit" name="apply_filter" value="Apply" class="apply_filter">
      <input type="submit" name="clean_canvas" value="Clean" class="clean_filter">
  </div>
  <input type="file" name="imageLoader" id="imageLoader">
</div>

<video id="video"></video>
<canvas id="background_filter"></canvas>
<canvas id="filtres"></canvas>

<div class="review"></div>

<script src="public/js/PopUp.class.js" charset="utf-8"></script>

<script type="text/javascript">

(function() {

  let video = document.getElementById('video');
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
  let review = document.getElementsByClassName('review')[0];
  let pop_up = new PopUp();
  let imageLoader = document.getElementById('imageLoader');

  img.src = 'public/pictures/filtres/' + all_filters.options[all_filters.selectedIndex].value;
  img.onload = () => {
    f_context.drawImage(img, 0, 0, 80, 80);
  };

  let get_user_picture = () => {
    let xhr = new XMLHttpRequest();


    xhr.onreadystatechange = () => {
      if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
      {
        review.innerHTML = xhr.response;
        for (let i = 0; i < review.childNodes.length; i++)
          review.childNodes[i].childNodes[1].addEventListener('click', (e) => {
            let xhr2 = new XMLHttpRequest();

            xhr2.onreadystatechange = () => {
              if (xhr2.readyState == 4 && (xhr2.status == 200 || xhr2.status == 0))
              {
                if (xhr2.response == "1")
                {
                  get_user_picture();
                  pop_up.display("Image supprimé", "success");
                }
                else
                  pop_up.display("Erreur lors de la suppression de l'images", "error");
              }
            }
            xhr2.open('GET', 'controller/get_pictures.php?delete=' + review.childNodes[i].childNodes[1].getAttribute('id_picture') + "&user_space", true);
            xhr2.send();
          });
      }
    }

    xhr.open('GET', 'controller/get_pictures.php?user_space', true);
    xhr.send();
  }

  window.onload = get_user_picture();

  imageLoader.addEventListener('change', handleImage, false);

  function handleImage(e)
  {
    let reader = new FileReader();

    reader.onload = (evt) => {
      let img = new Image();

      img.onload = () => {
        let r_canvas = document.createElement('canvas');
        let r_context = r_canvas.getContext('2d');

        r_canvas.setAttribute('id', 'video');
        r_canvas.setAttribute('type', 'picture');
        video.remove();
        document.body.insertBefore(r_canvas, document.getElementsByClassName('outils')[0]);
        video = r_canvas;
        r_context.drawImage(img, 0, 0, 400, 300);
      }
      img.src = evt.target.result;
    }
    reader.readAsDataURL(e.target.files[0]);
  }

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

   function getMousePos(evt)
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
        prop = getMousePos(e)
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
    pop_up.display("your navigator don't support camera", "error");

   document.getElementById('startbutton').addEventListener('click', () => {
     let xml = new XMLHttpRequest();
     let send_filtre = f_canvas.toDataURL();
     let send_bg_filtre = bg_canvas.toDataURL();
     let send_background = document.createElement('img');
     let blank = document.createElement('canvas');
     let tmp_canvas = document.createElement('canvas');

      if (video.getAttribute('type') == null)
        tmp_canvas.getContext('2d').drawImage(video, 0, 0, tmp_canvas.width, tmp_canvas.height)
      else
        tmp_canvas = video;
     send_background = tmp_canvas.toDataURL();

     blank.width = tmp_canvas.width;
     blank.height = tmp_canvas.height;
     if (tmp_canvas.toDataURL() == blank.toDataURL())
       pop_up.display("please take or upload a picture", "error");
     else
     {
       xml.onreadystatechange = () => {
         if (xml.readyState == 4 && (xml.status == 200 || xml.status == 0))
         {
           console.log(xml.response);
           if (xml.response > 0)
             pop_up.display("Image correctement sauver", "success");
            else
              pop_up.display("Failed to save the picture", "error");
           get_user_picture();
         }
       }

       xml.open('POST', 'controller/put_pictures.php', true);
       xml.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
       xml.send("img_background=" + send_background + "&filtre_background=" + send_filtre + "&filtre_bg_background="+send_bg_filtre);
     }
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
