<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;

class TokenController extends Controller
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
     * Display the access tokens for the authenticated user.
     *
     * @return View
     */
    public function index(): View
    {
        $user = auth()->user(); // Get the authenticated user
        $tokens = $user->tokens; // Retrieve the tokens associated with the user

        // Return the 'tokens.index' view with the tokens data
        return view('tokens.index', [
            'tokens' => $tokens,
        ]);
    }

    /**
     * Store a new access token for the authenticated user.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $user = auth()->user(); // Get the authenticated user

        $tokenName = explode('@', $user->email)[0]; // Extract the token name from the user's email

        $user->tokens()->delete(); // Delete existing tokens associated with the user

        $accessToken = $user->createToken($tokenName)->accessToken; // Create a new access token for the user

        // Redirect to the 'tokens.index' route with success message and access token
        return redirect()->route('tokens.index')
            ->with('success', 'Access token created successfully.')
            ->with('access_token', $accessToken);
    }

}
