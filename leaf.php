
<!DOCTYPE html>
<html lang="en">
<head>
	<base target="_top">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Quick Start - Leaflet</title>
	
	<link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>

	<style>
		html, body {
			height: 100%;
			margin: 0;
		}
		.leaflet-container {
			height: 400px;
			width: 600px;
			max-width: 100%;
			max-height: 100%;
		}
	</style>

	
</head>
<body>



<div id="map" style="width: 600px; height: 400px;"></div>
<script>

	const map = L.map('map').setView([34.700001, 136.500000], 13);

	const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	}).addTo(map);

	const marker = L.marker([34.700001, 136.500000]).addTo(map)
		.bindPopup('<b>Hello world!</b><br />I am a popup.').openPopup();

	// const circle = L.circle([51.508, -0.11], {
	// 	color: 'red',
	// 	fillColor: '#f03',
	// 	fillOpacity: 0.5,
	// 	radius: 500
	// }).addTo(map).bindPopup('I am a circle.');

	// const polygon = L.polygon([
	// 	[51.509, -0.08],
	// 	[51.503, -0.06],
	// 	[51.51, -0.047]
	// ]).addTo(map).bindPopup('I am a polygon.');


	// const popup = L.popup()
	// 	.setLatLng([34.700001, 136.500000])
	// 	.setContent('I am a standalone popup.')
	// 	.openOn(map);

	// function onMapClick(e) {
	// 	popup
	// 		.setLatLng(e.latlng)
	// 		.setContent(`You clicked the map at ${e.latlng.toString()}`)
	// 		.openOn(map);
	// }

	// map.on('click', onMapClick);

</script>



</body>
</html>