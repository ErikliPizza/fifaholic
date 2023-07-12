@extends('layouts.app')

@section('content')
    <!-- Import Swiper.js library -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
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
        <div class="px-1 mb-2">
            <a href="#" onclick="history.go(-1); return false;" class="btn btn-primary btn-sm position-relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10.354 3.646a.5.5 0 0 1 0 .708L6.707 8l3.647 3.646a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708 0z"/>
                </svg>
            </a>
        </div>
        <div class="text-center">
            <div style="display: inline-flex; align-items: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stopwatch" viewBox="0 0 16 16" style="margin-bottom: 2px;">
                    <path d="M8.5 5.6a.5.5 0 1 0-1 0v2.9h-3a.5.5 0 0 0 0 1H8a.5.5 0 0 0 .5-.5V5.6z"/>
                    <path d="M6.5 1A.5.5 0 0 1 7 .5h2a.5.5 0 0 1 0 1v.57c1.36.196 2.594.78 3.584 1.64a.715.715 0 0 1 .012-.013l.354-.354-.354-.353a.5.5 0 0 1 .707-.708l1.414 1.415a.5.5 0 1 1-.707.707l-.353-.354-.354.354a.512.512 0 0 1-.013.012A7 7 0 1 1 7 2.071V1.5a.5.5 0 0 1-.5-.5zM8 3a6 6 0 1 0 .001 12A6 6 0 0 0 8 3z"/>
                </svg>
                <span style="margin-left: 5px;" class="text-secondary">{{ $match->created_at->format('d M y, H:i') }}</span>
            </div>
        </div>

        <div class="nav-tabs-container">
            <ul class="nav nav-tabs justify-content-lg-center" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" href="/league/{{ $match->league_id }}">league</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" id="stats-tab" data-bs-toggle="tab" href="#stats" role="tab" aria-controls="stats" aria-selected="false">stats</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="played-tab" data-bs-toggle="tab" href="#played" role="tab" aria-controls="played" aria-selected="true">between</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="form-tab" data-bs-toggle="tab" href="#form" role="tab" aria-controls="form" aria-selected="false">form</a>
                </li>
                @if (auth()->check() && $match->league->user_id == auth()->user()->id)
                    <li>
                        <form action="{{ route('swap.team', $match) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="submit" class="nav-link text-info" value="swap" />
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('match.destroy', $match) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="nav-link text-danger" data-bs-toggle="modal" data-bs-target="#deleteMatchModal">delete</button>
                            <!-- Modal -->
                            <x-delete-confirmation-modal :modal_id="'deleteMatchModal'"/>
                        </form>
                    </li>
                @endif
            </ul>
        </div>

        <div class="tab-content py-3" id="contents">
            <div class="row">
                <div class="col-6 text-center">
                    <h5 class="fw-bolder" style="cursor: pointer;" onclick="window.location='/teams/{{ $match->homeTeam->id }}'">
                        <span class="bg-primary">&nbsp;</span> {{ $match->homeTeam->title }}
                    </h5>
                    <p class="fw-bolder text-secondary">{{ $match->home_team_score }}</p>
                </div>

                <div class="col-6 text-center">
                    <h5 class="fw-bolder" style="cursor: pointer;" onclick="window.location='/teams/{{ $match->awayTeam->id }}'">
                        {{ $match->awayTeam->title }} <span class="bg-danger">&nbsp;</span>
                    </h5>
                    <p class="fw-bolder text-secondary">{{ $match->away_team_score }}</p>
                </div>
            </div>

            <div class="tab-pane fade show active" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                @if ($match->league->user_id == auth()->user()->id)
                    <div class="d-flex justify-content-center">
                        <form class="d-flex">
                            <div class="input-group">
                                <span class="input-group-text">W</span>
                                <select class="form-select form-select-sm" id="week-select" name="week">
                                    @for ($i = 0; $i <= 200; $i++)
                                        <option value="{{ $i }}" {{ $i == $match->week ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </form>
                    </div>
                @endif
                <script>
                    $(document).ready(function() {
                        // Set the CSRF token for all AJAX requests
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        // Handle the change event of the select box
                        $('#week-select').change(function() {
                            // Get the selected week value and match ID
                            var week = $(this).val();
                            var matchId = {{ $match->id }}; // Replace with your actual match ID

                            // Send AJAX request to update the week
                            $.ajax({
                                url: '{{ route("update-week") }}',
                                type: 'POST',
                                data: {
                                    week: week,
                                    match_id: matchId
                                },
                                dataType: 'json',
                                success: function(response) {
                                    // Handle the response if needed
                                    console.log(response);
                                },
                                error: function(xhr, status, error) {
                                    // Handle any errors
                                    console.log(xhr.responseText);
                                }
                            });
                        });
                    });
                </script>
                @php
                    $statistics = [
                        ['kind' => 'possession', 'home' => $match->homeTeamStat ? $match->homeTeamStat->possession : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->possession : null],
                        ['kind' => 'shots', 'home' => $match->homeTeamStat ? $match->homeTeamStat->shots : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->shots : null],
                        ['kind' => 'expected goals', 'home' => $match->homeTeamStat ? $match->homeTeamStat->expected_goals : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->expected_goals : null],
                        ['kind' => 'passes', 'home' => $match->homeTeamStat ? $match->homeTeamStat->passes : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->passes : null],
                        ['kind' => 'tackles', 'home' => $match->homeTeamStat ? $match->homeTeamStat->tackles : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->tackles : null],
                        ['kind' => 'tackles won', 'home' => $match->homeTeamStat ? $match->homeTeamStat->tackles_won : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->tackles_won : null],
                        ['kind' => 'interceptions', 'home' => $match->homeTeamStat ? $match->homeTeamStat->interceptions : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->interceptions : null],
                        ['kind' => 'saves', 'home' => $match->homeTeamStat ? $match->homeTeamStat->saves : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->saves : null],
                        ['kind' => 'fouls committed', 'home' => $match->homeTeamStat ? $match->homeTeamStat->fouls_committed : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->fouls_committed : null],
                        ['kind' => 'offsides', 'home' => $match->homeTeamStat ? $match->homeTeamStat->offsides : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->offsides : null],
                        ['kind' => 'corners', 'home' => $match->homeTeamStat ? $match->homeTeamStat->corners : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->corners : null],
                        ['kind' => 'free kicks', 'home' => $match->homeTeamStat ? $match->homeTeamStat->free_kicks : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->free_kicks : null],
                        ['kind' => 'penalty kicks', 'home' => $match->homeTeamStat ? $match->homeTeamStat->penalty_kicks : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->penalty_kicks : null],
                        ['kind' => 'yellow cards', 'home' => $match->homeTeamStat ? $match->homeTeamStat->yellow_cards : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->yellow_cards : null],
                        ['kind' => 'red cards', 'home' => $match->homeTeamStat ? $match->homeTeamStat->red_cards : null, 'away' => $match->awayTeamStat ? $match->awayTeamStat->red_cards : null],
                    ];
                @endphp

                @foreach ($statistics as $stat)
                    @if ($stat['home'] !== null && $stat['away'] !== null)
                        <x-match-statistic-bar :kind="$stat['kind']" :home="$stat['home']" :away="$stat['away']"/>
                    @endif
                @endforeach
            </div>
            <div class="tab-pane fade" id="played" role="tabpanel" aria-labelledby="played-tab">
                <x-team-played-table :matches="$playedBetween" :team_id="'none'"/>
            </div>
            <div class="tab-pane fade" id="form" role="tabpanel" aria-labelledby="form-tab">
                @if (auth()->check())
                    <form action="{{ route('comments.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="match_id" value="{{ $match->id }}">

                        <div class="form-group">
                            <textarea id="comment" name="comment" class="form-control" rows="3"></textarea>
                            <div class="text-center mt-2">
                                <button type="submit" class="btn btn-sm btn-dark">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-plus-fill" viewBox="0 0 16 16">
                                        <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47L15.964.686Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
                                        <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm-3.5-2a.5.5 0 0 0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-.5-.5Z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </form>
                @else
                    <p class="text-muted">Please <a href="{{ route('login') }}">login</a> to add comments.</p>
                @endif

                <hr>

                <ul class="list-unstyled">
                    @foreach ($match->comments as $comment)
                        <li class="media mb-4">

                            <div class="media-body">
                                <h5 class="mt-0 mb-1 fw-bolder">{{ $comment->user->name }}</h5>

                                <p class="fst-italic text-black text-opacity-75">
                                    <span class="comment-text" id="comment-text-{{ $comment->id }}">{{ $comment->comment }}</span>
                                    <textarea class="form-control edit-comment-text" id="edit-comment-text-{{ $comment->id }}" style="display: none;"></textarea>
                                    <button type="button" class="btn btn-primary save-comment-btn my-2" id="save-comment-btn-{{ $comment->id }}" style="display: none;">Save</button>
                                </p>


                                <div class="d-flex justify-content-between">
                                    <div>
                                        @if (auth()->check() && $comment->user_id === auth()->user()->id)
                                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <span type="button" class="text-danger mt-1" data-bs-toggle="modal" data-bs-target="#deleteCommentModal">Delete</span>

                                                <!-- Modal -->
                                                <x-delete-confirmation-modal :modal_id="'deleteCommentModal'"/>
                                            </form>
                                            <span type="button" class="text-success ms-2 edit-comment" data-comment-id="{{ $comment->id }}">Edit</span>

                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $comment->updated_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>



        </div>
    </x-board-setting>


    <script>
        $(document).ready(function() {
            $('.edit-comment').click(function() {
                var commentId = $(this).data('comment-id');
                var commentText = $('#comment-text-' + commentId).text();
                $('#comment-text-' + commentId).toggle();
                $('#edit-comment-text-' + commentId).val(commentText).toggle(); // Toggle the edit area
                $('#save-comment-btn-' + commentId).toggle();
            });
        });


        $(document).ready(function() {
            $('.save-comment-btn').click(function() {
                var commentId = $(this).attr('id').split('-')[3];
                var newCommentText = $('#edit-comment-text-' + commentId).val();

                $.ajax({
                    type: 'PUT',
                    url: '/comments/' + commentId,
                    data: {
                        comment: newCommentText
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#comment-text-' + commentId).html(newCommentText);
                        $('#comment-text-' + commentId).show();
                        $('#edit-comment-text-' + commentId).hide();
                        $('#save-comment-btn-' + commentId).hide();
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });
        });
    </script>
@endsection

