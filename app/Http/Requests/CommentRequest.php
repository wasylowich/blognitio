<?php

namespace Blognitio\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        switch ($this->method()) {
            case 'GET':
                break;

            case 'POST':
                break;

            case 'PUT':
            case 'PATCH':
                break;

            case 'DELETE':
                break;

            default:
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                return [];

            case 'POST':
                return [
                    'body' => 'required',
                ];

            case 'PUT':
            case 'PATCH':
                return [];

            default:
                return [];
        }
    }

    /**
     * Override the response on failed validation to return json
     *
     * @param  Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $jsonResponse = response()->json(['errors' => $validator->errors()], 422);

        throw new HttpResponseException($jsonResponse);
    }
}
