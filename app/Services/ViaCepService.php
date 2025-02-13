<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ViaCepService
{
    /**
     *
     * @param string $cep
     * @return array|null
     */
    public function getAddressFromCep(string $cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);

        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");

        if ($response->successful() && !isset($response['erro'])) {
            return $response->json();
        }

        return null;
    }
}
