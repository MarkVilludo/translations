<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Auth\LoginRequest;
use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Action;
use Symfony\Component\HttpFoundation\Response;

class LoginAction extends Action
{
    use AsController;

    public function handle(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function asController(LoginRequest $request): JsonResponse
    {
        $data = $this->handle($request->validated());

        return response()->json($data, Response::HTTP_OK); // 200 OK
    }
}
