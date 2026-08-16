<?php

namespace App\Http\Controllers\Report;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
        $this->middleware('role:superadmin|admin|accountant');
    }

    public function index()
    {
        $types = $this->service->supportedTypes();

        return view('reports.index', compact('types'));
    }

    public function show(Request $request, string $type)
    {
        if (! array_key_exists($type, $this->service->supportedTypes())) {
            abort(404);
        }

        $report = $this->service->build($type, $request);
        $filterForm = $this->service->filterForm($type, $request);
        $types = $this->service->supportedTypes();

        return view('reports.show', compact('report', 'filterForm', 'types'));
    }

    public function exportPdf(Request $request, string $type)
    {
        if (! array_key_exists($type, $this->service->supportedTypes())) {
            abort(404);
        }

        $report = $this->service->build($type, $request);

        $pdf = Pdf::loadView('reports.pdf', compact('report'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($this->service->filename($type, 'pdf'));
    }

    public function exportExcel(Request $request, string $type)
    {
        if (! array_key_exists($type, $this->service->supportedTypes())) {
            abort(404);
        }

        $report = $this->service->build($type, $request);

        return Excel::download(new ReportExport($report), $this->service->filename($type, 'xlsx'));
    }
}
