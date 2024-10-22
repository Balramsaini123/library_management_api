<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use JsonResponseTrait;
    protected $authService;


/**
 * AuthController constructor.
 *
 * @param AuthService $authService The authentication service instance.
 */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  RegisterRequest  $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());

            return $this->successResponse($user, 'messages.user.register', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('messages.user.registration', 500);
        }
    }

    /**
     * Handle a login request for the application.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $token = $this->authService->login($credentials);
            if ($token) {
                return $this->successResponse([
                    'token' => $token,
                    'data' => Auth::user()
                ], 'messages.user.login', 200);
            }
            return $this->errorResponse('messages.error.login.invalid_credentials', 401);
        } catch (\Exception $e) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if ($user) {
                $user->token()->revoke();

                return $this->successResponse(null, 'messages.user.logout', 200);
            }
            return $this->errorResponse('messages.error.logout.unauthenticated', 401);
        } catch (\Exception $e) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }

    /**
     * List all users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function usersList(Request $request)
    {
        $this->authorize('readUserList', User::class);
        try {
            $searchTerm = $request->input('search');

            $users = $this->authService->getAllUsers($searchTerm);

            return $this->successResponse($users, 'messages.user.users', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('messages.user.faild', 500);
        }
    }

    /**
     * Retrieve a user by its UUID.
     *
     * @param string $uuid The user uuid.
     *
     * @return JsonResponse
     */
    public function user($uuid)
    {
        $this->authorize('accessAllRoutes', User::class);
        try {
            $user = $this->authService->getUserByUuid($uuid);
            if (!$user) {
                return $this->errorResponse('messages.user.notfound', 404);
            }

            return $this->successResponse($user, 'messages.user.users', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }

    /**
     * Update a user.
     *
     * @param UserUpdateRequest $request The validated request containing the user data.
     * @param string $uuid The user uuid.
     *
     * @return JsonResponse
     */
    public function updateUser(UserUpdateRequest $request, $uuid)
    {
        $this->authorize('accessAllRoutes', User::class);
        try {
            $user = $this->authService->getUserByUuid($uuid);
            if (!$user) {
                return $this->errorResponse('messages.user.notfound', 404);
            }

            $updateUser = $this->authService->updateUser($user, $request->validated());
            return $this->successResponse($updateUser, 'messages.user.update', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }

    /**
     * Delete a user.
     *
     * @param string $uuid The user uuid.
     *
     * @return JsonResponse
     */
    public function deleteUser($uuid)
    {
        $this->authorize('accessAllRoutes', User::class);
        try {
            $user = $this->authService->getUserByUuid($uuid);
            if (!$user) {
                return $this->errorResponse('messages.user.notfound', 404);
            }

            $this->authService->deleteUser($user);
            return $this->successResponse(null, 'messages.user.delete', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }
}
