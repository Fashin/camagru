class PopUp
{

  close(el)
  {
    let div = el.srcElement.parentNode;

    document.body.removeChild(div);
  }

  display(text, type)
  {
    let div = document.createElement('div')
    let img = document.createElement('img');
    let doc = document.createElement('span');

    doc.innerHTML = text;

    img.src = "/camagru/public/pictures/pop_up/" + type + ".png";

    div.className += "pop_up";
    doc.className += "pop_up-text";

    div.appendChild(img);
    div.appendChild(doc);

    div.addEventListener('click', (e) => { this.close(e); });

    document.body.prepend(div);
  }
}
