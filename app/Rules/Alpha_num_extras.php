<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Alpha_num_extras implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match("/^[\pL\pN\s'_-]*$/u", $value);
        /*
            \pL letras cualquier lenguaje
            \pN numeros
            \s espacios, tabs, etc
            '_-
            * puede estar vacío el string
            ^$ del inicio al final del string?
            / / comienza y termina el regex
            u unicode?
        */
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute may only contain letters, numbers, spaces, dashes and underscores.';
    }
}
