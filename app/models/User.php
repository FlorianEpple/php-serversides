<?php

namespace app\models;

use lib\database\Model;

class User extends Model
{
    public string $table = 'users';
    public string $primaryKey = 'id';

    public array $fillable = [
        // your fillable here
    ];

    public array $restricted = [
        // your restricted here
    ];
}
