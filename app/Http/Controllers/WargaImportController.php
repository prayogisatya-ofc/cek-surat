<?php

namespace App\Http\Controllers;

use App\Imports\WargaImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WargaImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:xls,xlsx','max:10240'],
        ]);

        Excel::import(new WargaImport, $request->file('file'));

        return back()->with('success', 'Import warga berhasil.');
    }
}
