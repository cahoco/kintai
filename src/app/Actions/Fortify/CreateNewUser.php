<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        // ✅ 自作RegisterRequestを明示的に使う
        $request = app(RegisterRequest::class);
        $request->merge($input);

        Validator::make(
            $request->all(),
            $request->rules(),
            $request->messages()
        )->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'is_admin' => false,
        ]);

        Auth::login($user);

        return $user;
    }
}
