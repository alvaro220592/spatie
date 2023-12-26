<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Functionality extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function permissions(){
        return $this->hasMany(Permission::class, 'functionality_id');
    }
}
