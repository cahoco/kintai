<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakCorrection extends Model
{
    protected $fillable = [
        'stamp_correction_request_id',
        'break_start',
        'break_end',
    ];

    public function request()
    {
        return $this->belongsTo(StampCorrectionRequest::class, 'stamp_correction_request_id');
    }
}
