<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepository as UserRepositoryContract;
use Illuminate\Support\Collection;

class EloquentUserRepository implements UserRepositoryContract
{
    public function listWithRole(): Collection
    {
        return User::query()
            ->select(['id', 'name', 'email', 'created_at'])
            ->with('roles:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->select(['id', 'name', 'email', 'password'])
            ->where('email', $email)
            ->first();
    }

    public function findById(int $id): ?User
    {
        return User::query()
            ->select(['id', 'name', 'email'])
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = User::query()->findOrFail($id);
        $user->update($data);

        return $user->fresh();
    }

    public function delete(int $id): bool
    {
        return User::query()->where('id', $id)->delete();
    }
}
