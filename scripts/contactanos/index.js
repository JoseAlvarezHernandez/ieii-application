/* ********************************************************************
	Se crea archivo de index.js para el modulo de contactanos
	Jose de Jesus Alvarez Hernandez
	2016-06-19

******************************************************************** */
$(document).ready(function(){
	//initialize();
});

/* ********************************************************************
	Se crea funcion para cargar mapa en la parte de contactanos
	Jose de Jesus Alvarez Hernandez
	2016-06-19

******************************************************************** */
function initialize() {
	var myLatlng = new google.maps.LatLng(19.432608,-99.133209);
	var mapOptions = {
  		zoom: 5,
  		center: myLatlng,
  		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	var marker = new google.maps.Marker({
  	position: myLatlng,
	  	map: map,
	  	title: 'Contact'
	});
}
google.maps.event.addDomListener(window, 'load', initialize);