<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function filterUser(array $filters)
    {
        $query = User::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = strtolower($filters['sort_order'] ?? 'asc');
        $allowedSorts = ['created_at', 'name', 'role'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');

        $perPage = $filters['per_page'] ?? 10;

        if ($perPage === 'all') {
            return $query->get();
        }

        return $query->paginate((int)$perPage);
    }
}
