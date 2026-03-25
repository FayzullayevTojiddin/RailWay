<div>
    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div style="flex:1;min-width:160px;">
            <label style="display:block;font-size:13px;font-weight:500;margin-bottom:4px;">Hisobot turi</label>
            <select wire:model="type" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;">
                <option value="yuk_ortilishi">Oylik yuk ortilishi</option>
                <option value="yuk_tushurilishi">Oylik yuk tushurilishi</option>
                <option value="pul_tushumi">Oylik pul tushumi</option>
                <option value="xarajat_daromad">Oylik xarajat va daromad</option>
            </select>
        </div>
        <div style="flex:1;min-width:140px;">
            <label style="display:block;font-size:13px;font-weight:500;margin-bottom:4px;">1-oy</label>
            <select wire:model="month1" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;">
                @foreach($this->getMonthOptions() as $val => $lbl)
                    <option value="{{ $val }}">{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:140px;">
            <label style="display:block;font-size:13px;font-weight:500;margin-bottom:4px;">2-oy</label>
            <select wire:model="month2" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;">
                @foreach($this->getMonthOptions() as $val => $lbl)
                    <option value="{{ $val }}">{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;align-items:flex-end;">
            <button wire:click="compare" style="padding:8px 20px;background:#16a34a;color:white;font-size:13px;font-weight:600;border-radius:8px;border:none;cursor:pointer;">
                Taqqoslash
            </button>
        </div>
    </div>

    @if($showResults)
        @php
            $opts = $this->getMonthOptions();
            $m1Name = $opts[$month1] ?? $month1;
            $m2Name = $opts[$month2] ?? $month2;
        @endphp
        <table style="width:100%;font-size:14px;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="padding:10px 12px;border:1px solid #e5e7eb;background:#f9fafb;text-align:left;width:22%;"></th>
                    <th style="padding:10px 12px;border:1px solid #e5e7eb;background:#eff6ff;color:#2563eb;text-align:center;font-weight:600;">{{ $m1Name }}</th>
                    <th style="padding:10px 12px;border:1px solid #e5e7eb;background:#f0fdf4;color:#16a34a;text-align:center;font-weight:600;">{{ $m2Name }}</th>
                    <th style="padding:10px 12px;border:1px solid #e5e7eb;background:#f3f4f6;text-align:center;font-weight:600;">Farq (+/-)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result as $row)
                    @php
                        $diff = $row['v2'] - $row['v1'];
                        $diffColor = $diff > 0 ? '#16a34a' : ($diff < 0 ? '#dc2626' : '#6b7280');
                        $diffSign = $diff > 0 ? '+' : '';
                    @endphp
                    <tr>
                        <td style="padding:10px 12px;border:1px solid #e5e7eb;font-weight:500;">{{ $row['label'] }}</td>
                        <td style="padding:10px 12px;border:1px solid #e5e7eb;text-align:center;">{{ number_format($row['v1'], 0, '.', ' ') }}</td>
                        <td style="padding:10px 12px;border:1px solid #e5e7eb;text-align:center;">{{ number_format($row['v2'], 0, '.', ' ') }}</td>
                        <td style="padding:10px 12px;border:1px solid #e5e7eb;text-align:center;font-weight:600;color:{{ $diffColor }};">{{ $diffSign }}{{ number_format($diff, 0, '.', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
