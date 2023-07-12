<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\Auth\LoginController;
use App\Models\User;
use App\Models\Matches;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    $user = Auth::user(); // Retrieve the authenticated user
    $latestMatch = Matches::whereIn('league_id', function ($query) use ($user) {
        $query->select('id')
            ->from('leagues')
            ->where('user_id', $user->id);
    })
        ->latest('created_at')
        ->first();

    return view('home', [
        'user' => $user,
        'match' => $latestMatch
    ]);
})->middleware('auth');

Auth::routes();

//Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/dashboard/add', [DashboardController::class, 'store'])->middleware('checkUserLeagueNumber');
Route::delete('/dashboard/{league}/delete', [DashboardController::class, 'destroy']);
Route::patch('/dashboard/{league}/update', [DashboardController::class, 'update']);

//Teams
Route::get('/teams', [TeamController::class, 'index'])->name('teams');
Route::get('/teams/{team}', [TeamController::class, 'show']);
Route::post('/teams/add', [TeamController::class, 'store'])->middleware('checkUserTeamNumber');
Route::patch('/teams/update', [TeamController::class, 'update']);


//League
Route::get('/league/{league}', [LeagueController::class, 'index']);
//Follow
Route::middleware('auth')->group(function () {
    Route::post('/leagues/{league}/follow', [LeagueController::class, 'follow'])->name('leagues.follow');
    Route::post('/leagues/{league}/unfollow', [LeagueController::class, 'unfollow'])->name('leagues.unfollow');
});


//Matches
Route::post('/match/add', [MatchController::class, 'store']);
Route::get('/match/{match}', [MatchController::class, 'show']);
Route::delete('/match/{match}', [MatchController::class, 'destroy'])->name('match.destroy');
Route::post('/match/update-week', [MatchController::class, 'updateWeek'])->name('update-week');
Route::post('/match/swap-team/{match}', [MatchController::class, 'swapTeam'])->name('swap.team');

//Comments
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

//Tokens
Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
Route::post('/tokens', [TokenController::class, 'store'])->name('tokens.store');

//Socialite
Route::get('auth/google', [LoginController::class, 'redirectToGoogle']);
Route::get('login/google/callback', [LoginController::class, 'handleGoogleCallback']);
