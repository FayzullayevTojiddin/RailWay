<div>
    <style>
        .cmp-type-row{margin-bottom:16px}
        .cmp-label{display:block;font-size:13px;font-weight:500;margin-bottom:4px;color:#374151}
        .dark .cmp-label{color:#d1d5db}
        .cmp-select{width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;background:#fff;color:#111827}
        .dark .cmp-select{background:#1f2937;color:#f3f4f6;border-color:#4b5563}

        .cmp-groups{display:grid;grid-template-columns:1fr auto 1fr;gap:16px;align-items:stretch;margin-bottom:18px}
        @media (max-width:760px){.cmp-groups{grid-template-columns:1fr}}
        .cmp-group{padding:14px;border-radius:12px;border:1px solid #e5e7eb;background:#fff;display:flex;flex-direction:column}
        .dark .cmp-group{background:#1f2937;border-color:#4b5563}
        .cmp-group-a{border-left:4px solid #3b82f6}
        .cmp-group-b{border-left:4px solid #16a34a}
        .cmp-group-title{font-size:13px;font-weight:700;margin-bottom:8px;display:flex;align-items:center;gap:8px;color:#111827}
        .dark .cmp-group-title{color:#f3f4f6}
        .cmp-group-badge{display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600}
        .cmp-badge-a{background:#dbeafe;color:#1d4ed8}.dark .cmp-badge-a{background:#1e3a8a;color:#93c5fd}
        .cmp-badge-b{background:#dcfce7;color:#15803d}.dark .cmp-badge-b{background:#14532d;color:#86efac}
        .cmp-checkbox-list{flex:1;max-height:180px;overflow-y:auto;border:1px solid #d1d5db;border-radius:8px;padding:8px;background:#fafafa;display:flex;flex-direction:column;gap:2px}
        .dark .cmp-checkbox-list{background:#111827;border-color:#4b5563}
        .cmp-checkbox-item{display:flex;align-items:center;gap:8px;padding:5px 6px;border-radius:6px;cursor:pointer;font-size:13px;color:#111827}
        .cmp-checkbox-item:hover{background:#f3f4f6}
        .dark .cmp-checkbox-item{color:#f3f4f6}
        .dark .cmp-checkbox-item:hover{background:#374151}
        .cmp-checkbox-item input{margin:0;cursor:pointer}
        .cmp-vs{display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#6b7280;padding:0 4px}
        .dark .cmp-vs{color:#9ca3af}
        @media (max-width:760px){.cmp-vs{padding:6px 0}}

        .cmp-actions{display:flex;justify-content:center;margin-bottom:18px}
        .cmp-btn{padding:10px 28px;background:#16a34a;color:#fff;font-size:13px;font-weight:600;border-radius:8px;border:none;cursor:pointer;white-space:nowrap}
        .cmp-btn:hover{background:#15803d}
        .cmp-btn:disabled{background:#9ca3af;cursor:not-allowed}

        .cmp-table{width:100%;font-size:13px;border-collapse:collapse;margin-top:4px}
        .cmp-table th,.cmp-table td{padding:10px;border:1px solid #e5e7eb}
        .dark .cmp-table th,.dark .cmp-table td{border-color:#4b5563}
        .cmp-th-empty{background:#f9fafb;text-align:left;width:18%}.dark .cmp-th-empty{background:#1f2937}
        .cmp-th-a{background:#eff6ff;color:#2563eb;text-align:center;font-weight:600}.dark .cmp-th-a{background:#1e3a5f;color:#60a5fa}
        .cmp-th-b{background:#f0fdf4;color:#16a34a;text-align:center;font-weight:600}.dark .cmp-th-b{background:#14532d;color:#4ade80}
        .cmp-th-diff{background:#f3f4f6;text-align:center;font-weight:600;color:#374151}.dark .cmp-th-diff{background:#374151;color:#d1d5db}
        .cmp-td-label{font-weight:500;color:#111827}.dark .cmp-td-label{color:#f3f4f6}
        .cmp-td-val{text-align:center;color:#374151}.dark .cmp-td-val{color:#d1d5db}
        .cmp-td-val .cmp-avg{display:block;font-size:11px;color:#9ca3af;margin-top:2px}
        .cmp-td-diff{text-align:center;font-weight:700}
        .cmp-plus{color:#16a34a}.dark .cmp-plus{color:#4ade80}
        .cmp-minus{color:#dc2626}.dark .cmp-minus{color:#f87171}
        .cmp-zero{color:#6b7280}.dark .cmp-zero{color:#9ca3af}
        .cmp-th-sub{font-size:11px;font-weight:500;opacity:0.85;display:block;margin-top:2px;font-style:normal}

        .cmp-empty{text-align:center;padding:20px;color:#6b7280;font-size:14px}.dark .cmp-empty{color:#9ca3af}
        .cmp-section-title{font-size:14px;font-weight:700;margin:20px 0 12px;color:#111827;display:flex;align-items:center;gap:8px}.dark .cmp-section-title{color:#f3f4f6}
        .cmp-section-title::before{content:'';display:block;width:4px;height:18px;background:#16a34a;border-radius:4px}
        .cmp-bar-wrap{margin-top:4px;padding:20px;border-radius:16px;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border:1px solid #e2e8f0}
        .dark .cmp-bar-wrap{background:linear-gradient(135deg,#1e293b,#0f172a);border-color:#334155}
        .cmp-warn{padding:10px 14px;border-radius:8px;background:#fef3c7;color:#92400e;font-size:13px;margin-bottom:12px;border:1px solid #fde68a}
        .dark .cmp-warn{background:#451a03;color:#fcd34d;border-color:#78350f}
    </style>

    <div class="cmp-type-row">
        <label class="cmp-label">Hisobot turi</label>
        <select wire:model.live="cmpType" class="cmp-select">
            <option value="yuk_ortilishi">Oylik yuk ortilishi</option>
            <option value="yuk_tushurilishi">Oylik yuk tushurilishi</option>
            <option value="pul_tushumi">Oylik pul tushumi</option>
            <option value="xarajat_daromad">Oylik xarajat va daromad</option>
        </select>
    </div>

    @if(count($monthOptions) === 0)
        <div class="cmp-empty">Bu turdagi hisobotlar topilmadi</div>
    @else
        <div class="cmp-groups">
            <div class="cmp-group cmp-group-a">
                <div class="cmp-group-title">
                    <span class="cmp-group-badge cmp-badge-a">Dan</span>
                </div>
                <div class="cmp-checkbox-list">
                    @foreach($monthOptions as $val => $lbl)
                        <label class="cmp-checkbox-item">
                            <input type="checkbox" wire:model="cmpMonthsA" value="{{ $val }}" />
                            <span>{{ $lbl }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="cmp-vs">VS</div>

            <div class="cmp-group cmp-group-b">
                <div class="cmp-group-title">
                    <span class="cmp-group-badge cmp-badge-b">Gacha</span>
                </div>
                <div class="cmp-checkbox-list">
                    @foreach($monthOptions as $val => $lbl)
                        <label class="cmp-checkbox-item">
                            <input type="checkbox" wire:model="cmpMonthsB" value="{{ $val }}" />
                            <span>{{ $lbl }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        @if((empty($cmpMonthsA) || empty($cmpMonthsB)) && !$cmpShowResults)
            <div class="cmp-warn">Har ikki guruhda kamida bittadan oy tanlang.</div>
        @endif

        <div class="cmp-actions">
            <button wire:click="runComparison" type="button" class="cmp-btn"
                    @if(empty($cmpMonthsA) || empty($cmpMonthsB)) disabled @endif>
                Taqqoslash
            </button>
        </div>
    @endif

    @if($cmpShowResults && !empty($cmpResult['metrics'] ?? null))
        @php
            $labelA = $cmpResult['labelA'];
            $labelB = $cmpResult['labelB'];
            $countA = $cmpResult['countA'];
            $countB = $cmpResult['countB'];
            $metrics = $cmpResult['metrics'];
        @endphp

        <table class="cmp-table">
            <thead>
                <tr>
                    <th class="cmp-th-empty"></th>
                    <th class="cmp-th-a">
                        Dan ({{ $countA }} oy)
                        <span class="cmp-th-sub">{{ $labelA }}</span>
                    </th>
                    <th class="cmp-th-b">
                        Gacha ({{ $countB }} oy)
                        <span class="cmp-th-sub">{{ $labelB }}</span>
                    </th>
                    <th class="cmp-th-diff">Farq</th>
                    <th class="cmp-th-diff">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metrics as $row)
                    @php
                        $diff = $row['diff'];
                        $diffClass = $diff > 0 ? 'cmp-plus' : ($diff < 0 ? 'cmp-minus' : 'cmp-zero');
                        $diffSign = $diff > 0 ? '+' : '';
                        $pct = $row['pct'];
                        $pctClass = 'cmp-zero';
                        if (str_starts_with($pct, '+') && $pct !== '+0%' && $pct !== '+0.0%') {
                            $pctClass = 'cmp-plus';
                        } elseif (str_starts_with($pct, '-')) {
                            $pctClass = 'cmp-minus';
                        }
                    @endphp
                    <tr>
                        <td class="cmp-td-label">{{ $row['label'] }}</td>
                        <td class="cmp-td-val">
                            <strong>{{ number_format($row['sumA'], 0, '.', ' ') }}</strong>
                            @if($countA > 1)
                                <span class="cmp-avg">o'rt: {{ number_format($row['avgA'], 0, '.', ' ') }}</span>
                            @endif
                        </td>
                        <td class="cmp-td-val">
                            <strong>{{ number_format($row['sumB'], 0, '.', ' ') }}</strong>
                            @if($countB > 1)
                                <span class="cmp-avg">o'rt: {{ number_format($row['avgB'], 0, '.', ' ') }}</span>
                            @endif
                        </td>
                        <td class="cmp-td-diff {{ $diffClass }}">{{ $diffSign }}{{ number_format($diff, 0, '.', ' ') }}</td>
                        <td class="cmp-td-diff {{ $pctClass }}">{{ $pct }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div
            x-data="{}"
            x-init="
                const loadChart = () => {
                    if (typeof Chart === 'undefined') {
                        const s = document.createElement('script');
                        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                        s.onload = () => draw();
                        document.head.appendChild(s);
                    } else {
                        draw();
                    }

                    function draw() {
                        const metrics = {{ Js::from($metrics) }};
                        const labelA = {{ Js::from('Dan (' . $countA . ' oy)') }};
                        const labelB = {{ Js::from('Gacha (' . $countB . ' oy)') }};
                        const isDark = document.documentElement.classList.contains('dark');
                        const txtC = isDark ? '#cbd5e1' : '#334155';
                        const gridC = isDark ? '#1e293b' : '#f1f5f9';

                        const barEl = $el.querySelector('#cmpBar');
                        if (!barEl) return;
                        const oldBar = Chart.getChart(barEl);
                        if (oldBar) oldBar.destroy();

                        new Chart(barEl, {
                            type: 'bar',
                            data: {
                                labels: metrics.map(m => m.label),
                                datasets: [
                                    {
                                        label: '🔵 ' + labelA,
                                        data: metrics.map(m => m.sumA),
                                        backgroundColor: 'rgba(59,130,246,0.85)',
                                        borderRadius: 8, borderSkipped: false,
                                        barPercentage: 0.55, categoryPercentage: 0.65,
                                    },
                                    {
                                        label: '🟢 ' + labelB,
                                        data: metrics.map(m => m.sumB),
                                        backgroundColor: 'rgba(34,197,94,0.85)',
                                        borderRadius: 8, borderSkipped: false,
                                        barPercentage: 0.55, categoryPercentage: 0.65,
                                    },
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                layout: { padding: { top: 25 } },
                                animation: { duration: 800 },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: { color: txtC, font: { size: 13, weight: '700' }, padding: 18 }
                                    },
                                    tooltip: {
                                        backgroundColor: isDark ? '#1e293b' : '#fff',
                                        titleColor: txtC, bodyColor: txtC,
                                        titleFont: { size: 13, weight: '700' },
                                        bodyFont: { size: 13 },
                                        borderColor: isDark ? '#334155' : '#e2e8f0', borderWidth: 1,
                                        cornerRadius: 8, padding: 12,
                                        callbacks: { label: (c) => ' ' + c.dataset.label + ': ' + Number(c.raw).toLocaleString('ru') }
                                    },
                                },
                                scales: {
                                    x: {
                                        ticks: { color: txtC, font: { size: 13, weight: '600' } },
                                        grid: { display: false }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        ticks: { color: txtC, font: { size: 11 }, callback: (v) => Number(v).toLocaleString('ru') },
                                        grid: { color: gridC }, border: { display: false }
                                    }
                                }
                            },
                            plugins: [{
                                id: 'valLabels',
                                afterDatasetsDraw(chart) {
                                    const {ctx} = chart;
                                    chart.data.datasets.forEach((ds, di) => {
                                        const meta = chart.getDatasetMeta(di);
                                        meta.data.forEach((bar, idx) => {
                                            const val = ds.data[idx];
                                            ctx.save();
                                            ctx.font = 'bold 11px system-ui,sans-serif';
                                            ctx.fillStyle = txtC;
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'bottom';
                                            ctx.fillText(Number(val).toLocaleString('ru'), bar.x, bar.y - 4);
                                            ctx.restore();
                                        });
                                    });
                                }
                            }]
                        });
                    }
                };
                loadChart();
            "
        >
            <div class="cmp-section-title">Guruhlar bo'yicha taqqoslash (yig'indi)</div>
            <div class="cmp-bar-wrap">
                <canvas id="cmpBar" style="height:280px;width:100%;"></canvas>
            </div>
        </div>
    @endif
</div>
