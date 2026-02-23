<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefMateri extends Model
{
    // No timestamps needed for this reference table based on Seeder, but migration didn't add them?
    // Wait, I replaced timestamps() with nothing in my migration edit above?
    // Let me check migration content again.
    // Ah, I replaced Schema::create... timestamps() was inside.
    // My replacement code REMOVED timestamps(). So I should disable them here.
    
    public $timestamps = false;
    protected $table = 'ref_materi';
    protected $fillable = ['kategori', 'materi'];
}
