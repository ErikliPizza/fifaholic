<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchTeam extends Model
{
    use HasFactory;
    protected $table = 'match_team';
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
