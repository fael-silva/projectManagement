<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidStatus implements Rule
{
    protected $validStatuses = ['planejado', 'em andamento', 'concluído'];

    /**
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, $this->validStatuses);
    }

    /**
     *
     * @return string
     */
    public function message()
    {
        return 'O status fornecido é inválido.';
    }
}
