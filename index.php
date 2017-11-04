<?php

  require_once("config/database.php");

  require_once("public/header.php");

  $page[0] = 0;
  $page[1] = 10;

  require_once("controller/get_pictures.php");

?>

<script type="text/javascript">

  window.onload = () => {
    let xhr = new XMLHttpRequest();
    let range = [0, 10];
    let hearth = document.getElementsByClassName('hearth');
    let send = document.getElementsByClassName('send');
    let interract = document.getElementsByClassName('interract-container');


    document.addEventListener('scroll', (e) => {
      let scrollY = window.scrollY;
      let body = document.body;
      let html = document.documentElement;
      let clientPos = body.scrollHeight - html.clientHeight;

      if (scrollY >= clientPos)
      {
        range[0] += 10;
        range[1] += 10;
        xhr.onreadystatechange = () => {
          if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
          {
            document.body.innerHTML += xhr.response;
            location.reload();
          }
        }
        xhr.open('GET', 'controller/get_pictures.php?picture=true&range_start=' + range[0] + '&range_end=' + range[1]);
        xhr.send();
      }
    });

    for (let i = 0; i < hearth.length; i++)
      hearth[i].addEventListener('click', (e) => {
        let id = hearth[i].parentNode.getAttribute('id_picture');
        let state = (hearth[i].getAttribute('src').split('/')[2] == 'empty_hearth.png') ? 0 : 1;
        let id_interract = hearth[i].getAttribute('id_interract');

        xhr.onreadystatechange = (e) => {
          if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
          {
            if (state)
              hearth[i].setAttribute('src', 'public/pictures/empty_hearth.png');
            else
              hearth[i].setAttribute('src', 'public/pictures/fill_hearth.png');
            if (id_interract == -1)
              hearth[i].setAttribute('id_interract', xhr.response);
            else
              hearth[i].setAttribute('id_interract', "-1");
          }
        }

        xhr.open('POST', 'controller/update_interract.php');
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("id=" + id + "&state=" + state + "&type=like" + "&id_interract=" + id_interract);
      });

    for (let i = 0; i < send.length; i++)
      send[i].addEventListener('click', (e) => {
        let parent = send[i].parentNode;
        let id = parent.getAttribute('id_picture');
        let textarea = parent.getElementsByClassName('comment')[0]
        let value = textarea.value

        if (value != "" && value.length < 255)
        {
          xhr.onreadystatechange = (e) => {
            if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
            {
              if (xhr.response == "-2")
                window.location.href= "connect.php";
              else
              {
                let el = document.createElement('div');

                el.setAttribute('class', 'interract');
                el.innerHTML = xhr.response;
                textarea.value = "";
                if (parent.getElementsByClassName('interract-container')[0].childNodes.length % 2)
                  el.setAttribute('class', el.getAttribute('class') + ' interract-pair');
                else
                  el.setAttribute('class', el.getAttribute('class') + ' interract-impair');
                parent.getElementsByClassName('interract-container')[0].append(el);
                location.reload();
              }
            }
          }
          xhr.open('POST', 'controller/update_interract.php');
          xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhr.send("id=" + id + "&value=" + value + "&type=comment&state=1&id_interract=-1");
        }
      });

    for (let i = 0; i < interract.length; i++)
    {
      interract[i].addEventListener('click', (e) => {
        let el = e.srcElement;
        let my_class = el.getAttribute('class');
        let parent = null;

        if (my_class == "pseudo" || my_class == "value" || my_class == "hidden")
          parent = el.parentElement;
        else if (my_class == "modify_comment" || my_class == "delete_comment")
          parent = el.parentElement.parentElement;
        else if (my_class == "interract interract-pair" || my_class == "interract interract-impair")
          parent = el;
        if (parent && parent.childNodes[3] !== undefined)
        {
          let style = window.getComputedStyle(parent.childNodes[3]);
          if (style.display == "none")
            parent.childNodes[3].style.display = "inline-block";
          else
            parent.childNodes[3].style.display = "none";
        }
      });
      for (let j = 0; j < interract[i].childNodes.length; j++)
      {
        let el = interract[i].childNodes[j].getElementsByClassName('hidden')[0];
        if (el !== undefined)
        {
          let parent = el.parentNode;
          let id = el.childNodes[0].value;
          let my_modify = el.childNodes[1];
          let my_delete = el.childNodes[2];

          my_modify.addEventListener('click', (e) => {
            let elem = parent.childNodes[2];
            let value = elem.innerText;
            let input = document.createElement('input');

            input.setAttribute('value', value);
            input.setAttribute('class', 'value');
            input.addEventListener('keydown', (event) => {
              if (event.keyCode == 13)
              {
                let val = event.srcElement.value;
                let tester = val.replace('/\s/g', '');

                if (tester.length > 0)
                {
                  xhr.onreadystatechange = (e) => {
                    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
                    {
                      if (xhr.response == "1")
                        location.reload();
                      else
                        alert("Error from update data");
                    }
                  }
                  xhr.open('POST', 'controller/update_interract.php', true);
                  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                  xhr.send('value=' + val + '&id=' + id + '&state=1&type=comment&request=UPDATE');
                }
              }
            });
            parent.removeChild(elem);
            parent.insertBefore(input, el);
          });

          my_delete.addEventListener('click', (e) => {
            let respons = confirm("Etes vous sur de vouloir supprimer le commentaire ?");
            if (respons)
            {
              xhr.onreadystatechange = (e) => {
                if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
                {
                  if (xhr.response)
                    location.reload();
                  else
                    alert("Error from delete data");
                }
              }
              xhr.open('POST', 'controller/update_interract.php', true);
              xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
              xhr.send('id=' + id + '&state=1&type=comment&request=DELETE');
            }
          });
        }
      }
    }
  }

</script>

<?php

  require_once("public/footer.php");

?>
