<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCep implements Rule
{
    /**
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^\d{5}-\d{3}$/', $value) === 1;
    }

    /**
     *
     * @return string
     */
    public function message()
    {
        return 'O CEP fornecido é inválido.';
    }
}

