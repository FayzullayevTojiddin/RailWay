<div>
    <style>
        .cmp-wrap{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px}
        .cmp-field{flex:1;min-width:140px}
        .cmp-label{display:block;font-size:13px;font-weight:500;margin-bottom:4px;color:#374151}
        .dark .cmp-label{color:#d1d5db}
        .cmp-select{width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;background:#fff;color:#111827}
        .dark .cmp-select{background:#1f2937;color:#f3f4f6;border-color:#4b5563}
        .cmp-btn{padding:8px 20px;background:#16a34a;color:#fff;font-size:13px;font-weight:600;border-radius:8px;border:none;cursor:pointer;white-space:nowrap}
        .cmp-btn:hover{background:#15803d}
        .cmp-btn:disabled{background:#9ca3af;cursor:not-allowed}
        .cmp-table{width:100%;font-size:13px;border-collapse:collapse;margin-top:4px}
        .cmp-table th,.cmp-table td{padding:8px 10px;border:1px solid #e5e7eb}
        .dark .cmp-table th,.dark .cmp-table td{border-color:#4b5563}
        .cmp-th-empty{background:#f9fafb;text-align:left;width:18%}.dark .cmp-th-empty{background:#1f2937}
        .cmp-th-m1{background:#eff6ff;color:#2563eb;text-align:center;font-weight:600}.dark .cmp-th-m1{background:#1e3a5f;color:#60a5fa}
        .cmp-th-m2{background:#f0fdf4;color:#16a34a;text-align:center;font-weight:600}.dark .cmp-th-m2{background:#14532d;color:#4ade80}
        .cmp-th-diff{background:#f3f4f6;text-align:center;font-weight:600;color:#374151}.dark .cmp-th-diff{background:#374151;color:#d1d5db}
        .cmp-td-label{font-weight:500;color:#111827}.dark .cmp-td-label{color:#f3f4f6}
        .cmp-td-val{text-align:center;color:#374151}.dark .cmp-td-val{color:#d1d5db}
        .cmp-td-diff{text-align:center;font-weight:600}
        .cmp-plus{color:#16a34a}.dark .cmp-plus{color:#4ade80}
        .cmp-minus{color:#dc2626}.dark .cmp-minus{color:#f87171}
        .cmp-zero{color:#6b7280}.dark .cmp-zero{color:#9ca3af}
        .cmp-empty{text-align:center;padding:20px;color:#6b7280;font-size:14px}.dark .cmp-empty{color:#9ca3af}
        .cmp-section-title{font-size:14px;font-weight:700;margin:20px 0 12px;color:#111827;display:flex;align-items:center;gap:8px}.dark .cmp-section-title{color:#f3f4f6}
        .cmp-section-title::before{content:'';display:block;width:4px;height:18px;background:#16a34a;border-radius:4px}
        .cmp-doughnut-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px}
        .cmp-doughnut-card{text-align:center;padding:16px;border-radius:16px;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border:1px solid #e2e8f0;overflow:hidden}
        .dark .cmp-doughnut-card{background:linear-gradient(135deg,#1e293b,#0f172a);border-color:#334155}
        .cmp-doughnut-label{font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px}.dark .cmp-doughnut-label{color:#94a3b8}
        .cmp-bar-wrap{margin-top:4px;padding:20px;border-radius:16px;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border:1px solid #e2e8f0}
        .dark .cmp-bar-wrap{background:linear-gradient(135deg,#1e293b,#0f172a);border-color:#334155}
    </style>

    <div class="cmp-wrap">
        <div class="cmp-field" style="min-width:160px;">
            <label class="cmp-label">Hisobot turi</label>
            <select wire:model.live="cmpType" class="cmp-select">
                <option value="yuk_ortilishi">Oylik yuk ortilishi</option>
                <option value="yuk_tushurilishi">Oylik yuk tushurilishi</option>
                <option value="pul_tushumi">Oylik pul tushumi</option>
                <option value="xarajat_daromad">Oylik xarajat va daromad</option>
            </select>
        </div>
        <div class="cmp-field">
            <label class="cmp-label">1-oy</label>
            <select wire:model="cmpMonth1" class="cmp-select" {{ count($monthOptions) === 0 ? 'disabled' : '' }}>
                @if(count($monthOptions) === 0)
                    <option value="">Ma'lumot yo'q</option>
                @else
                    @foreach($monthOptions as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="cmp-field">
            <label class="cmp-label">2-oy</label>
            <select wire:model="cmpMonth2" class="cmp-select" {{ count($monthOptions) === 0 ? 'disabled' : '' }}>
                @if(count($monthOptions) === 0)
                    <option value="">Ma'lumot yo'q</option>
                @else
                    @foreach($monthOptions as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div style="display:flex;align-items:flex-end;">
            <button wire:click="runComparison" type="button" class="cmp-btn" {{ count($monthOptions) < 2 ? 'disabled' : '' }}>
                Taqqoslash
            </button>
        </div>
    </div>

    @if($cmpShowResults && count($cmpResult) > 0)
        @php
            $m1Name = $monthOptions[$cmpMonth1] ?? $cmpMonth1;
            $m2Name = $monthOptions[$cmpMonth2] ?? $cmpMonth2;
        @endphp

        <table class="cmp-table">
            <thead>
                <tr>
                    <th class="cmp-th-empty"></th>
                    <th class="cmp-th-m1">{{ $m1Name }}</th>
                    <th class="cmp-th-m2">{{ $m2Name }}</th>
                    <th class="cmp-th-diff">Farq</th>
                    <th class="cmp-th-diff">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cmpResult as $row)
                    @php
                        $diff = $row['v2'] - $row['v1'];
                        $diffClass = $diff > 0 ? 'cmp-plus' : ($diff < 0 ? 'cmp-minus' : 'cmp-zero');
                        $diffSign = $diff > 0 ? '+' : '';
                        $pct = $row['pct'] ?? '0%';
                        $pctClass = str_starts_with($pct, '+') ? 'cmp-plus' : (str_starts_with($pct, '-') ? 'cmp-minus' : 'cmp-zero');
                    @endphp
                    <tr>
                        <td class="cmp-td-label">{{ $row['label'] }}</td>
                        <td class="cmp-td-val">{{ number_format($row['v1'], 0, '.', ' ') }}</td>
                        <td class="cmp-td-val">{{ number_format($row['v2'], 0, '.', ' ') }}</td>
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
                        const results = {{ Js::from($cmpResult) }};
                        const m1Name = {{ Js::from($m1Name) }};
                        const m2Name = {{ Js::from($m2Name) }};
                        const isDark = document.documentElement.classList.contains('dark');
                        const txtC = isDark ? '#cbd5e1' : '#334155';
                        const gridC = isDark ? '#1e293b' : '#f1f5f9';
                        const emptyC = isDark ? '#334155' : '#e2e8f0';

                        results.forEach((row, i) => {
                            const el = $el.querySelector('#cmpD' + i);
                            if (!el) return;
                            const old = Chart.getChart(el);
                            if (old) old.destroy();

                            const base = Math.max(row.v1, row.v2) || 1;
                            const pct = Math.min((row.v2 / base) * 100, 100);
                            const rest = Math.max(100 - pct, 0);
                            const clr = pct >= 100 ? '#22c55e' : (pct >= 80 ? '#eab308' : '#ef4444');

                            new Chart(el, {
                                type: 'doughnut',
                                data: {
                                    datasets: [{
                                        data: pct >= 100 ? [100] : [pct, rest],
                                        backgroundColor: pct >= 100 ? [clr] : [clr, emptyC],
                                        borderWidth: 0,
                                        borderRadius: pct >= 100 ? 0 : 8,
                                    }]
                                },
                                options: {
                                    cutout: '72%',
                                    responsive: false,
                                    animation: { animateRotate: true, duration: 800 },
                                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                                },
                                plugins: [{
                                    id: 'ct' + i,
                                    afterDraw(chart) {
                                        const {ctx, width, height} = chart;
                                        ctx.save();
                                        ctx.font = 'bold 22px system-ui,sans-serif';
                                        ctx.fillStyle = clr;
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        ctx.fillText(pct.toFixed(0) + '%', width/2, height/2 - 6);
                                        ctx.font = '11px system-ui,sans-serif';
                                        ctx.fillStyle = txtC;
                                        ctx.fillText(Number(row.v2).toLocaleString('ru'), width/2, height/2 + 14);
                                        ctx.restore();
                                    }
                                }]
                            });
                        });

                        const barEl = $el.querySelector('#cmpBar');
                        if (!barEl) return;
                        const oldBar = Chart.getChart(barEl);
                        if (oldBar) oldBar.destroy();

                        new Chart(barEl, {
                            type: 'bar',
                            data: {
                                labels: results.map(r => r.label),
                                datasets: [
                                    { label: '🔵 ' + m1Name, data: results.map(r => r.v1), backgroundColor: 'rgba(59,130,246,0.85)', borderRadius: 8, borderSkipped: false, barPercentage: 0.5, categoryPercentage: 0.6 },
                                    { label: '🟢 ' + m2Name, data: results.map(r => r.v2), backgroundColor: 'rgba(34,197,94,0.85)', borderRadius: 8, borderSkipped: false, barPercentage: 0.5, categoryPercentage: 0.6 }
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
                                        labels: { color: txtC, font: { size: 13, weight: '700' }, padding: 20 }
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
                                    datalabels: false
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
            <div class="cmp-section-title">Reja bajarilishi</div>
            <div class="cmp-doughnut-grid">
                @foreach($cmpResult as $i => $row)
                    <div class="cmp-doughnut-card">
                        <div class="cmp-doughnut-label">{{ $row['label'] }}</div>
                        <div style="display:flex;justify-content:center;">
                            <canvas id="cmpD{{ $i }}" width="150" height="150"></canvas>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="cmp-section-title">Oylar bo'yicha taqqoslash</div>
            <div class="cmp-bar-wrap">
                <canvas id="cmpBar" style="height:220px;width:100%;"></canvas>
            </div>
        </div>
    @elseif(count($monthOptions) === 0)
        <div class="cmp-empty">Bu turdagi hisobotlar topilmadi</div>
    @endif
</div>
