<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\Address;
use App\Services\ViaCepService;
use Illuminate\Http\Request;

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
                'estado' => $viaCepData['uf'], // Ajustar se necessário
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
}
