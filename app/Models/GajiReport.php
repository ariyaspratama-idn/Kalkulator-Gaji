<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GajiReport extends Model
{
    protected $fillable = ['periode', 'gaji_pokok', 'total_pengeluaran', 'sisa_bersih'];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
