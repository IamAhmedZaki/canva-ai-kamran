<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
     protected $fillable = [
        'canva_design_id',
        'title',
        'asset_type',
        'edit_url',
        'export_url',
        'local_path'
    ];
}
