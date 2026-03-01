<?php

namespace App\Http\Controllers;

use App\Imports\WargaImport;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class WargaImportController extends Controller
{
    public function page()
    {
        return view('warga.import');
    }

    public function import(Request $request)
    {
        try {
            @ini_set('memory_limit', '512M');
            @set_time_limit(0);

            $request->validate([
                'file' => ['required', 'file', 'mimes:xls,xlsx', 'max:10240'],
            ]);

            $uploadedFile = $request->file('file');
            $realPath = $uploadedFile->getRealPath() ?: $uploadedFile->path();
            $readerType = $this->detectReaderType($realPath);

            Excel::import(new WargaImport, $uploadedFile, null, $readerType);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Import warga berhasil diproses.',
                ]);
            }

            return back()->with('success', 'Import warga berhasil diproses.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Import gagal. Pastikan format file sesuai template.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Import gagal. Pastikan format file sesuai template.');
        }
    }

    private function detectReaderType(string $realPath): ?string
    {
        if (empty($realPath) || !is_file($realPath)) {
            return null;
        }

        $detected = IOFactory::identify($realPath);

        return match ($detected) {
            'Xlsx' => ExcelFormat::XLSX,
            'Xls' => ExcelFormat::XLS,
            'Csv' => ExcelFormat::CSV,
            'Ods' => ExcelFormat::ODS,
            default => null,
        };
    }
}
