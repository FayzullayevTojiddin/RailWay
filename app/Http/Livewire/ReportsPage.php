<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class ReportsPage extends Component
{
    public $selectedType = null;
    public $selectedMonth = null;
    public $selectedYear = null;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    public function updatedSelectedType($value)
    {
        $this->emit('reportsFilterChanged', [
            'type' => $this->selectedType,
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
        ]);
    }

    public function updatedSelectedMonth($value)
    {
        $this->emit('reportsFilterChanged', [
            'type' => $this->selectedType,
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
        ]);
    }

    public function updatedSelectedYear($value)
    {
        $this->emit('reportsFilterChanged', [
            'type' => $this->selectedType,
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
        ]);
    }

    public function setType($type)
    {
        $this->selectedType = $type;
        $this->updatedSelectedType($type);
    }

    public function render()
    {
        $months = collect(range(1,12))->mapWithKeys(function($m){ return [$m => Carbon::create()->month($m)->format('F')]; })->toArray();

        return view('livewire.reports-page', [
            'months' => $months,
            'types' => [
                'yuk_ortilishi' => 'Yuk ortilishi',
                'yuk_tushurilishi' => 'Yuk tushurilishi',
                'pul_tushumi' => 'Pul tushumi',
                'xarajat_daromad' => 'Xarajat/Daromad',
                'boshqalar' => 'Boshqalar',
            ],
        ]);
    }
}
