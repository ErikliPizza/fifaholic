<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Rules\alpha_spaces;
use App\Rules\team_each_user;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\League;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the teams and leagues for the authenticated user.
     *
     */
    public function index()
    {
        // Retrieve teams and leagues for the authenticated user, ordered by creation date
        $teams = Auth::user()->teams()->orderBy('created_at', 'desc')->get();
        $leagues = Auth::user()->leagues;
        if(count($leagues)<1) {
            return redirect('/dashboard')->with('info', 'Please create a league before add a team');
        }
        return view('teams', [
            'teams' => $teams,
            'leagues' => $leagues,
        ]);
    }

    /**
     * Display the team details and statistics.
     *
     * @param Team $team The team to display.
     * @return Renderable The rendered view.
     */
    public function show(Team $team): Renderable
    {
        foreach ($team->leagues as $lg)
        {
            $lg->getStats();
        }

        $collection = collect($team->matchStatistics);
        $statistics = [];
        $winCount = 0;
        $drawCount = 0;
        $lossCount = 0;
        foreach ($team->matches as $match) {
            if ($match->home_team_id == $team->id) {
                if ($match->home_team_score > $match->away_team_score) {
                    $winCount++;
                } elseif ($match->home_team_score < $match->away_team_score) {
                    $lossCount++;
                } else {
                    $drawCount++;
                }
            } elseif ($match->away_team_id == $team->id) {
                if ($match->away_team_score > $match->home_team_score) {
                    $winCount++;
                } elseif ($match->away_team_score < $match->home_team_score) {
                    $lossCount++;
                } else {
                    $drawCount++;
                }
            }
        }
        $statistics['win'] = $winCount;
        $statistics['loss'] = $lossCount;
        $statistics['draw'] = $drawCount;
        $possession_count = $collection->pluck('possession')->filter(function ($possession) { return $possession != null && $possession > 0; })->count();
        if ($possession_count > 0) $statistics['possession'] = ($collection->sum('possession') / $possession_count) . " / $possession_count";
        $statistics['shots'] = $collection->sum('shots');
        $statistics['expected_goals'] = $collection->sum('expected_goals');
        $statistics['passes'] = $collection->sum('passes');
        $statistics['tackles'] = $collection->sum('tackles');
        $statistics['tackles_won'] = $collection->sum('tackles_won');
        $statistics['interceptions'] = $collection->sum('interceptions');
        $statistics['saves'] = $collection->sum('saves');
        $statistics['fouls_committed'] = $collection->sum('fouls_committed');
        $statistics['offsides'] = $collection->sum('offsides');
        $statistics['corners'] = $collection->sum('corners');
        $statistics['free_kicks'] = $collection->sum('free_kicks');
        $statistics['penalty_kicks'] = $collection->sum('penalty_kicks');
        $statistics['yellow_cards'] = $collection->sum('yellow_cards');
        $statistics['red_cards'] = $collection->sum('red_cards');

        return view('team', [
            'team' => $team,
            'statistics' => $statistics
        ]);
    }


    /**
     * Store new teams.
     *
     * @return RedirectResponse Redirect back with a success message.
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function store(): RedirectResponse
    {
        // Remove multiple spaces from the title and replace with a single space
        $normalizedString = preg_replace('/\s+/', ' ', request()['title']);

        // Split the comma-separated string into an array of titles
        $title_raw = explode(',', $normalizedString);

        // Trim whitespace from each title
        $title = array_map('trim', $title_raw);

        // Update the 'title' input in the request with the array of titles
        request()->merge(['title' => $title]);

        // Validate the input data
        $validator = Validator::make(request()->all(), [
            // The 'title' input must be present and an array
            'title' => ['required', 'array'],
            // Each item in the 'title' array must be present, a string, contain only letters and spaces, be between 3 and 16 characters long, and be unique within the array
            'title.*' => [
                'required',
                'string',
                'min:3',
                'max:36',
                new team_each_user(request()->input('title.*'), request()->input('title.*')), // Custom validation rule for checking if the team title exists for the authenticated user
                function ($attribute, $value, $fail) {
                    $titleCounts = array_count_values(request()['title']);
                    if ($titleCounts[$value] > 1) {
                        $fail('Each title must be unique.');
                    }
                }
            ],
            // The 'league_id' input must be present
            'league_id' => 'required'
        ])->stopOnFirstFailure();

        // Set the attribute names for error messages
        $validator->setAttributeNames(['title.*' => 'title']);

        // Get the validated input data
        $attributes = $validator->validate();

        // Check league ownership
        $this->league_ownership($attributes['league_id']);

        // Create a new team for each title and attach it to the league
        foreach ($attributes['title'] as $title) {
            $team = new Team([
                'user_id' => Auth::id(),
                'title' => transliterator_create("tr-Upper")->transliterate($title)
            ]);
            $team->save();
            $team->leagues()->attach($attributes['league_id']);
        }

        // Redirect back with a success message
        return back()->with('success', 'Teams Created');
    }


    /**
     * Update teams' details.
     *
     * @return RedirectResponse Redirect back with a success message.
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(): RedirectResponse
    {
        // Validation rules for updating teams
        $validator = Validator::make(request()->all(), [
            'teams' => 'required|array',
            'teams.*.id' => 'required',
            'teams.*.title' => [
                'required',
                'string',
                'min:3',
                'max:36',
                new team_each_user(request()->input('teams.*.id'), request()->input('teams.*.title')), // Custom validation rule for checking if the team title exists for the authenticated user
                function ($attribute, $value, $fail) {
                    // Custom validation rule for checking if each team title is unique
                    $titleCounts = array_count_values(array_column(request()->input('teams'), 'title'));
                    if ($titleCounts[$value] > 1) {
                        $fail('Each title must be unique.');
                    }
                }
            ],
            'teams.*.league_id' => 'required',
            'teams.*.delete' => 'nullable'
        ])->stopOnFirstFailure();

        // Custom validation error message attributes
        $validator->setAttributeNames([
            'teams.*.title' => 'title',
            'teams.*.league_id' => 'league',
            'teams.*.id' => 'team id'
        ]);

        // Get validated data
        $attributes = $validator->validate();

        // Loop through each team and update the details
        foreach ($attributes['teams'] as $dataTeam) {
            // Check if the current user owns the team
            $this->team_ownership($dataTeam['id']);
            // Check if the current user owns the league
            $this->league_ownership($dataTeam['league_id']);

            // Update the leagues that the team belongs to
            Team::find($dataTeam['id'])->leagues()->sync($dataTeam['league_id']);
            $team = Team::find($dataTeam['id']);

            if (isset($dataTeam['delete'])) {
                // If the delete flag is set, delete the team
                $this->destroy($team);
            } else {
                // Otherwise, update the team title
                $team->update([
                    'title' => transliterator_create("tr-Upper")->transliterate($dataTeam['title'])
                ]);
            }
        }

        // Redirect back with success message
        return back()->with('success', 'Teams Updated');
    }


    /**
     * Delete a team from the database.
     *
     * @param Team $team The team to be deleted.
     * @return RedirectResponse Redirect back with a success message.
     * @throws AuthorizationException
     */
    public function destroy(Team $team): RedirectResponse
    {
        // Authorize that the current user has the permission to delete the team.
        $this->authorize('delete', $team);

        // Delete the team from the database.
        $team->delete();

        // Redirect back with a success message.
        return back()->with('info', 'Team/s Deleted');
    }

    /**
     * Check if the current user owns the leagues given by the array of IDs.
     *
     * @param array $leagues An array of league IDs.
     * @return void
     * @throws AuthorizationException
     */
    private function league_ownership(array $leagues): void
    {
        // For each league ID, check if the current user is the owner.
        foreach ($leagues as $leagueId) {
            $league = League::find($leagueId);
            $this->authorize('owner', $league);
        }
    }

    /**
     * Check if the current user owns the team with the given ID.
     *
     * @param int $teamId The ID of the team to be checked.
     * @return void
     * @throws AuthorizationException
     */
    private function team_ownership(int $teamId): void
    {
        // Find the team with the given ID and check if the current user is the owner.
        $team = Team::find($teamId);
        $this->authorize('owner', $team);
    }

    /*private function fixed_title($title)
    {
        // Split the string into an array of words
        $words = explode(' ', $title);

        if (count($words) > 1) {
            foreach ($words as &$word) {
                // Check the length of the word
                if (strlen($word) > 6) {
                    // Shorten the word to 4 characters and add a prefix
                    $word = substr($word, 0, 3) . ".";
                }
            }
            // Combine the modified words into a new string
            return implode(' ', $words);
        } else {
            if (strlen($title) > 9) {
                // Shorten the word to 4 characters and add a prefix
                return substr($title, 0, 9) . ".";
            }
        }

        return $title;
    }
    */

}
