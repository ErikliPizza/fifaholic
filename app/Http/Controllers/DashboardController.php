<?php

namespace App\Http\Controllers;

use App\Rules\alpha_spaces;
use App\Rules\league_each_user;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use App\Models\League;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * Only authenticated users can access methods in this controller
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     */
    public function index(): Renderable
    {
        // Get the authenticated user's leagues and pass them to the dashboard view
        return view('dashboard', [
            'leagues' => Auth::user()->leagues
        ]);
    }

    /**
     * Store a new league in the database.
     *
     * Validate the incoming request data and create a new league
     *
     */
    public function store(): RedirectResponse
    {
        $attributes = request()->validate([
            'title' => ['required', 'string', new alpha_spaces(), 'min:3', 'max:26', new league_each_user()],
        ]);
        $attributes['user_id'] = auth()->id();
        $data = League::create($attributes);
        // Redirect back to the dashboard with a success message
        return back()->with('success', 'Post Created With Id: ' . $data->id);
    }

    /**
     * Delete a league from the database.
     *
     * Check if the authenticated user is authorized to delete the league
     *
     * @param  League  $league
     */
    public function destroy(League $league): RedirectResponse
    {
        $this->authorize('delete', $league);
        $league->delete();
        // Redirect back to the dashboard with an info message
        return back()->with('info', 'Post Deleted With Id: ' . $league->id);
    }

    /**
     * Update a league in the database.
     *
     * Check if the authenticated user is authorized to update the league,
     * validate the incoming request data, and update the league
     *
     * @param  League  $league
     */
    public function update(League $league): RedirectResponse
    {
        $this->authorize('update', $league);
        $attributes = request()->validate([
            'title' => ['required', 'string', new alpha_spaces(), 'min:3', 'max:26', new league_each_user()],
        ]);
        $league->update($attributes);
        // Redirect back to the dashboard with a success message
        return back()->with('success', 'Post Updated With Id: ' . $league->id);
    }
}
