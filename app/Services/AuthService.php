<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user.
     *
     * @param array $data The user data to be registered.
     * @return mixed
     */
    public function register(array $data)
    {
        $data['role'] = $data['role'] ?? 3;
        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->create($data);
    }

    /**
     * Attempt to login a user.
     *
     * @param array $credentials The user credentials (email and password).
     * @return string|null The access token if the login is successful, null otherwise.
     */
    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            return Auth::user()->createToken('login')->accessToken;
        }

        return null;
    }

    /**
     * Get all users, or search for users by the given search term.
     *
     * @param string|null $searchTerm The search term to search for users.
     * @return \Illuminate\Database\Eloquent\Collection The collection of users.
     */
    public function getAllUsers($searchTerm = null)
    {
        if ($searchTerm) {
            return $this->userRepository->searchUsers($searchTerm);
        }

        return $this->userRepository->getAll();
    }

    /**
     * Get a user by its UUID.
     *
     * @param string $uuid The user UUID.
     * @return \App\Models\User|null The user if found, null otherwise.
     */
    public function getUserByUuid(string $uuid)
    {
        return $this->userRepository->findByUuid($uuid);
    }

    /**
     * Update a user.
     *
     * @param \App\Models\User $user The user to be updated.
     * @param array $data The user data to be updated.
     * @return \App\Models\User The updated user.
     */
    public function updateUser($user, array $data)
    {
        return $this->userRepository->update($user, $data);
    }

    /**
     * Delete a user.
     *
     * @param mixed $user The user data to be deleted.
     */
    public function deleteUser($user)
    {
        return $this->userRepository->delete($user);
    }
}
