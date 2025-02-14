<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCep implements Rule
{
    /**
     * Determina se a validação passa.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Valida o formato: 5 dígitos, um hífen, e 3 dígitos
        return preg_match('/^\d{5}-\d{3}$/', $value) === 1;
    }

    /**
     * Mensagem de erro para validação.
     *
     * @return string
     */
    public function message()
    {
        return 'O CEP fornecido é inválido.';
    }
}

