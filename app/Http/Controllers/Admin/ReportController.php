<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\ArticleReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $reports = ArticleReport::query()
            ->with(['article.user', 'user', 'reviewer'])
            ->when(
                $status !== '' && ReportStatus::tryFrom($status),
                fn ($q) => $q->where('status', ReportStatus::from($status)),
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'pending' => ArticleReport::query()->where('status', ReportStatus::Pending)->count(),
            'reviewed' => ArticleReport::query()->where('status', ReportStatus::Reviewed)->count(),
            'dismissed' => ArticleReport::query()->where('status', ReportStatus::Dismissed)->count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats', 'status'));
    }

    public function update(Request $request, ArticleReport $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(ReportStatus::class)],
        ]);

        $status = ReportStatus::from($validated['status']);

        $report->update([
            'status' => $status,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Report marked as '.$status->value.'.');
    }

    public function export(Request $request): StreamedResponse
    {
        $status = $request->string('status')->toString();
        $filename = 'truthlens-reports-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($status) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'id',
                'article_id',
                'article_title',
                'reporter_name',
                'reporter_email',
                'category',
                'details',
                'status',
                'reviewed_by',
                'reviewed_at',
                'created_at',
            ]);

            ArticleReport::query()
                ->with(['article', 'user', 'reviewer'])
                ->when(
                    $status !== '' && ReportStatus::tryFrom($status),
                    fn ($q) => $q->where('status', ReportStatus::from($status)),
                )
                ->orderByDesc('id')
                ->chunk(200, function ($reports) use ($handle) {
                    foreach ($reports as $report) {
                        fputcsv($handle, [
                            $report->id,
                            $report->article_id,
                            $report->article->title,
                            $report->user->name,
                            $report->user->email,
                            $report->category->value,
                            $report->details,
                            $report->status->value,
                            $report->reviewer?->name,
                            $report->reviewed_at?->toDateTimeString(),
                            $report->created_at?->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
