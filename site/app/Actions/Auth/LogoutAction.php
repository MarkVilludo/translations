<?php

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Action;
use Illuminate\Http\JsonResponse;

class LogoutAction extends Action
{
    use AsController;

    public function asController(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
