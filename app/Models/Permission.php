<?php

namespace App\Models;

use CodeIgniter\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'role_id', 'menu_id'
    ];
}