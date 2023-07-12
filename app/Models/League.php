<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'league_team');
    }
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_league', 'league_id', 'user_id');
    }
    public function matches()
    {
        return $this->hasMany(Matches::class)->orderBy('week');
    }
    public function getStats()
    {
        $statsFields = ['played', 'won', 'drawn', 'lost', 'scored', 'conceded', 'points'];
        $teamStats = array_fill_keys($statsFields, 0);
        $teamHomeStats = array_fill_keys($statsFields, 0);
        $teamAwayStats = array_fill_keys($statsFields, 0);

        foreach ($this->teams as $team) {

            foreach ($statsFields as $field) {
                $teamStats[$field] = 0;
                $teamHomeStats[$field] = 0;
                $teamAwayStats[$field] = 0;
            }
            $teamMatches = $this->matches()
                ->where(function ($query) use ($team) {
                    $query->where('home_team_id', $team->id)
                        ->orWhere('away_team_id', $team->id);
                })
                ->get();

            foreach ($teamMatches as $match) {
                $isHomeTeam = ($match->home_team_id == $team->id);
                $isAwayTeam = ($match->away_team_id == $team->id);

                $teamStats['played']++;

                if ($isHomeTeam) {
                    $teamHomeStats['played']++;
                    $teamHomeStats['scored'] += $match->home_team_score;
                    $teamHomeStats['conceded'] += $match->away_team_score;

                    $teamStats['scored'] += $match->home_team_score;
                    $teamStats['conceded'] += $match->away_team_score;

                    if ($match->home_team_score > $match->away_team_score) {
                        $teamHomeStats['won']++;
                        $teamHomeStats['points'] += 3;

                        $teamStats['won']++;
                        $teamStats['points'] += 3;
                    } elseif ($match->home_team_score < $match->away_team_score) {
                        $teamHomeStats['lost']++;

                        $teamStats['lost']++;
                    } else {
                        $teamHomeStats['drawn']++;
                        $teamHomeStats['points'] += 1;

                        $teamStats['drawn']++;
                        $teamStats['points'] += 1;
                    }
                } elseif ($isAwayTeam) {
                    $teamAwayStats['played']++;
                    $teamAwayStats['scored'] += $match->away_team_score;
                    $teamAwayStats['conceded'] += $match->home_team_score;

                    $teamStats['scored'] += $match->away_team_score;
                    $teamStats['conceded'] += $match->home_team_score;

                    if ($match->away_team_score > $match->home_team_score) {
                        $teamAwayStats['won']++;
                        $teamAwayStats['points'] += 3;

                        $teamStats['won']++;
                        $teamStats['points'] += 3;
                    } elseif ($match->away_team_score < $match->home_team_score) {
                        $teamAwayStats['lost']++;

                        $teamStats['lost']++;
                    } else {
                        $teamAwayStats['drawn']++;
                        $teamAwayStats['points'] += 1;

                        $teamStats['drawn']++;
                        $teamStats['points'] += 1;
                    }
                }
            }

            $team->played = $teamStats['played'];
            $team->won = $teamStats['won'];
            $team->drawn = $teamStats['drawn'];
            $team->lost = $teamStats['lost'];
            $team->scored = $teamStats['scored'];
            $team->conceded = $teamStats['conceded'];
            $team->points = $teamStats['points'];

            $team->h_played = $teamHomeStats['played'];
            $team->h_won = $teamHomeStats['won'];
            $team->h_drawn = $teamHomeStats['drawn'];
            $team->h_lost = $teamHomeStats['lost'];
            $team->h_scored = $teamHomeStats['scored'];
            $team->h_conceded = $teamHomeStats['conceded'];
            $team->h_points = $teamHomeStats['points'];

            $team->a_played = $teamAwayStats['played'];
            $team->a_won = $teamAwayStats['won'];
            $team->a_drawn = $teamAwayStats['drawn'];
            $team->a_lost = $teamAwayStats['lost'];
            $team->a_scored = $teamAwayStats['scored'];
            $team->a_conceded = $teamAwayStats['conceded'];
            $team->a_points = $teamAwayStats['points'];
        }

        return $this->teams;
    }

}
