<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temir yo'l xaritasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/panzoom/9.4.3/panzoom.min.js"></script>
    <style>
        .station-marker {
            position: absolute;
            transform: translate(-50%, -50%);
            transition: all 0.2s ease;
            cursor: pointer;
            z-index: 100;
        }
        .station-marker:hover {
            transform: translate(-50%, -50%) scale(1.3);
            z-index: 200;
        }
        .station-popup {
            position: fixed;
            background: white;
            padding: 14px 18px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            min-width: 220px;
            z-index: 1000;
            pointer-events: none;
            border: 1px solid #e5e7eb;
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
        #map-scene {
            width: 100%;
            height: 100%;
            cursor: grab;
        }
        #map-scene:active {
            cursor: grabbing;
        }
    </style>
</head>
<body class="overflow-hidden">
    <div x-data="{
        showSettings: false,
        mapType: 'light',
        filters: {
            station: true
        },
        panzoomInstance: null,
        currentZoom: 1,
        hoveredStation: null,
        popupX: 0,
        popupY: 0,
        
        stations: @js($this->getStations()),
        
        mapUrls: {
            light: '{{ asset('storage/railway-map-light.png') }}',
            dark: '{{ asset('storage/railway-map-dark.png') }}',
            satellite: '{{ asset('storage/railway-map-satellite.png') }}'
        },
        
        get currentMapUrl() {
            return this.mapUrls[this.mapType] || this.mapUrls.light;
        },
        
        get filteredStations() {
            return this.stations.filter(station => {
                return this.filters[station.type] === true;
            });
        },
        
        getStationIcon(type) {
            const icons = {
                station: 'https://railmap.das-uty.uz/assets/Train-77ea8bfb.svg'
            };
            return icons[type] || 'https://railmap.das-uty.uz/assets/Train-77ea8bfb.svg';
        },
        
        getMarkerSize() {
            // Zoom qanchalik kichik bo'lsa, marker shunchalik katta bo'ladi
            const baseSize = 32;
            const minSize = 32;
            const maxSize = 48;
            
            if (this.currentZoom < 1) {
                return Math.min(maxSize, baseSize / this.currentZoom);
            }
            return minSize;
        },
        
        initPanzoom() {
            const elem = document.getElementById('map-content');
            if (!elem || this.panzoomInstance) return;
            
            this.panzoomInstance = Panzoom(elem, {
                maxScale: 3,
                minScale: 0.5,
                step: 0.1,
                startScale: 1,
                startX: 0,
                startY: 0,
                contain: false,
                cursor: 'grab'
            });
            
            // Zoom o'zgarganda event listener
            elem.addEventListener('panzoomzoom', (event) => {
                this.currentZoom = event.detail.scale;
            });
            
            // Mouse wheel zoom
            const parent = elem.parentElement;
            parent.addEventListener('wheel', (event) => {
                if (!this.panzoomInstance) return;
                event.preventDefault();
                this.panzoomInstance.zoomWithWheel(event);
            });
        },
        
        zoomIn() {
            if (this.panzoomInstance) {
                this.panzoomInstance.zoomIn();
            }
        },
        
        zoomOut() {
            if (this.panzoomInstance) {
                this.panzoomInstance.zoomOut();
            }
        },
        
        resetZoom() {
            if (this.panzoomInstance) {
                this.panzoomInstance.reset();
                this.currentZoom = 1;
            }
        },
        
        showStationPopup(station, e) {
            this.hoveredStation = station;
            this.popupX = e.clientX;
            this.popupY = e.clientY;
        },
        
        hideStationPopup() {
            this.hoveredStation = null;
        },
        
        goToPanel() {
            window.location.href = '{{ route('filament.super.resources.stations.index') }}';
        }
    }" 
    x-init="
        $nextTick(() => { initPanzoom(); });
        $watch('mapType', value => { 
            if (panzoomInstance) {
                resetZoom();
            }
        });
    "
    class="relative w-full h-screen bg-gray-100 overflow-hidden">

        <!-- Map Container -->
        <div id="map-scene" class="absolute inset-0 overflow-hidden">
            <div id="map-content" class="inline-block">
                <div class="relative" style="width: 1200px;">
                    <img 
                        :src="currentMapUrl"
                        alt="Railway Map"
                        class="select-none w-full h-auto"
                        draggable="false"
                    />
                    
                    <!-- Station Markers -->
                    <template x-for="station in filteredStations" :key="station.id">
                        <div 
                            class="station-marker"
                            :style="`left: ${station.coordinates.x}%; top: ${station.coordinates.y}%; width: ${getMarkerSize()}px; height: ${getMarkerSize()}px;`"
                            @mouseenter="showStationPopup(station, $event)"
                            @mouseleave="hideStationPopup()"
                        >
                            <img 
                                :src="getStationIcon(station.type)"
                                :alt="station.title"
                                class="w-full h-full drop-shadow-lg"
                            />
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Station Popup -->
        <div 
            x-show="hoveredStation"
            class="station-popup"
            :style="`left: ${popupX + 15}px; top: ${popupY - 70}px;`"
            style="display: none;"
        >
            <div class="font-semibold text-gray-900 text-base mb-1.5" x-text="hoveredStation?.title"></div>
            <div class="text-xs text-gray-500">
                Turi: <span class="font-medium text-gray-700" x-text="hoveredStation?.type"></span>
            </div>
        </div>

        <!-- Top Right - Panel Button -->
        <div class="absolute top-4 right-4 z-10">
            <button
                @click="goToPanel()"
                class="bg-white rounded-lg shadow-md px-4 py-2 border border-gray-200 flex items-center gap-2 hover:bg-gray-50 transition-all duration-150 hover:shadow-lg active:scale-95"
            >
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="font-medium text-sm text-gray-700">Panelga o'tish</span>
            </button>
        </div>

        <!-- Bottom Left - Map Type Button -->
        <div class="absolute bottom-4 left-4 z-10">
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
                    <button
                        @click="showSettings = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <!-- Oddiy -->
                    <div 
                        @click="mapType = 'light'"
                        class="map-type-option"
                        :class="mapType === 'light' ? 'active' : ''"
                    >
                        <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center relative">
                            <svg class="w-12 h-12 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <div class="absolute bottom-2 left-2 right-2 text-center">
                                <span class="text-xs font-medium text-blue-900 bg-white/90 px-2 py-1 rounded">Oddiy</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sputnik -->
                    <div 
                        @click="mapType = 'satellite'"
                        class="map-type-option"
                        :class="mapType === 'satellite' ? 'active' : ''"
                    >
                        <div class="w-full h-full bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center relative">
                            <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                            </svg>
                            <div class="absolute bottom-2 left-2 right-2 text-center">
                                <span class="text-xs font-medium text-green-900 bg-white/90 px-2 py-1 rounded">Sputnik</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tungi -->
                    <div 
                        @click="mapType = 'dark'"
                        class="map-type-option"
                        :class="mapType === 'dark' ? 'active' : ''"
                    >
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

                <!-- Filters Section -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Xaritada ko'rsatish</h4>
                    <div class="space-y-2">
                        <template x-for="(value, key) in filters" :key="key">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900 capitalize" x-text="key"></span>
                                <input 
                                    type="checkbox" 
                                    x-model="filters[key]"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                />
                            </label>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Right - Zoom Controls -->
        <div class="absolute bottom-4 right-4 z-10 flex items-center gap-2 bg-white rounded-lg shadow-md border border-gray-200 p-1">
            <button
                @click="zoomOut()"
                class="w-8 h-8 rounded flex items-center justify-center font-semibold text-lg text-gray-700 hover:bg-gray-100 active:scale-95 transition-all duration-150"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
            </button>
            
            <button
                @click="resetZoom()"
                class="px-3 py-1 min-w-[60px] text-center hover:bg-gray-100 rounded transition-colors"
            >
                <span class="text-xs font-medium text-gray-700" x-text="Math.round(currentZoom * 100) + '%'"></span>
            </button>

            <button
                @click="zoomIn()"
                class="w-8 h-8 rounded flex items-center justify-center font-semibold text-lg text-gray-700 hover:bg-gray-100 active:scale-95 transition-all duration-150"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        </div>

    </div>
</body>
</html>