<?php

namespace App\Http\Controllers;

use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeagueController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the league view.
     *
     * @param  \App\Models\League  $league
     * @return \Illuminate\Contracts\View\View
     */
    public function index(League $league)
    {

        // Get statistics for the league
        $league->getStats();
        // Check if the authenticated user is following the league
        $isFollowing = $league->followers()->where('user_id', Auth::id())->exists();

        return view('league', [
            'league' => $league,
            'isFollowing' => $isFollowing
        ]);
    }

    public function follow(Request $request, League $league)
    {
        $user = Auth::user();

        if ($user->followedLeagues()->where('league_id', $league->id)->exists()) {
            // User is already following the league, do nothing or return a response indicating it
            return response()->json(['message' => 'You are already following this league.']);
        }

        $user->followedLeagues()->attach($league);

        return response()->json(['message' => 'You are now following the league.']);
    }

    public function unfollow(Request $request, League $league)
    {
        $user = Auth::user();

        if (!$user->followedLeagues()->where('league_id', $league->id)->exists()) {
            // User is not following the league, do nothing or return a response indicating it
            return response()->json(['message' => 'You are not following this league.']);
        }

        $user->followedLeagues()->detach($league);

        return response()->json(['message' => 'You have unfollowed the league.']);
    }

}
