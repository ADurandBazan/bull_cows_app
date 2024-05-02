<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoRepeatedCharacters;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="StoreGameRequest",
 *     description="Game data",
 *     type="object",
 *     required={"user", "age"},
 *     @OA\Property(property="user", type="string"),
 *     @OA\Property(property="age", type="integer")
 * )
 */
class StoreGameRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user' => 'required|string',
            'age' => 'required|integer|min:1|max:150',
        ];
    }

    public function messages()
    {
        return [
            'age.min' => 'The age must be greater than 0.',
            'age.max' => 'The age must be lesser than 150.',
        ];
    }
}
