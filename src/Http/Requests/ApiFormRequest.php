<?php

declare(strict_types=1);

namespace Gamebetr\Api\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Class ApiFormRequest.
 */
class ApiFormRequest extends FormRequest
{
    /**
     * {@inheritdoc}
     */
    protected function failedValidation(Validator $validator): void
    {
        // We force the error to be in a typical JSON:API format and
        // then re-throw it.
        $jsonResponse = response()->json(
            (object)[
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );

        throw new ValidationException($validator, $jsonResponse, $this->errorBag);
    }

}
