function addEvent(element, event, func) {
  if(element.addEventListener)
  {
    element.addEventListener(event, func, false);
  }
  else
  {
    element.attachEvent('on' + event, func);
  }
}

var formulaire = document.getElementById('filtres');
var boutton = document.getElementById('send');
var item_box = document.getElementById('items_box');

addEvent(boutton, 'click', function(e){
  e.preventDefault();
  var xhr = new XMLHttpRequest();
  var data = new FormData(formulaire);
  xhr.onloadend = function() {
    if(xhr.status == 200)
    {
      item_box.innerHTML = xhr.responseText;
    }
    else
    {
      alert('Erreur lors du filtrage : Erreur ' + xhr.status + '; ' + xhr.statusText);
    } 
  };
  xhr.open('POST', 'ajax/filters.php');
  xhr.send(data);
});

