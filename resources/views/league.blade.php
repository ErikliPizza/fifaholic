@extends('layouts.app')

@section('content')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Import Swiper.js library -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <style>
        .nav-tabs-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none; /* hide scrollbar for Firefox */
            -ms-overflow-style: none; /* hide scrollbar for IE10+ */
        }

        .nav-tabs {
            display: flex;
            flex-wrap: nowrap;
            margin-bottom: 0;
            padding-left: 0;
            list-style: none;
        }

        .nav-tabs .nav-item {
            flex-shrink: 0;
        }

        .nav-tabs .nav-link {
            padding-right: 1rem;
            padding-left: 1rem;
            color: #6c757d;
            text-decoration: none;
            border: none;
            background-color: transparent;
        }

        .nav-tabs .nav-link.active {
            color: #343a40;
            border: none;
            border-bottom: 3px solid #007bff;
        }

        /* hide left overhang */
        .nav-tabs-container:before {
            content: "";
            display: block;
            width: 1rem;
        }

        /* hide scrollbar for Chrome and Safari */
        .nav-tabs-container::-webkit-scrollbar {
            display: none;
        }

    </style>


    <x-board-setting>
        <div class="d-flex align-items-center">
            <div class="px-1">
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm position-relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M10.354 3.646a.5.5 0 0 1 0 .708L6.707 8l3.647 3.646a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708 0z"/>
                    </svg>
                </a>
            </div>
            <div class="flex-grow-1">
                <div style="cursor: pointer;" class="text-center p-1 my-1 bg-dark text-white rounded">
                    {{ $league->title }}
                </div>
            </div>
            <div class="ms-1">
                <button class="follow-button btn btn-sm btn-outline-dark" data-league-id="{{ $league->id }}" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                </button>
            </div>
            <div class="ms-1">
                <button class="unfollow-button btn btn-sm btn-dark" data-league-id="{{ $league->id }}" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
                        <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                    </svg>
                </button>
            </div>
        </div>
        <script>
            // Follow a league
            function followLeague(leagueId) {
                $.ajax({
                    url: '/leagues/' + leagueId + '/follow',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Handle success response
                        console.log(response.message);
                        $('.follow-button[data-league-id="' + leagueId + '"]').hide();
                        $('.unfollow-button[data-league-id="' + leagueId + '"]').show();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.log(xhr.responseText);
                        // Additional error handling
                    }
                });
            }

            // Unfollow a league
            function unfollowLeague(leagueId) {
                $.ajax({
                    url: '/leagues/' + leagueId + '/unfollow',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Handle success response
                        console.log(response.message);
                        $('.follow-button[data-league-id="' + leagueId + '"]').show();
                        $('.unfollow-button[data-league-id="' + leagueId + '"]').hide();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.log(xhr.responseText);
                        // Additional error handling
                    }
                });
            }

            // Button click event
            $(document).ready(function() {
                $('.follow-button').on('click', function() {
                    var leagueId = $(this).data('league-id');
                    followLeague(leagueId);
                });

                $('.unfollow-button').on('click', function() {
                    var leagueId = $(this).data('league-id');
                    unfollowLeague(leagueId);
                });

                var isFollowing = '{{ $isFollowing }}'; // Replace with your logic to determine the status
                var leagueId = '{{ $league->id }}'; // Replace with the league ID
                if (isFollowing) {
                    $('.unfollow-button[data-league-id="' + leagueId + '"]').show();
                    $('.follow-button[data-league-id="' + leagueId + '"]').hide();
                } else {
                    $('.follow-button[data-league-id="' + leagueId + '"]').show();
                    $('.unfollow-button[data-league-id="' + leagueId + '"]').hide();
                }
            });
        </script>

        <div class="nav-tabs-container">
            <ul class="nav nav-tabs justify-content-lg-center" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="points-tab" data-bs-toggle="tab" href="#points" role="tab" aria-controls="points" aria-selected="true">points</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="away-tab" data-bs-toggle="tab" href="#away" role="tab" aria-controls="away" aria-selected="false">away</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="fixture-tab" data-bs-toggle="tab" href="#fixture" role="tab" aria-controls="fixture" aria-selected="false">fixture</a>
                </li>
                @if (auth()->check() && $league->user_id == auth()->user()->id)
                    <li class="nav-item">
                        <a class="nav-link" id="create-tab" data-bs-toggle="tab" href="#create" role="tab" aria-controls="create" aria-selected="false">create</a>
                    </li>
                @endif

            </ul>
        </div>

        <div class="tab-content py-1" id="contents">
            <div class="tab-pane fade show active" id="points" role="tabpanel" aria-labelledby="points-tab">
                <x-league-table :teams="$league->teams" :prefix="''" />
            </div>
            <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                <x-league-table :teams="$league->teams" :prefix="'h_'" />
            </div>
            <div class="tab-pane fade" id="away" role="tabpanel" aria-labelledby="away-tab">
                <x-league-table :teams="$league->teams" :prefix="'a_'" />
            </div>
            <div class="tab-pane fade text-center" id="fixture" role="tabpanel" aria-labelledby="fixture-tab">
                <x-played-table :matches="$league->matches"/>
            </div>
            <div class="tab-pane fade" id="create" role="tabpanel" aria-labelledby="create-tab">

                <form method="POST" action="/match/add">
                    @csrf
                    <input type="hidden" value="{{ $league->id }}" name="league_id">

                    <div class="row">
                        <x-league-setting>
                            <div class="row">
                                <div class="col-md-10 col-12">
                                    <select class="form-select" name="home_team_id">
                                        @foreach($league->teams as $team)
                                            <option value="{{ $team->id }}"> {{ $team->title }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col mt-md-0 mt-1 text-center">
                                    <input type="text" class="form-control text-end" id="home_team_score"
                                           name="home_team_score">
                                </div>
                            </div>
                            <hr>
                        </x-league-setting>
                        <x-league-setting>
                            <div class="row">
                                <div class="col-md col order-md-0 order-2 mt-md-0 mt-1 text-center">
                                    <input type="text" id="away_team_score" name="away_team_score" class="form-control">
                                </div>

                                <div class="col-md-10 col-12 order-md-0 order-1">
                                    <select class="form-select" name="away_team_id">
                                        @foreach($league->teams as $team)
                                            <option value="{{ $team->id }}"> {{ $team->title }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                        </x-league-setting>
                    </div>

                    <div class="d-flex justify-content-center">
                        <form class="form-inline">
                            <div class="form-group">
                                <input type="number" id="week" name="week" placeholder="Week" class="form-control text-center">
                            </div>
                            <button type="submit" class="btn btn-warning btn-sm mx-1">Create</button>
                        </form>
                    </div>

                    <div class="text-center">
                        @if ($lastMatch = $league->matches->last())
                            <div class="py-2 text-secondary fst-italic text-decoration-underline">
                                Your last match played between <span class="text-dark">{{ $lastMatch->homeTeam->title }}</span> and <span class="text-dark">{{ $lastMatch->awayTeam->title }}</span> at week <span class="text-danger fw-bolder text-opacity-75">{{ $lastMatch->week }}</span>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </x-board-setting>
@endsection
