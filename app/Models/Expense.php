<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['gaji_report_id', 'nama_item', 'nominal', 'kategori'];

    public function gajiReport()
    {
        return $this->belongsTo(GajiReport::class);
    }
}
