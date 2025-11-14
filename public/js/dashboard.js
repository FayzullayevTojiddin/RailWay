// Soxta backend ma'lumotlari
const stationsData = {
    'karshi': {
        name: 'Karshi',
        type: 'Stansiya',
        distance: '0 km',
        electrified: true,
        description: 'РЖД-Термез йўналишининг бошланғич нуқтаси',
        coordinates: [200, 150],
        details: {
            code: 'KR-01',
            opened: '1985',
            platforms: '3 ta',
            status: 'Faol'
        }
    },
    'termez': {
        name: 'Termez Д460',
        type: 'Stansiya',
        distance: '220 km',
        electrified: true,
        description: 'Afg\'oniston chegarasiga yaqin asosiy stansiya',
        coordinates: [900, 650],
        details: {
            code: 'TM-01',
            opened: '1988',
            platforms: '4 ta',
            status: 'Faol'
        }
    },
    'galaba': {
        name: 'st. Galaba',
        type: 'Stansiya',
        distance: '168 km',
        electrified: true,
        description: 'Oraliq stansiya',
        coordinates: [850, 570],
        details: {
            code: 'GL-01',
            platforms: '2 ta',
            status: 'Faol'
        }
    }
};

// Chiziqlar ma'lumoti
const railLinesData = [
    {
        id: 'main-line-1',
        name: 'Karshi-Termez asosiy yo\'l',
        type: 'electrified',
        coords: [[200, 150], [300, 300], [500, 400], [700, 500], [900, 650]]
    },
    {
        id: 'branch-1',
        name: 'Galaba tarmog\'i',
        type: 'unelectrified',
        coords: [[850, 570], [950, 570], [1000, 550]]
    },
    {
        id: 'branch-2',
        name: 'Cement zavod',
        type: 'unelectrified',
        coords: [[300, 300], [250, 450], [280, 500]]
    }
];

// Filter holati
let showElectrified = true;
let showUnelectrified = true;

// Xarita o'lchamlari
const extent = [0, 0, 1200, 800];
const projection = new ol.proj.Projection({
    code: 'railway-map',
    units: 'pixels',
    extent: extent
});

// Popup
const popup = document.getElementById('popup');
const popupContent = document.getElementById('popup-content');
const popupCloser = document.getElementById('popup-closer');

const overlay = new ol.Overlay({
    element: popup,
    autoPan: true,
    autoPanAnimation: { duration: 250 }
});

popupCloser.onclick = function () {
    overlay.setPosition(undefined);
    return false;
};

// Xarita yaratish
const map = new ol.Map({
    target: 'map',
    overlays: [overlay],
    layers: [
        new ol.layer.Image({
            source: new ol.source.ImageStatic({
                url: 'https://i.imgur.com/placeholder.jpg',
                projection: projection,
                imageExtent: extent
            })
        })
    ],
    view: new ol.View({
        projection: projection,
        center: ol.extent.getCenter(extent),
        zoom: 2,
        maxZoom: 6,
        minZoom: 1
    }),
    controls: [] // Default controllarni o'chirish
});

// Vector layerlar
const stationLayer = new ol.layer.Vector({
    source: new ol.source.Vector(),
    style: new ol.style.Style({
        image: new ol.style.Circle({
            radius: 7,
            fill: new ol.style.Fill({ color: '#ef4444' }),
            stroke: new ol.style.Stroke({ color: 'white', width: 2 })
        })
    })
});

const railLayer = new ol.layer.Vector({
    source: new ol.source.Vector(),
    style: function (feature) {
        const lineType = feature.get('lineType');
        return new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: lineType === 'electrified' ? '#1f2937' : '#8b5cf6',
                width: 3,
                lineDash: lineType === 'unelectrified' ? [10, 5] : null
            })
        });
    }
});

map.addLayer(railLayer);
map.addLayer(stationLayer);

// Ma'lumotlarni yuklash
function loadMapData() {
    const stationSource = stationLayer.getSource();
    const railSource = railLayer.getSource();

    stationSource.clear();
    railSource.clear();

    // Stansiyalarni qo'shish
    Object.keys(stationsData).forEach(key => {
        const station = stationsData[key];
        const feature = new ol.Feature({
            geometry: new ol.geom.Point(station.coordinates),
            stationId: key,
            name: station.name,
            type: station.type
        });
        stationSource.addFeature(feature);
    });

    // Chiziqlarni qo'shish
    railLinesData.forEach(line => {
        if (line.type === 'electrified' && !showElectrified) return;
        if (line.type === 'unelectrified' && !showUnelectrified) return;

        const feature = new ol.Feature({
            geometry: new ol.geom.LineString(line.coords),
            lineType: line.type,
            name: line.name
        });
        railSource.addFeature(feature);
    });
}

