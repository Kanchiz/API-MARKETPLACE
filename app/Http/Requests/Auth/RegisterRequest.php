<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['nullable', 'string', 'in:buyer,seller'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electronico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electronico valido.',
            'email.unique' => 'El correo ya esta registrado.',
            'password.required' => 'La contrasena es obligatoria.',
            'password.min' => 'La contrasena debe contener al menos 8 caracteres.',
            'role.in' => 'El rol seleccionado no es valido.',
        ];
    }

    // manejo de errores 409 para correo duplicado y 422 para fallos de validacion
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        // si el fallo fue por unicidad de email responde 409 conflict
        $statusCode = ($errors->has('email') && str_contains(implode(' ', $errors->get('email')), 'ya esta registrado'))
            ? 409
            : 422;

        throw new HttpResponseException(response()->json([
            'message' => $statusCode === 409 ? 'El correo ya esta registrado.' : 'Error de validacion.',
            'errors' => $errors,
        ], $statusCode));
    }
}