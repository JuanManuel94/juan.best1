document.write(`<script src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=${key_google}&callback=initMap"></script>`);
function initMap(){
  var myLatlng = new google.maps.LatLng(0.0, -0.0);
  var infowindow = null;
  var mapOptions = {center:myLatlng,zoom:15,mapTypeId:google.maps.MapTypeId.ROADMAP};
  var map = new google.maps.Map(document.getElementById("customer_map"),mapOptions);
  var icon = base_url+"/Assets/images/default/marker.png";
  var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
  var ajaxUrl = base_url+'/customers/locations';
  request.open("GET",ajaxUrl,true);
  request.send();
  request.onreadystatechange = function(){
    if(request.readyState == 4 && request.status == 200){
      var objData = JSON.parse(request.responseText);
      Array.prototype.forEach.call(objData, function(c,e){
        if(c.latitud != "" && c.longitud != ""){
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng(c.latitud, c.longitud),
            map: map,
            icon:icon,
          });
          map.setCenter(marker.position);
          var carousel = gallery(c.images);
          if(c.state == 1){
            state = 'INSTALACIÓN';
          }
          if(c.state == 2){
            state = 'ACTIVO';
          }
          if(c.state == 3){
            state = 'SUSPENDIDO';
          }
          if(c.state == 4){
            state = 'CANCELADO';
          }
          if(c.state == 5){
            state = 'GRATIS';
          }
          var infowincontent = `
            <h5><span class="far fa-user mr-1"></span>${c.names} ${c.surnames}</h5>
            <p class="mb-1"><i class="fa fa-wifi mr-1"></i>Servicio ${state.toLowerCase()}</p>
            <p class="mb-1"><i class="fa fa-phone-alt mr-1"></i>${c.mobile}</p>
            <p class="mb-1"><i class="fa fa-envelope mr-1"></i>${c.email}</p>
            <p class="mb-1"><i class="fa fa-home mr-1"></i>${c.address}</p>
            ${carousel}
          `;
          google.maps.event.addListener(marker, 'click', function(){
            if(infowindow){
              infowindow.close();
            }
            infowindow = new google.maps.InfoWindow({content: infowincontent});
            infowindow.open(map,marker);
          });
        }
      });
    }
  }
}
function gallery(array){
  var images;
  if(array.length === 0){
    images = "";
  }else{
    images = `
      <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
    `;
    array.forEach(function(img,i){
      if(i == 0){
        images += `<li data-target="#carouselExampleIndicators" data-slide-to="${i}" class="active"></li>`;
      }else{
        images += `<li data-target="#carouselExampleIndicators" data-slide-to="${i}" class=""></li>`;
      }
    });
    images += `</ol><div class="carousel-inner">`;
    array.forEach(function(img,i){
      if(i == 0){
        images += `<div class="carousel-item active"><img src="${base_url}/Assets/uploads/gallery/${img.image}" style="width:520px;height:260px;background-repeat:no-repeat;background-size:cover;"/></div>`;
      }else{
        images += `<div class="carousel-item "><img src="${base_url}/Assets/uploads/gallery/${img.image}" style="width:520px;height:260px;background-repeat:no-repeat;background-size:cover;"/></div>`;
      }
    });
    images += `</div><a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></a><a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></a></div>`;
  }
  return images;
}
