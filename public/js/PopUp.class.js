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
    let old = document.getElementsByClassName('pop_up');

    doc.innerHTML = text;

    img.src = "./../public/pictures/pop_up/" + type + ".png";

    div.className += "pop_up";
    doc.className += "pop_up-text";

    div.appendChild(img);
    div.appendChild(doc);

    div.addEventListener('click', (e) => { this.close(e); });

    if (old.length > 0)
      old[0].remove();

    let pop_up = document.getElementsByClassName('pop_up');
    if (pop_up.length > 0)
      pop_up.remove();
    document.body.prepend(div);
  }
}
