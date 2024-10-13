<?php

namespace App\Repositories\Posts;

use App\Models\post;

interface PostInterface
{
    public function all(array $filters = [], $pagination = 10, $sortBy = 'created_at', $sortOrder = 'desc');
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
