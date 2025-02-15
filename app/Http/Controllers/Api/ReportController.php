<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function projectsReport(Request $request)
    {
        try {
            $startDateFrom = $request->query('start_date_from');
            $startDateTo = $request->query('start_date_to');

            $projectQuery = Project::query();

            if ($startDateFrom) {
                $projectQuery->where('start_date', '>=', $startDateFrom);
            }
            if ($startDateTo) {
                $projectQuery->where('start_date', '<=', $startDateTo);
            }

            $totalProjects = $projectQuery->count();

            $projectIds = $projectQuery->pluck('id')->toArray();

            $projectsByStatus = $projectQuery->selectRaw("status, COUNT(*) as count")
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $projectsByStatus = [
                'planejado' => $projectsByStatus['planejado'] ?? 0,
                'em andamento' => $projectsByStatus['em andamento'] ?? 0,
                'concluído' => $projectsByStatus['concluído'] ?? 0,
            ];

            if (empty($projectIds)) {
                $totalTasks = 0;
                $tasksByStatus = [
                    'pendente' => 0,
                    'em andamento' => 0,
                    'concluída' => 0,
                ];
            } else {
                $taskQuery = Task::whereIn('project_id', $projectIds);

                $totalTasks = $taskQuery->count();

                $tasksByStatus = [
                    'pendente' => (clone $taskQuery)->where('status', 'pendente')->count(),
                    'em andamento' => (clone $taskQuery)->where('status', 'em andamento')->count(),
                    'concluída' => (clone $taskQuery)->where('status', 'concluída')->count(),
                ];
            }

            $report = [
                'total_projects' => $totalProjects,
                'projects_by_status' => $projectsByStatus,
                'total_tasks' => $totalTasks,
                'tasks_by_status' => $tasksByStatus,
            ];

            return response()->json($report, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar relatório', 'message' => $e->getMessage()], 500);
        }
    }
}