// Zoom controls
document.getElementById('zoomIn').addEventListener('click', () => {
    const view = map.getView();
    view.animate({ zoom: view.getZoom() + 0.5, duration: 250 });
});

document.getElementById('zoomOut').addEventListener('click', () => {
    const view = map.getView();
    view.animate({ zoom: view.getZoom() - 0.5, duration: 250 });
});

// Filter toggles
document.getElementById('toggleElectrified').addEventListener('change', function () {
    showElectrified = this.checked;
    loadMapData();
});

document.getElementById('toggleUnelectrified').addEventListener('change', function () {
    showUnelectrified = this.checked;
    loadMapData();
});

// Map type selector
document.getElementById('mapType').addEventListener('change', function () {
    // Bu yerda xarita turini o'zgartirish logikasi
    // Keyinchalik turli xarita rasmlari o'rnatiladi
});

// Sidebar
const sidebar = document.querySelector('.sidebar');
const sidebarClose = document.querySelector('.sidebar-close');
const overlay_bg = document.querySelector('.overlay');

function openSidebar(stationId) {
    const station = stationsData[stationId];
    if (!station) return;

    const sidebarContent = document.querySelector('.sidebar-content');
    sidebarContent.innerHTML = `
        <div class="info-card">
            <div class="info-card-title">📍 ${station.name}</div>
            <div class="info-row">
                <span class="info-label">Turi:</span>
                <span class="info-value">${station.type}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Masofa:</span>
                <span class="info-value">${station.distance}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Elektrlashtirilgan:</span>
                <span class="info-value">${station.electrified ? 'Ha ✓' : 'Yo\'q ✗'}</span>
            </div>
        </div>
        
        <div class="info-card">
            <div class="info-card-title">📝 Tavsif</div>
            <p style="color: #6b7280; line-height: 1.6; font-size: 0.95rem;">${station.description}</p>
        </div>
        
        <div class="info-card">
            <div class="info-card-title">⚙️ Texnik ma'lumotlar</div>
            <div class="station-details">
                <div class="detail-item">
                    <div class="detail-label">Stansiya kodi</div>
                    <div class="detail-value">${station.details.code}</div>
                </div>
                ${station.details.opened ? `
                <div class="detail-item">
                    <div class="detail-label">Ochilgan yil</div>
                    <div class="detail-value">${station.details.opened}</div>
                </div>` : ''}
                ${station.details.platforms ? `
                <div class="detail-item">
                    <div class="detail-label">Platformalar soni</div>
                    <div class="detail-value">${station.details.platforms}</div>
                </div>` : ''}
                <div class="detail-item">
                    <div class="detail-label">Holat</div>
                    <div class="detail-value">${station.details.status}</div>
                </div>
            </div>
        </div>
    `;

    sidebar.classList.add('active');
    overlay_bg.classList.add('active');
}

function closeSidebar() {
    sidebar.classList.remove('active');
    overlay_bg.classList.remove('active');
}

sidebarClose.addEventListener('click', closeSidebar);
overlay_bg.addEventListener('click', closeSidebar);

// Click event
map.on('singleclick', function (evt) {
    const feature = map.forEachFeatureAtPixel(evt.pixel, function (feature) {
        return feature;
    });

    if (feature) {
        const stationId = feature.get('stationId');

        if (stationId) {
            openSidebar(stationId);
        } else {
            const coords = feature.getGeometry().getCoordinates()[0];
            const lineName = feature.get('name');

            popupContent.innerHTML = `
                <div class="popup-title">${lineName}</div>
                <div class="popup-type">Temir yo'l liniyasi</div>
            `;
            overlay.setPosition(coords);
        }
    }
});

// Hover effect
map.on('pointermove', function (evt) {
    const pixel = map.getEventPixel(evt.originalEvent);
    const hit = map.hasFeatureAtPixel(pixel);
    map.getTargetElement().style.cursor = hit ? 'pointer' : '';
});

// Dastlabki yuklash
document.getElementById('loading').style.display = 'none';
loadMapData();