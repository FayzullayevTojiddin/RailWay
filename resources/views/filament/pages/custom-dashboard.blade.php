<div>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        body, html {
            margin: 0;
            padding: 0;
            overflow: hidden;
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
            width: 30px;
            height: 30px;
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
            width: 34px;
            height: 34px;
            z-index: 5;
            pointer-events: none;
            transform: translate(-50%, -50%);
        }

        .train-body {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .train-body img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .train-body.electric {
            border: 2px solid rgba(16,185,129,0.4);
        }

        .train-body.diesel {
            border: 2px solid rgba(245,158,11,0.4);
        }

        .station-marker.enterprise {
            width: 40px;
            height: 40px;
        }

        .station-marker.enterprise img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .station-marker.station-selected.enterprise {
            transform: translate(-50%, -50%) scale(1.3);
            z-index: 110;
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

        .voice-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .voice-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .voice-modal {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 1200px;
            max-height: 85vh;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: row;
        }

        .voice-modal-images {
            flex: 1;
            background: #f3f4f6;
            position: relative;
            min-width: 0;
        }

        .voice-modal-images img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .voice-modal-content {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .voice-modal-text {
            font-size: 18px;
            line-height: 1.8;
            color: #1f2937;
        }

        .voice-modal-text .word {
            display: inline;
            transition: all 0.3s ease;
            padding: 2px 4px;
            border-radius: 4px;
        }

        .voice-modal-text .word.active {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            font-weight: 600;
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
        }

        .voice-modal-text .word.spoken {
            color: #6b7280;
            opacity: 0.7;
        }

        .voice-waveform {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            margin-top: 30px;
        }

        .voice-waveform span {
            width: 4px;
            background: linear-gradient(to top, #3b82f6, #8b5cf6);
            border-radius: 2px;
            animation: wave 0.8s ease-in-out infinite;
        }

        .voice-waveform span:nth-child(1) { height: 20px; animation-delay: 0s; }
        .voice-waveform span:nth-child(2) { height: 30px; animation-delay: 0.1s; }
        .voice-waveform span:nth-child(3) { height: 40px; animation-delay: 0.2s; }
        .voice-waveform span:nth-child(4) { height: 35px; animation-delay: 0.3s; }
        .voice-waveform span:nth-child(5) { height: 25px; animation-delay: 0.4s; }

        @keyframes wave {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(0.5); }
        }

        .image-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 10;
        }

        .image-nav-btn:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
        }

        .image-nav-btn.prev { left: 15px; }
        .image-nav-btn.next { right: 15px; }

        .image-counter {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
        }

        /* Loading Animation Styles */
        .loading-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(15px);
            z-index: 2500;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .loading-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .loading-container {
            text-align: center;
            color: white;
        }

        .loading-spinner {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            position: relative;
        }

        .loading-spinner::before,
        .loading-spinner::after {
            content: '';
            position: absolute;
            border-radius: 50%;
        }

        .loading-spinner::before {
            width: 80px;
            height: 80px;
            border: 4px solid rgba(59, 130, 246, 0.2);
            border-top-color: #3b82f6;
            animation: spin 1s linear infinite;
        }

        .loading-spinner::after {
            width: 60px;
            height: 60px;
            top: 10px;
            left: 10px;
            border: 4px solid rgba(139, 92, 246, 0.2);
            border-top-color: #8b5cf6;
            animation: spin 0.7s linear infinite reverse;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }

        .loading-dots span {
            width: 12px;
            height: 12px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 50%;
            animation: bounce 1.4s ease-in-out infinite;
        }

        .loading-dots span:nth-child(1) { animation-delay: -0.32s; }
        .loading-dots span:nth-child(2) { animation-delay: -0.16s; }
        .loading-dots span:nth-child(3) { animation-delay: 0s; }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        .loading-text {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 3s ease infinite;
        }

        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .loading-subtext {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.7);
        }

        .loading-progress {
            width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            margin: 25px auto 0;
            overflow: hidden;
            position: relative;
        }

        .loading-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
            background-size: 200% 100%;
            animation: progress-slide 1.5s ease-in-out infinite;
            border-radius: 2px;
        }

        @keyframes progress-slide {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(200%);
            }
        }

        /* Success Animation - Yangilangan */
        .success-animation {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            position: relative;
            animation: scale-up 0.5s ease-out;
        }

        @keyframes scale-up {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            animation: pulse-circle 1.5s ease-out;
        }

        @keyframes pulse-circle {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }
            70% {
                box-shadow: 0 0 0 30px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .success-checkmark {
            width: 70px;
            height: 70px;
            position: relative;
        }

        .checkmark-stem {
            position: absolute;
            width: 8px;
            height: 40px;
            background: white;
            left: 35px;
            top: 15px;
            border-radius: 4px;
            transform-origin: bottom left;
            transform: rotate(45deg) scaleY(0);
            animation: draw-stem 0.4s ease-out 0.5s forwards;
        }

        .checkmark-kick {
            position: absolute;
            width: 22px;
            height: 8px;
            background: white;
            left: 15px;
            top: 43px;
            border-radius: 4px;
            transform-origin: left bottom;
            transform: rotate(-45deg) scaleX(0);
            animation: draw-kick 0.3s ease-out 0.3s forwards;
        }

        @keyframes draw-stem {
            to {
                transform: rotate(45deg) scaleY(1);
            }
        }

        @keyframes draw-kick {
            to {
                transform: rotate(-45deg) scaleX(1);
            }
        }

        .success-text {
            font-size: 28px;
            font-weight: 700;
            color: #10b981;
            margin-top: 20px;
            animation: fade-in 0.5s ease-out 0.9s both;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                                        :class="{ 'enterprise': station.type && station.type.startsWith('enterprise_') }"
                                        :style="`left: ${station.coordinates.x}%; top: ${station.coordinates.y}%;`"
                                        :data-station-id="station.id"
                                        @click="openStationDetails(station)"
                                    >
                                        <img :src="getStationIcon(station.type)" :alt="station.type" style="width: 100%; height: 100%; object-fit: contain;" />
                                    </div>
                                </template>
                            </template>
                            
                            <template x-if="mapType === 'schematic'">
                                <template x-for="(train, index) in trains" :key="index">
                                    <div 
                                        class="train-animation"
                                        :style="`left: ${train.x}%; top: ${train.y}%;`"
                                    >
                                        <div class="train-body" :class="train.type">
                                            <img :src="getTrainIcon(train.type)" :alt="train.type + ' train'"/>
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
                                        x-on:error="$el.src = 'https://via.placeholder.com/800x400?text=Image+Not+Found'"
                                    />
                                    
                                    <button
                                        @click="prevImage()"
                                        class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full shadow-lg flex items-center justify-center transition-all"
                                    >
                                        <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>
                                    
                                    <button
                                        @click="nextImage()"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full shadow-lg flex items-center justify-center transition-all"
                                    >
                                        <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                    
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
                                        <div class="text-xl font-bold text-blue-600" x-text="selectedStation.details.employees"></div>
                                    </div>
                                    <div class="bg-green-50 rounded-lg p-3">
                                        <div class="text-xs text-gray-500 mb-1">Umumiy maydoni</div>
                                        <div class="text-xl font-bold text-green-600" x-text="selectedStation.details.area + ' m²'"></div>
                                    </div>
                                    <div class="bg-purple-50 rounded-lg p-3">
                                        <div class="text-xs text-gray-500 mb-1">Shaxobcha yo'llari</div>
                                        <div class="text-xl font-bold text-purple-600" x-text="selectedStation.details.branch_tracks"></div>
                                    </div>
                                </div>
                            </template>
                            
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

        <!-- Voice Assistant -->
        <div x-data="voiceAssistant()" class="fixed bottom-6 right-6 z-[1004]">
            <div class="loading-backdrop" :class="{ 'active': isProcessing }">
                <div class="loading-container">
                    <template x-if="!showSuccess">
                        <div>
                            <div class="loading-spinner"></div>
                            <h3 class="loading-text">AI javob tayyorlayapti...</h3>
                            <p class="loading-subtext">Iltimos kuting</p>
                            <div class="loading-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <div class="loading-progress">
                                <div class="loading-progress-bar"></div>
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="showSuccess">
                        <div>
                            <div class="success-animation">
                                <div class="success-circle">
                                    <div class="success-checkmark">
                                        <div class="checkmark-kick"></div>
                                        <div class="checkmark-stem"></div>
                                    </div>
                                </div>
                            </div>
                            <h3 class="success-text">Tayyor!</h3>
                        </div>
                    </template>
                </div>
            </div>

            <button 
                @click="toggleVoice()"
                class="w-16 h-16 rounded-full shadow-2xl flex items-center justify-center text-white transition-all duration-300"
                :class="{
                    'bg-gradient-to-r from-red-600 to-pink-600 animate-pulse scale-110': isListening,
                    'bg-gradient-to-r from-green-600 to-emerald-600 animate-pulse': isSpeaking,
                    'bg-gradient-to-r from-blue-600 to-indigo-600 hover:scale-110': !isListening && !isSpeaking
                }"
                :title="isListening ? 'Eshityapman...' : (isSpeaking ? 'Gapiryapman...' : 'AI bilan gaplashish')"
            >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                </svg>
            </button>

            <div class="voice-backdrop" :class="{ 'active': isSpeaking }">
                <div class="voice-modal" @click.stop>
                    <div class="voice-modal-images">
                        <template x-if="currentResponse && currentStationImages && currentStationImages.length > 0">
                            <div class="relative w-full h-full">
                                <img :src="currentStationImages[currentImageIndex]" alt="Station Image" class="w-full h-full object-cover">
                                
                                <template x-if="currentStationImages.length > 1">
                                    <div>
                                        <button @click="prevModalImage()" class="image-nav-btn prev">
                                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>
                                        
                                        <button @click="nextModalImage()" class="image-nav-btn next">
                                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>

                                        <div class="image-counter">
                                            <span x-text="(currentImageIndex + 1) + ' / ' + currentStationImages.length"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        
                        <template x-if="!currentResponse || !currentStationImages || currentStationImages.length === 0">
                            <div class="flex items-center justify-center w-full h-full">
                                <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </template>
                    </div>

                    <div class="voice-modal-content">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-900">
                                <template x-if="currentResponse && currentResponse.intent">
                                    <span x-text="currentResponse.intent.title"></span>
                                </template>
                                <template x-if="!currentResponse || !currentResponse.intent">
                                    <span>AI Javob</span>
                                </template>
                            </h3>
                            <button @click="stopAll()" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="voice-modal-text flex-1">
                            <template x-if="currentResponse && currentResponse.response_text">
                                <p>
                                    <template x-for="(word, index) in getWords()" :key="index">
                                        <span 
                                            class="word"
                                            :class="{
                                                'active': currentWordIndex === index,
                                                'spoken': currentWordIndex > index
                                            }"
                                            x-text="word + ' '"
                                        ></span>
                                    </template>
                                </p>
                            </template>
                            <template x-if="!currentResponse || !currentResponse.response_text">
                                <p class="text-gray-400">Javob yuklanmoqda...</p>
                            </template>
                        </div>

                        <div class="voice-waveform">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
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
                    this.initTrains();
                    this.startTrainAnimation();
                    
                    setTimeout(() => {
                        this.initRealMap();
                    }, 100);
                    
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = !!document.fullscreenElement;
                    });

                    window.mapStations = this.stations;
                },
                
                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {});
                    } else {
                        document.exitFullscreen();
                    }
                },
                
                initRealMap() {
                    try {
                        if (this.realMap) return;
                        
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
                    } catch (error) {}
                },

                addMarkersToRealMap() {
                    Object.values(this.mapMarkers).forEach(m => {
                        try { this.realMap.removeLayer(m); } catch(e) {}
                    });
                    this.mapMarkers = {};

                    this.stations.forEach(station => {
                        if (station.location && station.location.lat && station.location.lng) {
                            const iconUrl = this.getStationIcon(station.type);
                            const isEnterprise = station.type && station.type.startsWith('enterprise_');
                            const sizePx = 50;
                            const anchorX = Math.round(sizePx / 2);
                            const anchorY = sizePx;

                            const customIcon = L.divIcon({
                                className: 'custom-station-divicon',
                                html: `<img class="marker-img ${isEnterprise ? 'marker-enterprise' : ''}" src="${iconUrl}" alt="${station.title}" style="width:${sizePx}px;height:${sizePx}px;display:block;object-fit:contain;" />`,
                                iconSize: [sizePx, sizePx],
                                iconAnchor: [anchorX, anchorY],
                                popupAnchor: [0, -anchorY]
                            });

                            const marker = L.marker([station.location.lat, station.location.lng], {
                                icon: customIcon
                            }).addTo(this.realMap);

                            setTimeout(() => {
                                const el = marker.getElement();
                                if (el) {
                                    const img = el.querySelector('img.marker-img');
                                    if (img) {
                                        img.style.width = sizePx + 'px';
                                        img.style.height = sizePx + 'px';
                                        img.style.objectFit = 'contain';
                                        img.style.display = 'block';
                                    }
                                }
                            }, 20);

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
                    if (!this.stations || this.stations.length < 5) return;

                    const route1Stations = ['Quduqli', 'Sariosiyo', 'Denov', 'Xayrabod', "Sho'rchi", 'Elbayon', "Qumqo'rg'on"];
                    const route1 = route1Stations.map(name => this.stations.findIndex(s => s.title === name)).filter(idx => idx !== -1);

                    const route2Stations = ['Oqnazar', "Sho'rob", 'PCH-15', 'Darband', 'Boysun', 'Pulhakim', 'Tangimush', 'Oqjar', "Qumqo'rg'on", 'Surxon', 'Zartepa', "Jarqo'rg'on", 'Baktriya', 'Termiz'];
                    const route2 = route2Stations.map(name => this.stations.findIndex(s => s.title === name)).filter(idx => idx !== -1);

                    const route3Stations = ['Surxonobod', 'Boldir', 'Sherabod', 'Naushaxar', 'Uchqizil', 'Termiz'];
                    const route3 = route3Stations.map(name => this.stations.findIndex(s => s.title === name)).filter(idx => idx !== -1);

                    const routes = [];
                    const trainTypes = [];

                    if (route1.length >= 3) {
                        routes.push(route1);
                        trainTypes.push('diesel');
                    }

                    if (route2.length >= 3) {
                        routes.push(route2);
                        trainTypes.push('electric');
                    }

                    if (route3.length >= 3) {
                        routes.push(route3);
                        trainTypes.push('diesel');
                    }

                    routes.forEach((route, i) => {
                        const startStation = this.stations[route[0]];
                        if (!startStation || !startStation.coordinates) return;

                        this.trains.push({
                            x: startStation.coordinates.x,
                            y: startStation.coordinates.y,
                            rotation: 0,
                            route: route,
                            currentRouteIndex: 0,
                            direction: 1,
                            speed: 0.05 + (i * 0.01),
                            type: trainTypes[i]
                        });
                    });
                },
                
                getTrainIcon(type) {
                    const icons = {
                        'electric': '/storage/train-electric.png',
                        'diesel': '/storage/train-diesel.png'
                    };
                    return icons[type] || '/storage/train-icon.png';
                },

                startTrainAnimation() {
                    this.trainInterval = setInterval(() => {
                        if (this.mapType === 'schematic' && this.trains.length > 0) {
                            this.trains.forEach(train => {
                                const targetStationIndex = train.route[train.currentRouteIndex];
                                const targetStation = this.stations[targetStationIndex];
                                
                                if (!targetStation || !targetStation.coordinates) return;
                                
                                const dx = targetStation.coordinates.x - train.x;
                                const dy = targetStation.coordinates.y - train.y;
                                const distance = Math.sqrt(dx * dx + dy * dy);
                                
                                if (distance < 1) {
                                    const nextIndex = train.currentRouteIndex + train.direction;

                                    if (nextIndex >= train.route.length) {
                                        train.currentRouteIndex = train.route.length - 2;
                                        train.direction = -1;
                                    } else if (nextIndex < 0) {
                                        train.currentRouteIndex = 1;
                                        train.direction = 1;
                                    } else {
                                        train.currentRouteIndex = nextIndex;
                                    }

                                    const arrivedStation = this.stations[train.route[train.currentRouteIndex]];
                                    if (train.type === 'electric' && arrivedStation && arrivedStation.title === 'Oqnazar') {
                                        train.direction = -1;
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
                    if (type?.startsWith('enterprise_')) {
                        return 'Korxona';
                    }
                    const types = {
                        'big_station': 'Katta Stantsiya',
                        'small_station': 'Kichik Stantsiya',
                        'bridge': "Ko'prik"
                    };
                    return types[type] || 'Stantsiya';
                },

                getStationIcon(type) {
                    if (type == 'enterprise_rju') return '/storage/mtu-icon.png';
                    if (type?.startsWith('enterprise_')) {
                        return '/storage/enterprise-icon.png';
                    }

                    const icons = {
                        'big_station': '/storage/big-station-icon.png',
                        'small_station': '/storage/small-station-icon.png',
                        'bridge': '/storage/bridge-icon.png',
                    };

                    return icons[type] || '/storage/big-station-icon.png';
                },
                
                goToStationDetails() {
                    window.location.href = `/super/stations/${this.selectedStation.id}`;
                },

                open360View() {
                    if (this.selectedStation && this.selectedStation.details && this.selectedStation.details['360_link']) {
                        window.open(this.selectedStation.details['360_link'], '_blank');
                    }
                }
            }
        }
        
        function voiceAssistant() {
            return {
                isListening: false,
                isSpeaking: false,
                isProcessing: false,
                showSuccess: false,
                mediaRecorder: null,
                audioChunks: [],
                audioElement: null,
                currentResponse: null,
                currentImageIndex: 0,
                currentStationImages: [],
                carouselInterval: null,
                currentWordIndex: -1,
                wordTrackingInterval: null,
                words: [],
                
                toggleVoice() {
                    if (this.isListening || this.isSpeaking) {
                        this.stopAll();
                    } else {
                        this.startListening();
                    }
                },
                
                getWords() {
                    if (!this.currentResponse || !this.currentResponse.response_text) {
                        return [];
                    }
                    if (this.words.length === 0) {
                        this.words = this.currentResponse.response_text.split(' ').filter(w => w.trim() !== '');
                    }
                    return this.words;
                },
                
                stopAll() {
                    this.isListening = false;
                    this.isProcessing = false;
                    this.showSuccess = false;
                    
                    // Word tracking intervalini to'xtatish
                    if (this.wordTrackingInterval) {
                        clearInterval(this.wordTrackingInterval);
                        this.wordTrackingInterval = null;
                    }
                    
                    // Carousel intervalini to'xtatish
                    if (this.carouselInterval) {
                        clearInterval(this.carouselInterval);
                        this.carouselInterval = null;
                    }
                    
                    if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
                        this.mediaRecorder.stop();
                    }
                    
                    if (this.audioElement) {
                        this.audioElement.pause();
                        this.audioElement = null;
                    }
                    
                    this.isSpeaking = false;
                    this.currentResponse = null;
                    this.currentImageIndex = 0;
                    this.currentStationImages = [];
                    this.currentWordIndex = -1;
                    this.words = [];
                },
                
                async startListening() {
                    try {
                        this.isListening = true;
                        
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        
                        this.audioChunks = [];
                        this.mediaRecorder = new MediaRecorder(stream);
                        
                        this.mediaRecorder.ondataavailable = (event) => {
                            this.audioChunks.push(event.data);
                        };
                        
                        this.mediaRecorder.onstop = async () => {
                            const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                            
                            this.isListening = false;
                            this.isProcessing = true;
                            this.showSuccess = false;
                            
                            try {
                                const response = await this.sendAudioToBackend(audioBlob);
                                
                                if (response.success) {
                                    this.currentResponse = response;
                                    this.currentImageIndex = 0;
                                    
                                    this.loadStationImages(response);

                                    if (response.audio && response.audio.remote_url) {
                                        this.showSuccessAndPlay(response.audio.remote_url);
                                    } else if (response.task_id) {
                                        this.pollTtsStatus(response.task_id);
                                    } else {
                                        this.isProcessing = false;
                                    }
                                } else {
                                    this.isProcessing = false;
                                }
                            } catch (error) {
                                console.error('Audio backend error:', error);
                                this.isProcessing = false;
                            }
                            
                            stream.getTracks().forEach(track => track.stop());
                        };
                        
                        this.mediaRecorder.start();
                        
                        setTimeout(() => {
                            if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
                                this.mediaRecorder.stop();
                            }
                        }, 5000);
                        
                    } catch (error) {
                        console.error('Microphone error:', error);
                        this.isListening = false;
                        this.isProcessing = false;
                        alert('Mikrofonga ruxsat berilmadi. Iltimos brauzer sozlamalarini tekshiring.');
                    }
                },

                loadStationImages(response) {
                    this.currentStationImages = [];
                    
                    if (response.images && Array.isArray(response.images) && response.images.length > 0) {
                        this.currentStationImages = response.images;
                        console.log('Loaded images from response:', this.currentStationImages);
                        
                        // Auto carousel ni boshlash
                        this.startCarousel();
                    } else {
                        console.log('No images in response');
                    }
                },
                
                // Yangi metod - auto carousel
                startCarousel() {
                    // Oldingi intervalni tozalash
                    if (this.carouselInterval) {
                        clearInterval(this.carouselInterval);
                    }
                    
                    // Faqat 2 va undan ko'p rasm bo'lganda ishga tushadi
                    if (this.currentStationImages.length > 1) {
                        this.carouselInterval = setInterval(() => {
                            // Faqat ovoz chiqayotganda carousel ishlaydi
                            if (this.isSpeaking && this.currentStationImages.length > 0) {
                                this.nextModalImage();
                            }
                        }, 3000); // Har 3 sekundda
                    }
                },
                
                showSuccessAndPlay(audioUrl) {
                    this.showSuccess = true;
                    
                    setTimeout(() => {
                        this.isProcessing = false;
                        this.showSuccess = false;
                        this.playAudio(audioUrl);
                    }, 1500);
                },
                
                async sendAudioToBackend(audioBlob) {
                    try {
                        const formData = new FormData();
                        formData.append('audio', audioBlob, 'recording.webm');
                        
                        const response = await fetch('/api/voice/process', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        
                        if (!response.ok) {
                            throw new Error('HTTP xato: ' + response.status);
                        }
                        
                        return await response.json();
                    } catch (error) {
                        console.error('Send audio error:', error);
                        throw error;
                    }
                },
                
                async pollTtsStatus(taskId, maxAttempts = 30) {
                    let attempt = 0;
                    
                    const checkStatus = async () => {
                        attempt++;
                        
                        try {
                            const response = await fetch(`/api/tts/status/${taskId}`);
                            
                            if (!response.ok) {
                                throw new Error('Status tekshirishda xato');
                            }
                            
                            const data = await response.json();
                            
                            if (data.status === 'SUCCESS' && data.audio_url) {
                                this.showSuccessAndPlay(data.audio_url);
                                return;
                            }
                            
                            if (data.status === 'FAILED' || data.status === 'ERROR') {
                                console.error('TTS failed:', data);
                                this.isProcessing = false;
                                return;
                            }
                            
                            if (attempt < maxAttempts) {
                                setTimeout(checkStatus, 1000);
                            } else {
                                console.error('TTS timeout');
                                this.isProcessing = false;
                            }
                            
                        } catch (error) {
                            console.error('Poll TTS error:', error);
                            if (attempt < maxAttempts) {
                                setTimeout(checkStatus, 1000);
                            } else {
                                this.isProcessing = false;
                            }
                        }
                    };
                    
                    checkStatus();
                },
                
                playAudio(audioUrl) {
                    this.isSpeaking = true;
                    this.currentWordIndex = -1;
                    this.words = [];
                    
                    this.audioElement = new Audio(audioUrl);
                    
                    // Audio metadata yuklanganidan keyin word tracking ni boshlash
                    this.audioElement.onloadedmetadata = () => {
                        console.log('Audio duration:', this.audioElement.duration);
                    };
                    
                    // Audio boshlanganda word tracking ni boshlash
                    this.audioElement.onplay = () => {
                        // Bir oz kutib, keyin startWordTracking ni chaqirish
                        setTimeout(() => {
                            this.startWordTracking();
                        }, 100);
                    };
                    
                    this.audioElement.onended = () => {
                        this.isSpeaking = false;
                        
                        // Word tracking intervalini to'xtatish
                        if (this.wordTrackingInterval) {
                            clearInterval(this.wordTrackingInterval);
                            this.wordTrackingInterval = null;
                        }
                        
                        // Carousel intervalini to'xtatish
                        if (this.carouselInterval) {
                            clearInterval(this.carouselInterval);
                            this.carouselInterval = null;
                        }
                        
                        this.currentResponse = null;
                        this.currentImageIndex = 0;
                        this.currentStationImages = [];
                        this.currentWordIndex = -1;
                        this.words = [];
                        this.audioElement = null;
                    };
                    
                    this.audioElement.onerror = () => {
                        console.error('Audio play error');
                        this.isSpeaking = false;
                        
                        // Word tracking intervalini to'xtatish
                        if (this.wordTrackingInterval) {
                            clearInterval(this.wordTrackingInterval);
                            this.wordTrackingInterval = null;
                        }
                        
                        // Carousel intervalini to'xtatish
                        if (this.carouselInterval) {
                            clearInterval(this.carouselInterval);
                            this.carouselInterval = null;
                        }
                        
                        this.currentResponse = null;
                        this.currentImageIndex = 0;
                        this.currentStationImages = [];
                        this.currentWordIndex = -1;
                        this.words = [];
                        this.audioElement = null;
                    };
                    
                    this.audioElement.play();
                },
                
                startWordTracking() {
                    if (!this.currentResponse || !this.currentResponse.response_text) return;
                    
                    const totalWords = this.getWords().length;
                    if (totalWords === 0) return;
                    
                    // Audio davomiyligini olish
                    let duration = this.audioElement.duration;
                    
                    // Agar duration hali tayyor bo'lmasa, taxminiy vaqtni hisoblash
                    if (!duration || duration === 0 || isNaN(duration) || !isFinite(duration)) {
                        console.warn('Duration not available, using estimated time');
                        // O'rtacha 2 so'z/soniya tezlikda (sekinroq)
                        duration = totalWords / 2;
                    }
                    
                    console.log('Total words:', totalWords);
                    console.log('Audio duration:', duration, 'seconds');
                    
                    // Har bir so'z uchun taxminiy vaqt (millisekundlarda)
                    // 1.4x sekinroq qilish uchun duration ni ko'paytirish
                    const timePerWord = (duration * 1000 * 1.4) / totalWords;
                    console.log('Time per word:', timePerWord, 'ms');
                    
                    // Birinchi so'zni darhol highlight qilish
                    this.currentWordIndex = 0;
                    
                    // Har bir so'zni ketma-ket highlight qilish
                    this.wordTrackingInterval = setInterval(() => {
                        if (this.currentWordIndex < totalWords - 1) {
                            this.currentWordIndex++;
                        } else {
                            clearInterval(this.wordTrackingInterval);
                            this.wordTrackingInterval = null;
                        }
                    }, timePerWord);
                },

                prevModalImage() {
                    if (this.currentStationImages && this.currentStationImages.length > 0) {
                        if (this.currentImageIndex > 0) {
                            this.currentImageIndex--;
                        } else {
                            this.currentImageIndex = this.currentStationImages.length - 1;
                        }
                    }
                },

                nextModalImage() {
                    if (this.currentStationImages && this.currentStationImages.length > 0) {
                        if (this.currentImageIndex < this.currentStationImages.length - 1) {
                            this.currentImageIndex++;
                        } else {
                            this.currentImageIndex = 0;
                        }
                    }
                }
            }
        }
    </script>
</div>