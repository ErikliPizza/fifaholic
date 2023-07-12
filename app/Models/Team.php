<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Team extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function leagues()
    {
        return $this->belongsToMany(League::class, 'league_team');
    }

    //used by a collection over the team controller
    public function matchStatistics()
    {
        return $this->hasMany(MatchStatistics::class);
    }
    public function matches()
    {
        return $this->hasMany(Matches::class, 'home_team_id')->orWhere('away_team_id', $this->id);
    }
    public function getTitleAttribute($title)
    {
        $words = explode(' ', $title);

        if (count($words) > 1) {
            foreach ($words as &$word) {
                // Check the length of the word
                if (mb_strlen($word) > 4) {
                    // Shorten the word to 4 characters and add a prefix
                    $word = mb_substr($word, 0, 4);
                }
            }
            // Combine the modified words into a new string
            $modifiedTitle = implode(' ', $words);
        } else {
            if (mb_strlen($title) > 7) {
                // Shorten the word to 4 characters and add a prefix
                $modifiedTitle = mb_substr($title, 0, 7);
            } else {
                $modifiedTitle = $title;
            }
        }
        return $modifiedTitle;
    }


}
