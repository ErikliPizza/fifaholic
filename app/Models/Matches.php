<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function comments()
    {
        return $this->hasMany(Comment::class, 'match_id');
    }
    public function league()
    {
        return $this->belongsTo(League::class);
    }
    public function statistics()
    {
        return $this->hasMany(MatchStatistics::class, 'match_id');
    }
    public function getHomeTeamStatAttribute()
    {
        return $this->statistics()->where('team_id', $this->home_team_id)->first();
    }

    public function getAwayTeamStatAttribute()
    {
        return $this->statistics()->where('team_id', $this->away_team_id)->first();
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

}
