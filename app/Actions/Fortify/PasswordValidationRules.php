<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;
// use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function passwordRules()
    {
        // return ['required', 'string', new Password, 'confirmed', 'min:8', 'max:255', 'mixedCase'];
        return [
            'required',
            'string',
            'confirmed',
            'max:255',
            Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(3)
        ];
    }
}
