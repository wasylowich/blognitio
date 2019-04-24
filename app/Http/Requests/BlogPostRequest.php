<?php

namespace Blognitio\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class BlogPostRequest extends FormRequest
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
                if (auth()->user()->id != $this->route()->post->user_id) {
                    return false;
                }
                break;

            case 'DELETE':
                if (auth()->user()->id != $this->route()->post->user_id) {
                    return false;
                }
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
                    'title' => 'required',
                    'body'  => 'required',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    'title' => 'required',
                    'body'  => 'required',
                ];

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
