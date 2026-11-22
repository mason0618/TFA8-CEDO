<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $rules = [
            'username' => ['bail', 'required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user)],
            'full_name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => $user && $user->password
                ? ['prohibited']
                : ['required', 'confirmed', Password::defaults()],
        ];

        if ($user) {
            $rules['avatar'] = [
                'bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,bmp', 'max:2048',
                'dimensions:max_width=4096,max_height=4096',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return ['full_name' => 'full name', 'avatar' => 'profile picture'];
    }

    public function messages(): array
    {
        return [
            'avatar.max' => 'The profile picture must not be larger than 2 MB.',
            'avatar.dimensions' => 'The profile picture must be no larger than 4096 by 4096 pixels.',
            'password.prohibited' => 'Existing passwords must be changed through the account owner\'s Profile page.',
        ];
    }
}
