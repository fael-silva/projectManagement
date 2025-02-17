<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\Address;
use App\Services\ViaCepService;
use Illuminate\Http\Request;
use App\Notifications\TaskCompletedNotification;

class ProjectController extends Controller
{
    protected $viaCepService;

    public function __construct(ViaCepService $viaCepService)
    {
        $this->viaCepService = $viaCepService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cep' => 'required|string|max:9',
            'tasks' => 'nullable|array',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
        ]);

        $formattedCep = substr_replace($validated['cep'], '-', 5, 0);

        $address = Address::where('cep', $formattedCep)->first();

        if (!$address) {
            $viaCepData = $this->viaCepService->getAddressFromCep($validated['cep']);
            if (!$viaCepData) {
                return response()->json(['error' => 'CEP inválido ou não encontrado.'], 404);
            }

            $address = Address::create([
                'cep' => $viaCepData['cep'],
                'logradouro' => $viaCepData['logradouro'],
                'complemento' => $viaCepData['complemento'] ?? null,
                'bairro' => $viaCepData['bairro'],
                'localidade' => $viaCepData['localidade'],
                'uf' => $viaCepData['uf'],
                'estado' => $viaCepData['uf'],
                'regiao' => $viaCepData['regiao'] ?? null,
                'ibge' => $viaCepData['ibge'],
                'ddd' => $viaCepData['ddd'] ?? null,
                'siafi' => $viaCepData['siafi'] ?? null,
            ]);
        }

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'start_date' => now(),
            'status' => 'planejado',
            'user_id' => auth()->id(),
            'address_id' => $address->id,
        ]);

        if (!empty($validated['tasks'])) {
            foreach ($validated['tasks'] as $taskData) {
                $project->tasks()->create($taskData);
            }
        }

        return response()->json($project->load('tasks', 'address'), 201);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 10);


        if ($user->can('view all projects')) {
            $projects = Project::with(['tasks', 'address'])->paginate($perPage);
        } elseif ($user->can('view own projects')) {
            $projects = Project::where('user_id', $user->id)
                ->with(['tasks', 'address'])
                ->paginate($perPage);
        } else {
            return response()->json(['error' => 'Você não tem permissão para acessar os projetos.'], 403);
        }

        return response()->json($projects, 200);
    }

    public function show($id)
    {
        $project = Project::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['tasks', 'address'])
            ->first();

        if (!$project) {
            return response()->json(['error' => 'Projeto não encontrado'], 404);
        }

        return response()->json($project, 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:planejado,em andamento,concluído',
            'cep' => 'nullable|string|max:9',
            'tasks' => 'nullable|array',
            'tasks.*.id' => 'nullable|exists:tasks,id',
            'tasks.*.title' => 'nullable|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.status' => 'nullable|string|in:pendente,em andamento,concluída',
            'tasks_to_remove' => 'nullable|array',
            'tasks_to_remove.*' => 'exists:tasks,id',
        ]);

        $project = Project::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$project) {
            return response()->json(['error' => 'Projeto não encontrado'], 404);
        }

        if (isset($validated['name'])) $project->name = $validated['name'];
        if (isset($validated['description'])) $project->description = $validated['description'];
        if (isset($validated['status'])) {
            $project->status = $validated['status'];
            if ($validated['status'] === 'concluído') {
                $project->end_date = now();
            }
        }

        if (isset($validated['cep'])) {
            $formattedCep = substr_replace($validated['cep'], '-', 5, 0);
            $address = Address::where('cep', $formattedCep)->first();

            if (!$address) {
                $viaCepData = $this->viaCepService->getAddressFromCep($formattedCep);
                if (!$viaCepData) {
                    return response()->json(['error' => 'CEP inválido ou não encontrado.'], 404);
                }
                $address = Address::create([
                    'cep' => $viaCepData['cep'],
                    'logradouro' => $viaCepData['logradouro'],
                    'bairro' => $viaCepData['bairro'],
                    'localidade' => $viaCepData['localidade'],
                    'uf' => $viaCepData['uf'],
                    'complemento' => $viaCepData['complemento'] ?? null,
                ]);
            }

            $project->address_id = $address->id;
        }

        $project->save();

        if (isset($validated['tasks_to_remove'])) {
            foreach ($validated['tasks_to_remove'] as $taskId) {
                $task = Task::where('id', $taskId)
                    ->where('project_id', $project->id)
                    ->first();

                if ($task) {
                    $task->delete();
                }
            }
        }

        if (isset($validated['tasks'])) {
            foreach ($validated['tasks'] as $taskData) {
                if (isset($taskData['id'])) {
                    $task = Task::find($taskData['id']);
                    if (isset($taskData['title'])) $task->title = $taskData['title'];
                    if (isset($taskData['description'])) $task->description = $taskData['description'];

                    if (isset($taskData['status'])) {
                        $task->status = $taskData['status'];
                        if ($taskData['status'] === 'concluída') {
                            $task->completed_at = now();
                            // $user = $task->project->user;
                            // $user->notify(new TaskCompletedNotification($task));
                        } else {
                            $task->completed_at = null;
                        }
                    }

                    $task->save();
                } else {
                    $project->tasks()->create([
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'status' => $taskData['status'] ?? 'pendente',
                    ]);
                }
            }
        }

        return response()->json($project->load('tasks', 'address'), 200);
    }

    public function destroy($id)
    {
        $project = Project::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$project) {
            return response()->json(['error' => 'Projeto não encontrado'], 404);
        }

        $project->tasks()->delete();

        $project->delete();

        return response()->json(['message' => 'Projeto e tarefas deletados com sucesso!'], 200);
    }

}
