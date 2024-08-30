<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Show all users.
     */
    public function index()
    {
        $users = User::all();
        return ApiResponse::success($users, 'Users retrieved successfully');
    }
    //----------------------------------------------------------------------------------------
    /**
     * Create a new user.
     */
    public function store(UserFormRequest $request)
    {
        $userRequest = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ];
        $newUser = $this->userService->createUser($userRequest);
        return ApiResponse::success($newUser, 'User created successfully', 201);
    }
    //----------------------------------------------------------------------------------------
    /**
     * Show a specific user.
     */
    public function show(User $user)
    {
        $user->findOrFail($user->id);
        return ApiResponse::success($user, 'User retrieved successfully');
    }
    //----------------------------------------------------------------------------------------
    /**
     * Update a specific user.
     */
    public function update(UserFormRequest $request, User $user)
    {
        $updatedUser = $this->userService->updateUser($user, $request->validated());
        return ApiResponse::success($user, 'User updated successfully');
    }
    //----------------------------------------------------------------------------------------
    /**
     * Delete a specific user.
     */
    public function destroy(User $user)
    {
        $this->userService->deleteUser($user);
        return ApiResponse::success(null, 'User deleted successfully');
    }
}
