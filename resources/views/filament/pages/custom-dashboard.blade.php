<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temir yo'l xaritasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        #map {
            width: 100%;
            height: 100vh;
        }
        .leaflet-popup-content-wrapper {
            display: none;
        }
        .map-type-option {
            width: 120px;
            height: 90px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
        }
        .map-type-option:hover {
            border-color: #3b82f6;
        }
        .map-type-option.active {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
    </style>
</head>
<body class="overflow-hidden">
    <div x-data="mapComponent()" x-init="init()" class="relative w-full h-screen bg-gray-100 overflow-hidden">

        <!-- Map Container -->
        <div id="map"></div>

        <!-- Right Sidebar -->
        <div
            x-show="selectedStation"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform translate-x-full"
            x-transition:enter-end="transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform translate-x-0"
            x-transition:leave-end="transform translate-x-full"
            class="absolute top-0 right-0 h-full w-96 bg-white shadow-2xl z-[1001] overflow-y-auto"
            style="display: none;"
        >
            <template x-if="selectedStation">
                <div>
                    <!-- Close Button -->
                    <button
                        @click="closeStationDetails()"
                        class="absolute top-4 right-4 z-10 w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
                    >
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- Images Carousel -->
                    <div class="relative h-64 bg-gray-200">
                        <template x-if="selectedStation.images && selectedStation.images.length > 0">
                            <div class="relative h-full">
                                <img 
                                    :src="selectedStation.images[currentImageIndex]" 
                                    class="w-full h-full object-cover"
                                    alt="Station image"
                                />
                                
                                <!-- Image navigation -->
                                <template x-if="selectedStation.images.length > 1">
                                    <div>
                                        <button
                                            @click="prevImage()"
                                            class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 rounded-full flex items-center justify-center hover:bg-black/70 transition-colors"
                                        >
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>
                                        <button
                                            @click="nextImage()"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 rounded-full flex items-center justify-center hover:bg-black/70 transition-colors"
                                        >
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                        
                                        <!-- Image indicators -->
                                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2">
                                            <template x-for="(img, index) in selectedStation.images" :key="index">
                                                <button
                                                    @click="currentImageIndex = index"
                                                    class="w-2 h-2 rounded-full transition-all"
                                                    :class="currentImageIndex === index ? 'bg-white w-4' : 'bg-white/50'"
                                                ></button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <!-- Title -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-2" x-text="selectedStation.title"></h2>
                        
                        <!-- Type Badge -->
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mb-4"
                             :class="{
                                'bg-blue-100 text-blue-700': selectedStation.type === 'station',
                                'bg-purple-100 text-purple-700': selectedStation.type === 'terminal',
                                'bg-green-100 text-green-700': selectedStation.type === 'junction'
                             }">
                            <span x-text="selectedStation.type === 'station' ? 'Stantsiya' : selectedStation.type === 'terminal' ? 'Terminal' : 'Tutashuv'"></span>
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm leading-relaxed mb-6" x-text="selectedStation.description"></p>

                        <!-- Details -->
                        <template x-if="selectedStation.details">
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-500">Platformalar</span>
                                    <span class="text-sm font-medium text-gray-900" x-text="selectedStation.details.platforms"></span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-500">Yo'llar</span>
                                    <span class="text-sm font-medium text-gray-900" x-text="selectedStation.details.tracks"></span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-500">Elektrlashtirilgan</span>
                                    <span class="text-sm font-medium" 
                                          :class="selectedStation.details.electrified ? 'text-green-600' : 'text-red-600'"
                                          x-text="selectedStation.details.electrified ? 'Ha' : 'Yo\'q'">
                                    </span>
                                </div>
                                <template x-if="selectedStation.details.year_built">
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                        <span class="text-sm text-gray-500">Qurilgan yili</span>
                                        <span class="text-sm font-medium text-gray-900" x-text="selectedStation.details.year_built"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Facilities -->
                        <template x-if="selectedStation.details && selectedStation.details.facilities">
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">Xizmatlar</h3>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="facility in selectedStation.details.facilities" :key="facility">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700"
                                              x-text="facility">
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Batafsil Button -->
                        <button
                            @click="goToStationDetails()"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition-colors flex items-center justify-center gap-2"
                        >
                            <span>Batafsil ma'lumot</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Bottom Left - Map Type Button -->
        <div class="absolute bottom-4 left-4 z-[1000]">
            <button
                @click="showSettings = !showSettings"
                class="bg-white rounded-lg shadow-md px-4 py-2.5 border border-gray-200 flex items-center gap-2 hover:bg-gray-50 transition-all duration-150 hover:shadow-lg active:scale-95"
            >
                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span class="font-medium text-sm text-gray-700">Xarita ko'rinishi</span>
            </button>

            <!-- Map Type Panel -->
            <div
                x-show="showSettings"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                @click.away="showSettings = false"
                class="absolute bottom-full mb-3 left-0 bg-white rounded-lg shadow-xl p-4 border border-gray-200 min-w-[400px]"
                style="display: none;"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Xarita ko'rinishi</h3>
                    <button @click="showSettings = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div @click="changeMapLayer('osm')" class="map-type-option" :class="mapType === 'osm' ? 'active' : ''">
                        <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center relative">
                            <svg class="w-12 h-12 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <div class="absolute bottom-2 left-2 right-2 text-center">
                                <span class="text-xs font-medium text-blue-900 bg-white/90 px-2 py-1 rounded">Oddiy</span>
                            </div>
                        </div>
                    </div>

                    <div @click="changeMapLayer('satellite')" class="map-type-option" :class="mapType === 'satellite' ? 'active' : ''">
                        <div class="w-full h-full bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center relative">
                            <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                            </svg>
                            <div class="absolute bottom-2 left-2 right-2 text-center">
                                <span class="text-xs font-medium text-green-900 bg-white/90 px-2 py-1 rounded">Sputnik</span>
                            </div>
                        </div>
                    </div>

                    <div @click="changeMapLayer('dark')" class="map-type-option" :class="mapType === 'dark' ? 'active' : ''">
                        <div class="w-full h-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center relative">
                            <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                            </svg>
                            <div class="absolute bottom-2 left-2 right-2 text-center">
                                <span class="text-xs font-medium text-white bg-gray-800/90 px-2 py-1 rounded">Qorong'u</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Xaritada ko'rsatish</h4>
                    <div class="space-y-2">
                        <label class="flex items-center justify-between cursor-pointer group">
                            <span class="text-sm text-gray-700 group-hover:text-gray-900">Stansiyalar</span>
                            <input type="checkbox" x-model="filters.station" @change="updateFilters()" 
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" />
                        </label>
                        <label class="flex items-center justify-between cursor-pointer group">
                            <span class="text-sm text-gray-700 group-hover:text-gray-900">Temir yo'l yo'nalishlari</span>
                            <input type="checkbox" x-model="filters.railway" @change="updateFilters()" 
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" />
                        </label>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function mapComponent() {
            return {
                showSettings: false,
                mapType: 'osm',
                filters: {
                    station: true,
                    railway: true
                },
                map: null,
                markersLayer: null,
                railwayLayer: null,
                currentTileLayer: null,
                selectedStation: null,
                currentImageIndex: 0,
                
                stations: @json($this->getStations()),
                
                mapLayers: {
                    osm: {
                        url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                        attribution: '© OpenStreetMap'
                    },
                    satellite: {
                        url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                        attribution: '© Esri'
                    },
                    dark: {
                        url: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
                        attribution: '© CartoDB'
                    }
                },
                
                init() {
                    this.$nextTick(() => {
                        this.initMap();
                    });
                },
                
                initMap() {
                    const center = [38.0, 66.5];
                    
                    this.map = L.map('map', {
                        center: center,
                        zoom: 8,
                        zoomControl: true
                    });
                    
                    this.map.zoomControl.setPosition('bottomright');
                    this.changeMapLayer('osm');
                    
                    this.markersLayer = L.layerGroup().addTo(this.map);
                    this.railwayLayer = L.layerGroup().addTo(this.map);
                    
                    this.addStations();
                    this.addRailwayLine();
                },
                
                changeMapLayer(type) {
                    if (this.currentTileLayer) {
                        this.map.removeLayer(this.currentTileLayer);
                    }
                    
                    const layer = this.mapLayers[type];
                    this.currentTileLayer = L.tileLayer(layer.url, {
                        attribution: layer.attribution,
                        maxZoom: 18
                    }).addTo(this.map);
                    
                    this.mapType = type;
                },
                
                addStations() {
                    if (!this.markersLayer) return;
                    this.markersLayer.clearLayers();
                    
                    if (!this.filters.station) return;
                    
                    this.stations.forEach(station => {
                        const lat = station.coordinates?.lat || 37.2242;
                        const lng = station.coordinates?.lng || 67.2783;
                        
                        const trainIcon = L.divIcon({
                            className: 'custom-marker',
                            html: `
                                <div style="
                                    background: white;
                                    border-radius: 50%;
                                    padding: 10px;
                                    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 44px;
                                    height: 44px;
                                    border: 3px solid #2563eb;
                                    cursor: pointer;
                                ">
                                    <img src="https://railmap.das-uty.uz/assets/Train-77ea8bfb.svg" 
                                         style="width: 24px; height: 24px;" 
                                         alt="train">
                                </div>
                            `,
                            iconSize: [44, 44],
                            iconAnchor: [22, 22]
                        });
                        
                        const marker = L.marker([lat, lng], { icon: trainIcon });
                        
                        marker.on('click', () => {
                            this.openStationDetails(station);
                        });
                        
                        this.markersLayer.addLayer(marker);
                    });
                },
                
                addRailwayLine() {
                    if (!this.railwayLayer) return;
                    this.railwayLayer.clearLayers();
                    
                    if (!this.filters.railway) return;
                    
                    const mainLine = this.stations.map(s => [
                        s.coordinates?.lat || 37.2242, 
                        s.coordinates?.lng || 67.2783
                    ]);
                    
                    const polyline = L.polyline(mainLine, {
                        color: '#2563eb',
                        weight: 5,
                        opacity: 0.8,
                        smoothFactor: 1
                    });
                    
                    this.railwayLayer.addLayer(polyline);
                },
                
                updateFilters() {
                    this.addStations();
                    this.addRailwayLine();
                },
                
                openStationDetails(station) {
                    this.selectedStation = station;
                    this.currentImageIndex = 0;
                },
                
                closeStationDetails() {
                    this.selectedStation = null;
                    this.currentImageIndex = 0;
                },
                
                prevImage() {
                    if (this.currentImageIndex > 0) {
                        this.currentImageIndex--;
                    } else {
                        this.currentImageIndex = this.selectedStation.images.length - 1;
                    }
                },
                
                nextImage() {
                    if (this.currentImageIndex < this.selectedStation.images.length - 1) {
                        this.currentImageIndex++;
                    } else {
                        this.currentImageIndex = 0;
                    }
                },
                
                goToStationDetails() {
                    window.location.href = `/super/stations/${this.selectedStation.id}/edit`;
                }
            }
        }
    </script>
</body>
</html>