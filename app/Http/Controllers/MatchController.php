<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Matches;
use App\Models\Team;
use App\Models\MatchTeam;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    /**
     * Create a new instance and apply the 'auth' middleware.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display the specified match.
     *
     * @param  Matches $match
     * @return View
     */
    public function show(Matches $match): View
    {
        // Load the relationships
        $match->load('homeTeam', 'awayTeam');

        $team1_id = $match->homeTeam->id;
        $team2_id = $match->awayTeam->id;

        // Find the matches played between the two teams
        $playedBetween = Matches::where(function ($query) use ($team1_id, $team2_id) {
            $query->where('home_team_id', $team1_id)
                ->where('away_team_id', $team2_id);
        })
            ->orWhere(function ($query) use ($team1_id, $team2_id) {
                $query->where('home_team_id', $team2_id)
                    ->where('away_team_id', $team1_id);
            })
            ->get();

        return view('match', [
            'match' => $match,
            'playedBetween' => $playedBetween
        ]);
    }


    /**
     * Store a newly created match.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request data
        $validatedData = request()->validate([
            'home_team_id' => 'required|integer',
            'away_team_id' => 'required|integer|different:home_team_id',
            'home_team_score' => 'required|integer|min:0',
            'away_team_score' => 'required|integer|min:0',
            'week' => 'required|integer|min:0',
            'league_id' => 'required|integer'
        ], [
            'away_team_id.different' => 'The home team and away team must be different.'
        ]);

        $teamIds = [$validatedData['home_team_id'], $validatedData['away_team_id']];
        $teams = Team::whereIn('id', $teamIds)->get();
        $this->authorize('owner', $teams[0]);
        $this->authorize('owner', $teams[1]);
        $league = League::find($validatedData['league_id']);
        $this->authorize('owner', $league);

        // Create a new match record
        $match = Matches::create([
            'league_id' => $validatedData['league_id'],
            'home_team_id' => $validatedData['home_team_id'],
            'away_team_id' => $validatedData['away_team_id'],
            'week' => $validatedData['week']
        ]);

        // Create two new match_team records
        MatchTeam::create([
            'match_id' => $match->id,
            'team_id' => $validatedData['home_team_id'],
            'home_or_away' => 'home'
        ]);

        MatchTeam::create([
            'match_id' => $match->id,
            'team_id' => $validatedData['away_team_id'],
            'home_or_away' => 'away'
        ]);

        // Update the match score fields
        $match->home_team_score = $request->input('home_team_score');
        $match->away_team_score = $request->input('away_team_score');
        $match->save();

        return redirect()->back()->with('success', 'Match created successfully.');
    }


    /**
     * Delete a match.
     *
     * @param Matches $match
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Matches $match): RedirectResponse
    {
        // Check authorization
        $this->authorize('owner', League::find($match->league_id));

        // Delete the match
        $match->delete();

        return redirect('/teams')->with('success', 'Match deleted successfully!');
    }

    public function updateWeek(Request $request)
    {
        // Get the selected week and match ID from the request
        $week = $request->input('week');
        $matchId = $request->input('match_id');
        $match = Matches::find($matchId);
        $this->authorize('owner', League::find($match->league_id));
        $match->update([
            'week' => $week
        ]);

        // Return a response (if needed)
        return response()->json(['success' => true]);
    }

    public function swapTeam(Matches $match)
    {
        $matchId = $match->id;
        $this->authorize('owner', League::find($match->league_id));
        // Store original values
        $originalHomeTeamId = $match->home_team_id;
        $originalAwayTeamId = $match->away_team_id;
        $originalHomeTeamScore = $match->home_team_score;
        $originalAwayTeamScore = $match->away_team_score;

        // Swap home and away team IDs and scores
        $match->home_team_id = $originalAwayTeamId;
        $match->away_team_id = $originalHomeTeamId;
        $match->home_team_score = $originalAwayTeamScore;
        $match->away_team_score = $originalHomeTeamScore;
        $match->save();
        // Swap home and away teams in the match_team table
        MatchTeam::where('match_id', $matchId)
            ->whereIn('home_or_away', ['home', 'away'])
            ->update([
                'home_or_away' => MatchTeam::raw('CASE WHEN home_or_away = "home" THEN "away" ELSE "home" END')
            ]);
        return redirect()->back()->with('success', 'Teams swapped successfully.');
    }
}
