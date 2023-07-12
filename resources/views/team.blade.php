@extends('layouts.app')

@section('content')
    <x-board-setting>
        <div class="px-1" style="position: absolute; top: 12px; left: 10px;">
            <a href="#" onclick="history.go(-1); return false;" class="btn btn-primary btn-sm position-relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10.354 3.646a.5.5 0 0 1 0 .708L6.707 8l3.647 3.646a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708 0z"/>
                </svg>
            </a>
        </div>
        <div class="text-center fw-bolder text-primary">
            {{ $team->title }}
        </div>
        <div class="text-center d-flex justify-content-center mb-1">
            @foreach ($team->matches->sortByDesc('created_at')->take(5)->reverse() as $match)
                @php
                    $result = 'D'; // default value is draw
                    if ($match->homeTeam->id == $team->id) {
                        if ($match->home_team_score > $match->away_team_score) {
                            $result = 'W';
                        } elseif ($match->home_team_score < $match->away_team_score) {
                            $result = 'L';
                        }
                    } elseif ($match->awayTeam->id == $team->id) {
                        if ($match->away_team_score > $match->home_team_score) {
                            $result = 'W';
                        } elseif ($match->away_team_score < $match->home_team_score) {
                            $result = 'L';
                        }
                    }
                @endphp
                <div class="d-flex justify-content-center align-items-center bg-{{ $result == 'W' ? 'success' : ($result == 'L' ? 'danger' : 'warning') }} text-white ms-1 rounded-1" style="width: 20px; height: 20px;">
                    <span>{{ $result }}</span>
                </div>
            @endforeach
        </div>


        <ul class="nav nav-tabs d-flex justify-content-center" id="top-menu-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="played-tab" data-bs-toggle="tab" href="#played" role="tab" aria-controls="played" aria-selected="true">played</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="leagues-tab" data-bs-toggle="tab" href="#leagues" role="tab" aria-controls="leagues" aria-selected="false">leagues</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="statistics-tab" data-bs-toggle="tab" href="#statistics" role="tab" aria-controls="statistics" aria-selected="false">statistics</a>
            </li>
        </ul>

        <div class="tab-content py-1" id="contents">
            <div class="tab-pane fade show active" id="played" role="tabpanel" aria-labelledby="played-tab">
                @foreach($team->leagues as $league)
                    <div class="py-1">
                        <x-league-title :title="$league->title" :id="$league->id"/>

                        <x-team-played-table :matches="$team->matches->where('league_id', $league->id)" :team_id="$team->id"/>
                    </div>
                @endforeach
                    <div class="accordion" id="played-matches">
                        <div style="cursor: pointer;" class="text-center p-1 my-1 bg-dark text-white rounded" data-bs-toggle="collapse" data-bs-target="#played-matches-collapse" aria-expanded="false" aria-controls="played-matches-collapse">
                            All Played
                        </div>
                        <div class="collapse" id="played-matches-collapse" aria-labelledby="played-matches" data-bs-parent="#played-matches">
                            <x-team-played-table :matches="$team->matches" :team_id="$team->id"/>
                        </div>
                    </div>
            </div>

            <div class="tab-pane fade" id="leagues" role="tabpanel" aria-labelledby="leagues-tab">
                <div class="py-1 text-secondary fw-bolder text-center">Related Leagues</div>
                @foreach($team->leagues as $league)
                    <div class="py-1">
                        <ul class="nav nav-tabs d-flex justify-content-center" id="league-{{ $league->id }}-menu" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active text-black fw-bolder fst-italic" id="all-tab-{{ $league->id }}" data-bs-toggle="tab" href="#all-{{ $league->id }}" role="tab" aria-controls="all-{{ $league->id }}" aria-selected="true">All</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-black fw-bolder fst-italic" id="home-tab-{{ $league->id }}" data-bs-toggle="tab" href="#home-{{ $league->id }}" role="tab" aria-controls="home-{{ $league->id }}" aria-selected="false">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-black fw-bolder fst-italic" id="away-tab-{{ $league->id }}" data-bs-toggle="tab" href="#away-{{ $league->id }}" role="tab" aria-controls="away-{{ $league->id }}" aria-selected="false">Away</a>
                            </li>
                        </ul>

                        <x-league-title :title="$league->title" :id="$league->id"/>

                        <div class="tab-content py-1" id="contents">
                            <div class="tab-pane fade show active" id="all-{{ $league->id }}" role="tabpanel" aria-labelledby="all-tab-{{ $league->id }}">
                                <x-league-table :teams="$league->teams" :prefix="''" />
                            </div>
                            <div class="tab-pane fade" id="home-{{ $league->id }}" role="tabpanel" aria-labelledby="home-tab-{{ $league->id }}">
                                <x-league-table :teams="$league->teams" :prefix="'h_'" />
                            </div>
                            <div class="tab-pane fade" id="away-{{ $league->id }}" role="tabpanel" aria-labelledby="away-tab-{{ $league->id }}">
                                <x-league-table :teams="$league->teams" :prefix="'a_'" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <col style="width: 50%;">
                        <col style="width: 50%;">
                        <thead class="thead-dark">
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($statistics as $key => $statistic)
                            <tr>
                                <td class="text-end fw-bolder text-secondary"> {{ ucwords(str_replace('_', ' ', $key)) }} </td>
                                <td class="fw-bolder"> {{ $statistic }} </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </x-board-setting>
@endsection
