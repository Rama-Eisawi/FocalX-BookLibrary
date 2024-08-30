<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     *
     * @param $data an array containing the data required to create a new user.
     * @return User: The function returns an instance of the newly created User model containing the saved user’s details.
     */
    public function createUser($data)
    {
        return User::create($data);
    }
    //----------------------------------------------------------------------------------------
    /**
     * @param User $user: The instance of the User model that is being updated.
     * @param array $data: An associative array containing the user data to be updated. Possible keys include:
     */
    public function updateUser(User $user, $data)
    {
        $user->update($data);
        return $user;
    }
    //----------------------------------------------------------------------------------------
    /**
     * @param User $user: The instance of the User model that is being updated.
     */
    public function deleteUser(User $user)
    {
        $user->delete();
    }
}
