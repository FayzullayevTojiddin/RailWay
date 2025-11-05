<div>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        body, html {
            margin: 0;
            padding: 0;
            overflow: hidden !important;
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
        }
        
        #map-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: #1a1a1a;
            overflow: hidden;
        }
        
        .map-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .map-wrapper > div {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        #map-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: fill;
            user-select: none;
            pointer-events: none;
            display: block;
        }
        
        #real-map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            display: none;
            z-index: 1;
        }
        
        #real-map.active {
            display: block;
        }
        
        .station-marker {
            position: absolute;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 10;
            transform: translate(-50%, -50%);
        }
        
        
        .station-marker:hover {
            transform: translate(-50%, -50%) scale(1.2);
        }
        
        
        /* Tanlangan marker animatsiyasi */
        .station-marker.station-selected {
            transform: translate(-50%, -50%) scale(1.3);
            z-index: 100;
            animation: pulse-selected 2s infinite;
        }
        
        .station-marker.station-selected:not(.enterprise) {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 1),
                        0 0 20px rgba(16, 185, 129, 0.8),
                        0 6px 30px rgba(16, 185, 129, 0.9);
        }
        
        .station-marker.station-selected.enterprise {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 1),
                        0 0 20px rgba(245, 158, 11, 0.8),
                        0 6px 30px rgba(245, 158, 11, 0.9);
        }
        
        @keyframes pulse-selected {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7),
                            0 0 20px rgba(16, 185, 129, 0.5),
                            0 6px 30px rgba(16, 185, 129, 0.6);
            }
            50% {
                box-shadow: 0 0 0 15px rgba(16, 185, 129, 0),
                            0 0 30px rgba(16, 185, 129, 0.8),
                            0 6px 40px rgba(16, 185, 129, 0.9);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0),
                            0 0 20px rgba(16, 185, 129, 0.5),
                            0 6px 30px rgba(16, 185, 129, 0.6);
            }
        }
        
        .train-animation {
            position: absolute;
            width: 32px;
            height: 32px;
            z-index: 5;
            pointer-events: none;
        }
        
        .train-body {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border-radius: 50%;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .train-body span {
            font-size: 20px;
        }
        
        .map-type-option {
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
            text-align: left;
            background: white;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .map-type-option:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        
        .map-type-option.active {
            border-color: #2563eb;
            background: #dbeafe;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .search-dropdown {
            max-height: 300px;
            overflow-y: auto;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 8px;
        }

        .leaflet-popup-content {
            margin: 8px;
            min-width: 200px;
        }
    </style>

    <div x-data="mapComponent()" x-init="init()" class="fixed inset-0 w-full h-screen overflow-hidden">

        <div id="map-container" :class="{ 'hidden': mapType === 'real' }">
            <div class="map-wrapper">
                <div>
                    <img 
                        id="map-image" 
                        :src="getCurrentMapImage()" 
                        alt="Railway Map"
                    />
                    
                    <template x-if="mapType !== 'real'">
                        <div class="absolute inset-0">
                            <template x-for="station in stations" :key="station.id">
                                <template x-if="station && station.coordinates">
                                    <div 
                                        class="station-marker"
                                        :class="{ 'enterprise': station.type === 'enterprise' }"
                                        :style="`left: ${station.coordinates.x}%; top: ${station.coordinates.y}%;`"
                                        :data-station-id="station.id"
                                        @click="openStationDetails(station)"
                                    >
                                        <img src="/storage/station-icon.png" alt="Station" style="width: 100%; height: 100%; object-fit: contain;" />
                                    </div>
                                </template>
                            </template>
                            
                            <!-- Poyezdlar faqat schematic xaritada -->
                            <template x-if="mapType === 'schematic'">
                                <template x-for="(train, index) in trains" :key="index">
                                    <div 
                                        class="train-animation"
                                        :style="`left: ${train.x}%; top: ${train.y}%; transform: translate(-50%, -50%);`"
                                    >
                                        <div class="train-body">
                                            <span>🚂</span>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div id="real-map" :class="{ 'active': mapType === 'real' }"></div>

        <div class="absolute top-4 right-4 z-[1002] flex flex-col items-end gap-2">
            <button
                @click="toggleFullscreen()"
                class="w-12 h-12 bg-white rounded-lg shadow-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors"
                title="To'liq ekran"
            >
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!isFullscreen">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="isFullscreen" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <button
                @click="showMapSelector = !showMapSelector"
                class="w-12 h-12 bg-white rounded-lg shadow-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors"
                title="Xarita ko'rinishi"
            >
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </button>

            <div class="relative">
                <button
                    @click="showSearchInput = !showSearchInput"
                    class="w-12 h-12 bg-white rounded-lg shadow-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors"
                    x-show="!showSearchInput"
                    title="Qidirish"
                >
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <div 
                    class="relative"
                    x-show="showSearchInput"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    style="display: none;"
                >
                    <input 
                        type="text"
                        x-model="searchQuery"
                        @input="handleSearch()"
                        @focus="showSearchResults = true"
                        placeholder="Stantsiya qidirish..."
                        class="w-80 px-4 py-3 pr-10 bg-white rounded-lg shadow-lg border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-500 font-medium"
                        x-ref="searchInput"
                    />
                    <button 
                        @click="showSearchInput = false; searchQuery = ''; showSearchResults = false;"
                        class="absolute right-3 top-3.5 w-5 h-5 text-gray-400 hover:text-gray-600 cursor-pointer"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div 
                    x-show="showSearchResults && filteredStations.length > 0"
                    @click.away="showSearchResults = false"
                    class="absolute top-full mt-2 w-full bg-white rounded-lg shadow-xl border border-gray-200 search-dropdown"
                    style="display: none;"
                >
                    <template x-for="station in filteredStations" :key="station.id">
                        <div 
                            @click="selectStation(station)"
                            class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                     :class="station.type === 'enterprise' ? 'bg-orange-100' : 'bg-green-100'">
                                    <span style="font-size: 18px;" x-text="station.type === 'enterprise' ? '🏭' : '🚉'"></span>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900" x-text="station.title"></div>
                                    <div class="text-xs text-gray-500" x-text="station.type === 'enterprise' ? 'Korxona' : 'Stantsiya'"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <form method="POST" action="{{ route('filament.super.auth.logout') }}" class="inline-block">
                @csrf
                <button
                    type="submit"
                    class="w-12 h-12 bg-white rounded-lg shadow-lg border border-gray-200 flex items-center justify-center hover:bg-red-50 transition-colors"
                    title="Chiqish"
                >
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>

        <div
            x-show="selectedStation"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform translate-x-full"
            x-transition:enter-end="transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform translate-x-0"
            x-transition:leave-end="transform translate-x-full"
            class="fixed top-0 right-0 h-full w-96 bg-white shadow-2xl z-[1003] flex flex-col"
            style="display: none;"
        >
            <template x-if="selectedStation">
                <div class="flex flex-col h-full">
                    <!-- Tepa qismi - Fixed -->
                    <div class="flex-shrink-0 relative">
                        <button
                            @click="closeStationDetails()"
                            class="absolute top-4 right-4 z-10 w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors"
                        >
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                    <div class="relative bg-gray-200 h-64">
                        <template x-if="selectedStation.images && selectedStation.images.length > 0">
                            <div class="relative h-full">
                                <img 
                                    :src="selectedStation.images[currentImageIndex]" 
                                    class="w-full h-full object-cover"
                                    :alt="'Station image ' + (currentImageIndex + 1)"
                                />
                                
                                <!-- Prev tugma -->
                                <button
                                    @click="prevImage()"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full shadow-lg flex items-center justify-center transition-all"
                                >
                                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                
                                <!-- Next tugma -->
                                <button
                                    @click="nextImage()"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full shadow-lg flex items-center justify-center transition-all"
                                >
                                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                
                                <!-- Rasm counter -->
                                <div class="absolute bottom-2 right-2 px-3 py-1 bg-black/60 text-white text-sm rounded-full">
                                    <span x-text="(currentImageIndex + 1) + ' / ' + selectedStation.images.length"></span>
                                </div>
                            </div>
                        </template>
                        <template x-if="!selectedStation.images || selectedStation.images.length === 0">
                            <div class="h-full flex items-center justify-center text-gray-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </template>
                    </div>

                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2" x-text="selectedStation.title"></h2>
                        
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mb-4"
                             :class="{
                                'bg-blue-100 text-blue-700': selectedStation.type === 'station',
                                'bg-purple-100 text-purple-700': selectedStation.type === 'terminal',
                                'bg-green-100 text-green-700': selectedStation.type === 'junction',
                                'bg-orange-100 text-orange-700': selectedStation.type === 'enterprise'
                             }">
                            <span x-text="getStationType(selectedStation.type)"></span>
                        </div>

                        <p class="text-gray-600 text-sm leading-relaxed mb-6" x-text="selectedStation.description"></p>

                        <template x-if="selectedStation.details">
                            <div class="grid grid-cols-2 gap-3 mb-6">
                                <div class="bg-blue-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Xodimlar soni</div>
                                    <div class="text-xl font-bold text-blue-600" x-text="selectedStation.details.employees || '120'"></div>
                                </div>
                                <div class="bg-green-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Umumiy maydoni</div>
                                    <div class="text-xl font-bold text-green-600" x-text="(selectedStation.details.area || '5000') + ' m²'"></div>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Shaxobcha yo'llari</div>
                                    <div class="text-xl font-bold text-purple-600" x-text="selectedStation.details.branch_tracks || '8'"></div>
                                </div>
                                <div class="bg-orange-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Temir yo'llari</div>
                                    <div class="text-xl font-bold text-orange-600" x-text="selectedStation.details.railway_tracks || '12'"></div>
                                </div>
                            </div>
                        </template>
                        
                        <!-- 360 ko'rish tugmasi -->
                        <button
                            @click="open360View()"
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium py-3 rounded-lg transition-colors flex items-center justify-center gap-2 mb-3"
                            :class="{ 'opacity-50 cursor-not-allowed': !selectedStation.details || !selectedStation.details['360_link'] }"
                            :disabled="!selectedStation.details || !selectedStation.details['360_link']"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>360° ko'rish</span>
                        </button>
                        
                        <!-- Batafsil ma'lumot tugmasi -->
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

        <div class="absolute top-4 right-4 z-[998]">
            <div
                x-show="showMapSelector"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                @click.away="showMapSelector = false"
                class="absolute top-full right-0 mt-2 bg-white rounded-lg shadow-xl p-3 border border-gray-200 w-64"
                style="display: none;"
            >
                <div class="space-y-2">
                    <div 
                        @click="changeMapType('schematic')" 
                        class="map-type-option" 
                        :class="mapType === 'schematic' ? 'active' : ''"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" :class="mapType === 'schematic' ? 'text-blue-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">Sxematik</div>
                            <div class="text-xs text-gray-500">Yo'nalishlar bilan</div>
                        </div>
                        <template x-if="mapType === 'schematic'">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                    </div>

                    <div 
                        @click="changeMapType('simple')" 
                        class="map-type-option" 
                        :class="mapType === 'simple' ? 'active' : ''"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" :class="mapType === 'simple' ? 'text-blue-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">Oddiy</div>
                            <div class="text-xs text-gray-500">Sodda ko'rinish</div>
                        </div>
                        <template x-if="mapType === 'simple'">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                    </div>

                    <div 
                        @click="changeMapType('real')" 
                        class="map-type-option" 
                        :class="mapType === 'real' ? 'active' : ''"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" :class="mapType === 'real' ? 'text-blue-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">Real xarita</div>
                            <div class="text-xs text-gray-500">GPS koordinatalar</div>
                        </div>
                        <template x-if="mapType === 'real'">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function mapComponent() {
            return {
                mapType: 'schematic',
                selectedStation: null,
                currentImageIndex: 0,
                searchQuery: '',
                showSearchResults: false,
                filteredStations: [],
                trains: [],
                trainInterval: null,
                realMap: null,
                mapMarkers: {},
                showMapSelector: false,
                showSearchInput: false,
                isFullscreen: false,
                
                stations: @json($this->getStations()),
                
                mapImages: {
                    schematic: '/storage/railway-map-dark.jpg',
                    simple: '/storage/railway-map-simple.jpg'
                },
                
                init() {
                    console.log('=== FRONTEND DEBUG START ===');
                    console.log('Total stations:', this.stations.length);
                    
                    this.stations.forEach((station, index) => {
                        console.log(`Station ${index + 1}: ${station.title}`);
                        console.log('  - Has coordinates:', !!station.coordinates);
                        console.log('  - Coordinates:', station.coordinates);
                        console.log('  - Has x:', station.coordinates?.x);
                        console.log('  - Has y:', station.coordinates?.y);
                    });
                    console.log('=== DEBUG END ===');
                    
                    console.log('⭐ Stations from backend:', this.stations);
                    console.log('⭐ Stations type:', typeof this.stations);
                    console.log('⭐ Is Array:', Array.isArray(this.stations));
                    console.log('⭐ Stations length:', this.stations?.length);
                    console.log('⭐ Stations JSON:', JSON.stringify(this.stations));
                    
                    this.initTrains();
                    this.startTrainAnimation();
                    
                    // Leaflet uchun DOM tayyor bo'lishi kutiladi
                    setTimeout(() => {
                        this.initRealMap();
                    }, 100);
                    
                    // Fullscreen change event listener
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = !!document.fullscreenElement;
                    });
                },
                
                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {
                            console.error('Fullscreen error:', err);
                        });
                    } else {
                        document.exitFullscreen();
                    }
                },
                
                initRealMap() {
                    try {
                        // Agar allaqachon yaratilgan bo'lsa, qaytish
                        if (this.realMap) {
                            console.log('Real map already initialized');
                            return;
                        }
                        
                        console.log('Initializing real map...');
                        console.log('Leaflet available:', typeof L !== 'undefined');
                        
                        this.realMap = L.map('real-map', {
                            center: [41.2995, 69.2401],
                            zoom: 6,
                            zoomControl: true
                        });

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                        maxZoom: 19
                    }).addTo(this.realMap);

                        this.addMarkersToRealMap();
                        console.log('Real map initialized successfully');
                    } catch (error) {
                        console.error('Error initializing real map:', error);
                    }
                },

                addMarkersToRealMap() {
                    this.stations.forEach(station => {
                        if (station.location && station.location.lat && station.location.lng) {
                            const markerColor = station.type === 'enterprise' ? 'orange' : 'blue';
                            
                            const customIcon = L.icon({
                                iconUrl: '/storage/station-icon.png',
                                iconSize: [50, 50],
                                iconAnchor: [25, 50],
                                popupAnchor: [0, -50]
                            });

                            const marker = L.marker([station.location.lat, station.location.lng], {
                                icon: customIcon
                            }).addTo(this.realMap);

                            const popupContent = `
                                <div class="p-2">
                                    <h3 class="font-bold text-base mb-1">${station.title}</h3>
                                    <p class="text-xs text-gray-600 mb-2">${station.description}</p>
                                    <button 
                                        onclick="window.mapComponentInstance.openStationDetailsFromMap(${station.id})"
                                        class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors"
                                    >
                                        Batafsil
                                    </button>
                                </div>
                            `;
                            marker.bindPopup(popupContent);

                            marker.on('click', () => {
                                this.openStationDetails(station);
                            });

                            this.mapMarkers[station.id] = marker;
                        }
                    });

                    window.mapComponentInstance = this;
                },

                openStationDetailsFromMap(stationId) {
                    const station = this.stations.find(s => s.id === stationId);
                    if (station) {
                        this.openStationDetails(station);
                    }
                },
                
                initTrains() {
                    this.trains = [];
                    
                    if (!this.stations || this.stations.length < 5) {
                        console.warn('Not enough stations to initialize trains');
                        return;
                    }
                    
                    // Marshrut 1: Kelif -> RZD -> Surxonobod -> Boldir -> Sherobod -> Naushaxar -> Uchqizil -> Termiz
                    const route1Stations = ['Kelif', 'RZD', 'Surxonobod', 'Boldir', 'Werobod', 'Naushaxar', 'Uchqizil', 'Termez'];
                    const route1 = route1Stations
                        .map(name => this.stations.findIndex(s => s.title === name))
                        .filter(idx => idx !== -1);
                    
                    // Marshrut 2: Termez -> Baqtriya -> Jarqorgon -> Zartepa -> Surxan -> Qumqorgon -> Elbayon -> Shorchi -> Xayrabod -> Danau -> Sariasiya -> Quduqli
                    const route2Stations = ['Termez', 'Baqtriya', 'Jarqorgon', 'Zartepa', 'Surxan', 'Qumqorgon', 'Elbayon', 'Shorchi', 'Xayrabod', 'Danau', 'Sariasiya', 'Quduqli'];
                    const route2 = route2Stations
                        .map(name => this.stations.findIndex(s => s.title === name))
                        .filter(idx => idx !== -1);
                    
                    // Marshrut 3: Qumqorgon -> Aqdajar -> Tangimush -> Pulxakim -> Boysun -> Darband -> Shurab -> Aknazar
                    const route3Stations = ['Qumqorgon', 'Aqdajar', 'Tangimush', 'Pulxakim', 'Boysun', 'Darband', 'Shurab', 'Aknazar'];
                    const route3 = route3Stations
                        .map(name => this.stations.findIndex(s => s.title === name))
                        .filter(idx => idx !== -1);
                    
                    const routes = [];
                    
                    // Har bir marshrut uchun faqat 1 ta poyezd
                    if (route1.length >= 3) {
                        routes.push(route1);
                    }
                    
                    if (route2.length >= 3) {
                        routes.push(route2);
                    }
                    
                    if (route3.length >= 3) {
                        routes.push(route3);
                    }
                    
                    routes.forEach((route, i) => {
                        const startStation = this.stations[route[0]];
                        
                        this.trains.push({
                            x: startStation.coordinates.x,
                            y: startStation.coordinates.y,
                            rotation: 0,
                            route: route,
                            currentRouteIndex: 0,
                            direction: 1, // 1 = oldinga, -1 = orqaga
                            speed: 0.05 + (i * 0.01)
                        });
                    });
                    
                    console.log('Trains initialized:', this.trains.length, 'trains on', routes.length / 2, 'routes');
                },
                
                startTrainAnimation() {
                    this.trainInterval = setInterval(() => {
                        if (this.mapType === 'schematic' && this.trains.length > 0) {
                            this.trains.forEach(train => {
                                const targetStationIndex = train.route[train.currentRouteIndex];
                                const targetStation = this.stations[targetStationIndex];
                                
                                if (!targetStation || !targetStation.coordinates) {
                                    console.warn('Invalid target station coordinates');
                                    return;
                                }
                                
                                const dx = targetStation.coordinates.x - train.x;
                                const dy = targetStation.coordinates.y - train.y;
                                const distance = Math.sqrt(dx * dx + dy * dy);
                                
                                if (distance < 1) {
                                    train.currentRouteIndex += train.direction;
                                    
                                    // Oxiriga yetganda yo'nalishni o'zgartirish
                                    if (train.currentRouteIndex >= train.route.length) {
                                        train.currentRouteIndex = train.route.length - 2;
                                        train.direction = -1; // orqaga
                                    } else if (train.currentRouteIndex < 0) {
                                        train.currentRouteIndex = 1;
                                        train.direction = 1; // oldinga
                                    }
                                } else {
                                    train.x += (dx / distance) * train.speed;
                                    train.y += (dy / distance) * train.speed;
                                }
                            });
                        }
                    }, 50);
                },
                
                getCurrentMapImage() {
                    return this.mapImages[this.mapType] || this.mapImages.schematic;
                },
                
                changeMapType(type) {
                    this.mapType = type;
                    this.showMapSelector = false;
                    
                    if (type === 'real' && this.realMap) {
                        setTimeout(() => {
                            this.realMap.invalidateSize();
                        }, 300);
                    }
                },

                getMapTypeName(type) {
                    const names = {
                        'schematic': 'Sxematik',
                        'simple': 'Oddiy',
                        'real': 'Real xarita'
                    };
                    return names[type] || 'Sxematik';
                },
                
                handleSearch() {
                    if (this.searchQuery.trim() === '') {
                        this.filteredStations = [];
                        return;
                    }
                    
                    const query = this.searchQuery.toLowerCase();
                    this.filteredStations = this.stations.filter(station => 
                        station.title.toLowerCase().includes(query)
                    );
                },
                
                selectStation(station) {
                    this.searchQuery = '';
                    this.showSearchResults = false;
                    this.filteredStations = [];
                    
                    if (this.mapType === 'real' && this.realMap && station.location) {
                        this.realMap.setView([station.location.lat, station.location.lng], 14);
                        
                        if (this.mapMarkers[station.id]) {
                            this.mapMarkers[station.id].openPopup();
                        }
                    }
                    
                    this.openStationDetails(station);
                    this.highlightSelectedStation(station.id);
                },
                
                openStationDetails(station) {
                    this.selectedStation = station;
                    this.currentImageIndex = 0;
                    this.highlightSelectedStation(station.id);
                },
                
                highlightSelectedStation(stationId) {
                    const allMarkers = document.querySelectorAll('.station-marker');
                    allMarkers.forEach(marker => {
                        marker.classList.remove('station-selected');
                    });
                    
                    const selectedMarker = document.querySelector(`.station-marker[data-station-id="${stationId}"]`);
                    if (selectedMarker) {
                        selectedMarker.classList.add('station-selected');
                    }
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
                
                getStationType(type) {
                    const types = {
                        'station': 'Stantsiya',
                        'terminal': 'Terminal',
                        'junction': 'Tutashuv',
                        'enterprise': 'Korxona'
                    };
                    return types[type] || 'Stantsiya';
                },
                
                goToStationDetails() {
                    window.location.href = `/super/stations/${this.selectedStation.id}`;
                },

                open360View() {
                    if (this.selectedStation && this.selectedStation.details && this.selectedStation.details['360_link']) {
                        window.open(this.selectedStation.details['360_link'], '_blank');
                    }
                },

                openImageViewer(index) {
                    this.currentImageIndex = index;
                }
            }
        }
    </script>
</div>