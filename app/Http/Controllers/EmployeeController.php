<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function download($id)
    {
        $record = Employee::findOrFail($id);
        $path = $record->details['pdf_document'];

        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'Fayl topilmadi');
        }

        return Storage::disk('private')->download($path, $record->full_name . '.pdf');
    }
}
