<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogWebController extends BaseController
{
    /**
     * Paginated, filterable list of audit log entries.
     */
    public function index(Request $request)
    {
        $auditLogs = $this->buildQuery($request)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.audit-logs', compact('auditLogs'));
    }

    /**
     * Stream the filtered audit logs as a CSV download.
     */
    public function export(Request $request): StreamedResponse
    {
        $logs = $this->buildQuery($request)->with('user')->orderByDesc('created_at')->get();

        $filename = 'audit-logs-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'User', 'Action', 'Model', 'Model ID']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    optional($log->created_at)->format('Y-m-d H:i:s'),
                    $log->user->name ?? 'Unknown',
                    $log->action,
                    $log->model_type,
                    $log->model_id,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Shared query builder honoring the action / date-range filters and the
     * current university scope.
     */
    private function buildQuery(Request $request)
    {
        $universityId = $request->query('university_id', session('university_id'));

        $query = AuditLog::query();

        if ($universityId) {
            $query->where('university_id', $universityId);
        }

        if ($action = $request->query('action_type')) {
            $query->where('action', $action);
        }

        if ($start = $request->query('start_date')) {
            $query->whereDate('created_at', '>=', $start);
        }

        if ($end = $request->query('end_date')) {
            $query->whereDate('created_at', '<=', $end);
        }

        return $query;
    }
}
