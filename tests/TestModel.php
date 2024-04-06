<?php

declare(strict_types=1);

namespace Asseco\JsonQueryBuilder\Tests;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
}



class TestModel extends Model
{
    protected $table = 'test';

    public function tags() {
        return $this->morphToMany(Tag::class, "taggable");
    }
}
