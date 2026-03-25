<?php

namespace App\Livewire;

use App\Models\Report;
use Livewire\Component;

class ReportsComparison extends Component
{
    public int $stationId;
    public string $type = 'yuk_ortilishi';
    public string $month1 = '';
    public string $month2 = '';
    public array $result = [];
    public bool $showResults = false;

    public function mount(int $stationId): void
    {
        $this->stationId = $stationId;
        $this->month1 = now()->subMonth()->format('Y-m');
        $this->month2 = now()->format('Y-m');
    }

    public function getMonthOptions(): array
    {
        $months = [];
        $names = [1=>'Yanvar',2=>'Fevral',3=>'Mart',4=>'Aprel',5=>'May',6=>'Iyun',7=>'Iyul',8=>'Avgust',9=>'Sentabr',10=>'Oktabr',11=>'Noyabr',12=>'Dekabr'];
        for ($i = 0; $i < 24; $i++) {
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = $names[(int)$date->format('m')] . ' ' . $date->format('Y');
        }
        return $months;
    }

    public function compare(): void
    {
        $report1 = Report::where('station_id', $this->stationId)
            ->where('type', $this->type)
            ->whereYear('date', substr($this->month1, 0, 4))
            ->whereMonth('date', substr($this->month1, 5, 2))
            ->first();

        $report2 = Report::where('station_id', $this->stationId)
            ->where('type', $this->type)
            ->whereYear('date', substr($this->month2, 0, 4))
            ->whereMonth('date', substr($this->month2, 5, 2))
            ->first();

        if ($this->type === 'xarajat_daromad') {
            $this->result = [
                ['label' => 'Xarajat', 'v1' => $report1->expense ?? 0, 'v2' => $report2->expense ?? 0],
                ['label' => 'Daromad', 'v1' => $report1->income ?? 0, 'v2' => $report2->income ?? 0],
            ];
        } else {
            $this->result = [
                ['label' => 'Reja', 'v1' => $report1->planned_value ?? 0, 'v2' => $report2->planned_value ?? 0],
                ['label' => 'Haqiqiy', 'v1' => $report1->actual_value ?? 0, 'v2' => $report2->actual_value ?? 0],
            ];
        }

        $this->showResults = true;
    }

    public function render()
    {
        return view('livewire.reports-comparison');
    }
}
