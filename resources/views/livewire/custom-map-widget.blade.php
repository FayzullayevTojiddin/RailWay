<div 
    wire:ignore 
    x-data 
    x-init="$nextTick(() => initMap())"
    class="h-[600px] w-full rounded-lg overflow-hidden bg-gray-100"
    id="customMap">
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script>
        function initMap() {
            const mapContainer = document.getElementById('customMap');
            if (!mapContainer) return;

            const imageExtent = [0, 0, 1024, 768];

            const map = new ol.Map({
                target: mapContainer,
                layers: [
                    new ol.layer.Image({
                        source: new ol.source.ImageStatic({
                            url: 'https://cdn.britannica.com/06/276306-049-A8451874/world-map.jpg',
                            imageExtent: imageExtent,
                        }),
                    }),
                ],
                view: new ol.View({
                    projection: new ol.proj.Projection({
                        code: 'custom-image',
                        units: 'pixels',
                        extent: imageExtent,
                    }),
                    center: [512, 384],
                    zoom: 2,
                    maxZoom: 8,
                }),
                controls: ol.control.defaults({ attribution: false, zoom: true }),
                interactions: ol.interaction.defaults({ dragPan: true, mouseWheelZoom: true }),
            });

            // Filament layout ichida bo‘lgani uchun DOM o‘lchamini aniqlab ber
            setTimeout(() => map.updateSize(), 300);
        }
    </script>
@endpush