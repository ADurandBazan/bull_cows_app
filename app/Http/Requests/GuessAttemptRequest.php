<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoRepeatedCharacters;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="GuessAttemptRequest",
 *     description="Guess Attempt data",
 *     type="object",
 *     required={"proposal"},
 *     @OA\Property(property="proposal", type="string", maxLength=4,
 *     pattern="^[0-9]*$") 
 * )
 */
class GuessAttemptRequest extends FormRequest
{
    public function rules()
    {
        return [
            'proposal' =>  ['required', 
                            'string', 
                            'size:4', 
                            'regex:/^[0-9]*$/',
                             new NoRepeatedCharacters()]
        ];
    }
}