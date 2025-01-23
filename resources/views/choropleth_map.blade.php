<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choropleth Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        #map { 
            height: 600px; 
        }

        .info {
            padding: 6px 8px;
            font: 14px/16px Arial, Helvetica, sans-serif;
            background: white;
            background: rgba(255,255,255,0.8);
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border-radius: 5px;
        }
        .info h4 {
            margin: 0 0 5px;
            color: #777;
        }

        .legend {
            line-height: 18px;
            color: #555;
        }
        .legend i {
            width: 18px;
            height: 18px;
            float: left;
            margin-right: 8px;
            opacity: 0.7;
        }
     </style>
</head>
<body>
    <div id="map"></div>

     <!-- Make sure you put this AFTER Leaflet's CSS -->
     <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

     <script>
        var map = L.map('map').setView([-3.3194, 103.9144], 7);

        var tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <ahref="https://sumsel.bps.go.id/id/publication/2024/09/26/25fc4b6a2ccdd4d4f9156322/regional-statistics-of-sumatera-selatan-province-2024.html">BPSP sumsel</a>'
        }).addTo(map);

        const sumsels = @json($sumsels)
        // const sumsels = {!! @json_encode($sumsels) !!}
        console.log(@json($sumsels))

        const sumselData = sumsels.map(sumsel => ({
            type: 'Feature',
            properties: {
                name: sumsel.name,
                id: sumsel.id,
                populasi: sumsel.populasi,
            },
            geometry: {
                type: sumsel.type_polygon,
                coordinates: JSON.parse(sumsel.polygon)
            }
        }));

        const geojson = {
            type: 'FeatureCollection',
            features: sumselData
        };

        function getColor(d) {
            return d > 900000 ? '#800026' :
                d > 750000  ? '#BD0026' :
                d > 600000  ? '#E31A1C' :
                d > 500000  ? '#FC4E2A' :
                d > 400000   ? '#FD8D3C' :
                d > 300000   ? '#FEB24C' :
                d > 200000   ? '#FED976' :
                            '#FFEDA0';
        }

        function style(feature) {
            return {
                fillColor: getColor(feature.properties.populasi),
                weight: 2,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.7
            };
        }

        function highlightFeature(e) {
            var layer = e.target;

            layer.setStyle({
                weight: 5,
                color: '#666',
                dashArray: '',
                fillOpacity: 0.7
            });

            layer.bringToFront();
            info.update(layer.feature.properties);
        }

        function resetHighlight(e) {
            geojson.resetStyle(e.target);
            info.update();
        }

        function zoomToFeature(e) {
            map.fitBounds(e.target.getBounds());
        }

        function onEachFeature(feature, layer) {
            layer.on({
                mouseover: highlightFeature,
                mouseout: resetHighlight,
                click: zoomToFeature
            });
        }

        L.geoJson(geojson, {
            style: style,
            onEachFeature: onEachFeature
        }).addTo(map);

        var info = L.control();

        info.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
            this.update();
            return this._div;
        };

        // method that we will use to update the control based on feature properties passed
        info.update = function (props) {
            this._div.innerHTML = '<h4>South Sumatra Province Population</h4>' +  (props ?
                '<b>' + props.name + '</b><br />' + props.populasi.toLocaleString('id-ID') + ' penduduk'
                : 'Hover over a sumsel');
        };

        info.addTo(map);

        var legend = L.control({position: 'bottomright'});

        legend.onAdd = function (map) {

            var div = L.DomUtil.create('div', 'info legend'),
                grades = [0, 200000, 300000, 400000, 500000, 600000, 750000, 900000],
                labels = [];

            // loop through our density intervals and generate a label with a colored square for each interval
            for (var i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
                    grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
            }

            return div;
        };

        legend.addTo(map);

     </script>
</body>
</html>