<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumlah Orang Islam</title>
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
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const sumsels = @json($sumsels);

    const sumselData = sumsels.map(sumsel => ({
        type: 'Feature',
        properties: {
            name: sumsel.name,
            id: sumsel.id,
            beragama_islam: Number(sumsel.beragama_islam),
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
        return d > 900000 ? '#0000FF' : // Biru tua
            d > 750000  ? '#0000CC' : // Biru sedang
            d > 600000  ? '#000099' : // Biru lebih muda
            d > 500000  ? '#000066' : // Biru gelap
            d > 400000  ? '#6666FF' : // Biru keunguan
            d > 300000  ? '#9999FF' : // Biru muda
            d > 200000  ? '#CCCCFF' : // Biru sangat muda
                        '#FFFFFF';  // Putih
    }

    function style(feature) {
        return {
            fillColor: getColor(feature.properties.beragama_islam),
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
        geojsonLayer.resetStyle(e.target);
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

    const geojsonLayer = L.geoJson(geojson, { style: style, onEachFeature: onEachFeature }).addTo(map);

    var info = L.control();

    info.onAdd = function (map) {
        this._div = L.DomUtil.create('div', 'info');
        this.update();
        return this._div;
    };

    info.update = function (props) {
        this._div.innerHTML = '<h4>Number of Muslims in South Sumatra Province</h4>' + 
            (props ? 
                '<b>' + props.name + '</b><br />' + 
                (props.beragama_islam ? props.beragama_islam.toLocaleString('id-ID') : 'Data tidak tersedia') + ' orang' : 
                'Hover over a sumsel');
    };

    info.addTo(map);

    var legend = L.control({ position: 'bottomright' });

    legend.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'info legend'),
            grades = [0, 200000, 300000, 400000, 500000, 600000, 750000, 900000],
            labels = [];

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