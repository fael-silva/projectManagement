<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ViaCepService;
use Illuminate\Http\Request;

class CepController extends Controller
{
    protected $viaCepService;

    public function __construct(ViaCepService $viaCepService)
    {
        $this->viaCepService = $viaCepService;
    }

    /**
     *
     * @param string $cep
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAddress(string $cep)
    {
        $address = $this->viaCepService->getAddressFromCep($cep);

        if (!$address) {
            return response()->json(['error' => 'CEP não encontrado ou inválido.'], 404);
        }

        return response()->json($address);
    }
}

