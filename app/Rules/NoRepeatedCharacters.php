<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoRepeatedCharacters implements Rule
{
    public function passes($attribute, $value)
    {
        $chars = str_split($value);
        $uniqueChars = array_unique($chars);

        return count($chars) === count($uniqueChars);
    }

    public function message()
    {
        return 'The :attribute should not have repeated characters.';
    }
}