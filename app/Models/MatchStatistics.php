<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchStatistics extends Model
{
    use HasFactory;
    protected $table = 'match_statistics';
    protected $guarded = [];

    public function match()
    {
        return $this->belongsTo(Matches::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
