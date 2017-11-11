<?php

namespace Rainwater\Active\Tests;

use Illuminate\Database\Eloquent\Model;

class FakeUser extends Model
{
    protected $guarded = [];
    protected $table = 'users';
}
