@php
    $record = $getRecord();

    if ($record->type === 'xarajat_daromad') {
        $val1 = $record->expense ?? 0;
        $val2 = $record->income ?? 0;
    } else {
        $val1 = $record->planned_value ?? 0;
        $val2 = $record->actual_value ?? 0;
    }

    $total = $val1 + $val2;
    if ($total > 0) {
        $pct1 = ($val1 / $total) * 100;
    } else {
        $pct1 = 50;
    }

    // Natija foizi
    if ($record->type === 'xarajat_daromad') {
        $expense = $record->expense ?? 0;
        $income = $record->income ?? 0;
        if ($expense == 0) {
            $percent = $income > 0 ? 100 : 0;
        } else {
            $percent = round((($income - $expense) / $expense) * 100, 1);
        }
    } else {
        $planned = $record->planned_value ?? 0;
        $actual = $record->actual_value ?? 0;
        $percent = $planned > 0 ? round(($actual / $planned) * 100, 1) : 0;
    }

    $circumference = 2 * 3.14159 * 36;
    $dash1 = ($pct1 / 100) * $circumference;
    $dash2 = $circumference - $dash1;
@endphp

<div style="display: flex; align-items: center; justify-content: center;">
    <svg width="48" height="48" viewBox="0 0 80 80">
        <circle cx="40" cy="40" r="36" fill="none" stroke="#d1d5db" stroke-width="7"/>
        <circle cx="40" cy="40" r="36" fill="none" stroke="#10b981" stroke-width="7"
            stroke-dasharray="{{ $dash1 }} {{ $dash2 }}"
            stroke-dashoffset="0"
            transform="rotate(-90 40 40)"/>
        <circle cx="40" cy="40" r="36" fill="none" stroke="#3b82f6" stroke-width="7"
            stroke-dasharray="{{ $dash2 }} {{ $dash1 }}"
            stroke-dashoffset="-{{ $dash1 }}"
            transform="rotate(-90 40 40)"/>
        <text x="40" y="40" text-anchor="middle" dominant-baseline="central"
            fill="currentColor" font-size="14" font-weight="bold">{{ $percent }}%</text>
    </svg>
</div>
