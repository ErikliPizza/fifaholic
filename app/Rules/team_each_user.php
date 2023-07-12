<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use App\Models\Team;
class team_each_user implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $team;
    protected $title;
    public function __construct($team = null, $title = null)
    {
        $this->team = $team;
        $this->title = $title;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $userId = Auth::id();

        if($this->team) {

            foreach ($this->team as $index => $item) {
                $value = $this->title[$index];
                $team_id = $item;
                if (Team::where('user_id', $userId)
                        ->where('title', $value)
                        ->whereNotIn('id', [$team_id])
                        ->count() > 0) {
                    $fail('The :attribute must be unique.');
                }
            }

        } else {
            if (Team::where('user_id', $userId)
                    ->where($attribute, $value)
                    ->count() > 0) {
                $fail('The :attribute must be unique.');
            }
        }

    }
}
