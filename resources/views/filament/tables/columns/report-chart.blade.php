@php
    $id = 'report-chart-' . $record->id;
    $type = $record->type;

    if ($type === 'xarajat_daromad') {
        $labels = ['Xarajat', 'Daromad'];
        $values = [(float)($record->expense ?? 0), (float)($record->income ?? 0)];
    } else {
        $labels = ['Reja', 'Haqqiqiy'];
        $values = [(float)($record->planned_value ?? 0), (float)($record->actual_value ?? 0)];
    }

    foreach ($values as $k => $v) {
        $values[$k] = is_numeric($v) ? (float)$v : 0;
    }

    // umumiy o'lchamlar
    $width = 120;
    $height = 48;
@endphp

@if($type === 'xarajat_daromad')
    {{-- BAR CHART: Xarajat / Daromad --}}
    @php
        $pad = 6;
        $barGap = 6;
        $bars = count($values);
        $maxVal = max($values) > 0 ? max($values) : 1;
        $barWidth = intval(($width - $pad*2 - ($bars-1)*$barGap) / $bars);
        $colors = [];
        foreach ($values as $i => $v) {
            if ($i === 1 && $values[1] > ($values[0] ?? 0)) $colors[] = '#22c55e';
            else $colors[] = '#6366f1';
        }
    @endphp

    <div style="width:{{ $width }}px; height:{{ $height }}px; display:flex; align-items:center; justify-content:center;">
        <svg role="img" aria-label="chart-{{ $record->id }}" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 {{ $width }} {{ $height }}" xmlns="http://www.w3.org/2000/svg" style="display:block;">
            @foreach($values as $i => $v)
                @php
                    $x = $pad + $i * ($barWidth + $barGap);
                    $barMaxHeight = $height - ($pad * 2);
                    $barHeight = $maxVal > 0 ? ($v / $maxVal) * $barMaxHeight : 0;
                    $y = ($height - $pad) - $barHeight;
                    $rx = 3;
                    $fill = $colors[$i] ?? '#6366f1';
                    $label = $labels[$i] ?? '';
                    $displayValue = number_format($v, 0, '.', ' ');
                @endphp
                <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ max(1, $barHeight) }}"
                      rx="{{ $rx }}" ry="{{ $rx }}" fill="{{ $fill }}" opacity="0.95">
                    <title>{{ $label }}: {{ $displayValue }}</title>
                </rect>
            @endforeach
        </svg>
    </div>

@else
    {{-- DONUT: bajarilish foizi = actual / plan * 100 (markazda haqiqiy foiz, donut 0..100% vizual) --}}
    @php
        $a = $values[0] ?? 0; // reja (plan)
        $b = $values[1] ?? 0; // haqiqiy (actual)

        // bajarilish foizi
        $pctComplete = ($a > 0) ? ($b / $a) * 100 : 0;
        // donutda 0..100 oralig'ida chizamiz (visual)
        $pctShown = min(max($pctComplete, 0), 100);

        // donut params
        $size = 56;
        $cx = $size / 2;
        $cy = $size / 2;
        $r = 22;
        $circ = 2 * pi() * $r;

        $dashCompleted = ($pctShown / 100) * $circ;
        $dashRemaining = $circ - $dashCompleted;

        // rang: overshoot (actual >= plan) -> green; aks holda purple
        $colorCompleted = ($a > 0 && $b >= $a) ? '#22c55e' : '#6366f1';
        $colorRemaining = '#2d2f34';
        // markazdagi matn: haqiqiy foiz (1 decimal)
        $centerText = $a > 0 ? number_format($pctComplete, 1) . '%' : '0%';
    @endphp

    <div style="width:{{ $width }}px; height:{{ $height }}px; display:flex; align-items:center; justify-content:center;">
        <svg role="img" aria-label="donut-{{ $record->id }}" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 {{ $width }} {{ $height }}" xmlns="http://www.w3.org/2000/svg" style="display:block;">
            <g transform="translate(6,0)">
                {{-- remaining ring --}}
                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="{{ $colorRemaining }}" stroke-width="10" stroke-linecap="round" stroke-dasharray="{{ $dashRemaining }} {{ $circ - $dashRemaining }}" transform="rotate(-90 {{ $cx }} {{ $cy }})"></circle>

                {{-- completed ring --}}
                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="{{ $colorCompleted }}" stroke-width="10" stroke-linecap="round" stroke-dasharray="{{ $dashCompleted }} {{ $circ - $dashCompleted }}" transform="rotate(-90 {{ $cx }} {{ $cy }})"></circle>

                {{-- center text (shows >100% too) --}}
                <text x="{{ $cx }}" y="{{ $cy+4 }}" font-size="10" text-anchor="middle" fill="#E5E7EB" style="font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;">
                    {{ $centerText }}
                </text>

                <title>Reja: {{ number_format($a,0,'.',' ') }} — Haqiqiy: {{ number_format($b,0,'.',' ') }} ({{ $centerText }})</title>
            </g>

            {{-- legend --}}
            <g transform="translate(66,6)">
                <rect x="0" y="0" width="9" height="9" rx="2" fill="#aab0ff"></rect>
                <text x="14" y="7.5" font-size="10" fill="#9CA3AF">{{ \Illuminate\Support\Str::limit($labels[0] ?? 'Reja', 10) }}</text>

                <rect x="0" y="18" width="9" height="9" rx="2" fill="{{ $colorCompleted }}"></rect>
                <text x="14" y="25.5" font-size="10" fill="#9CA3AF">{{ \Illuminate\Support\Str::limit($labels[1] ?? 'Haqiqiy', 10) }}</text>
            </g>
        </svg>
    </div>
@endif