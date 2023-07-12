@extends('layouts.app')

@section('content')
    <style>
        /* Styles for unchecked checkbox */
        .delete-checkbox:not(:checked) {
            background-color: #198754;
            border-color: #198754;

        }
        /* Styles for checked checkbox */
        .delete-checkbox:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .form-control:focus {
            box-shadow: none;
        }
        .form-check-input:focus {
            box-shadow: none;
        }
        @media (max-width: 576px) {
            .form-check-label {
                max-width: 75px; /* or any other value that suits your needs */
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }
    </style>
    <x-board-setting>
        <form method="POST" action="/teams/add">
            @csrf
            <div class="row mb-3">
                <label for="title" class="col-md-2 col-3 col-form-label">Title*</label>
                <div class="col-md-8 col-7">
                    <input type="text" class="form-control" id="title" placeholder="MFL Sport" name="title">
                </div>
                <button type="submit" class="btn btn-sm btn-outline-dark col-md-2 col-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-send-plus-fill" viewBox="0 0 16 16">
                        <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47L15.964.686Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
                        <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm-3.5-2a.5.5 0 0 0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-.5-.5Z"/>
                    </svg>
                </button>
            </div>
            <hr style="width: 10%;">
            @if($leagues)


                <label class="form-label">League*</label>
                <div class="d-flex justify-content-center py-1">
                    <div class="row">
                        @foreach($leagues as $league)
                            <div class="col text-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="league_check{{ $league->id }}" name="league_id[]" value="{{ $league->id }}"{{ $loop->first ? ' checked' : '' }}>
                                    <label class="form-check-label text-truncate" for="league_check {{ $league->id }}">
                                        {{ $league->title }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <span>No League to Append</span>
            @endif
        </form>


        <!-- exist start -->
        @if($teams->count())
            <!-- records start -->
            <div class="container text-center mt-2">
                <form method="POST" action="/teams/update">
                    @csrf
                    @method('PATCH')

                    <hr style="color:lightgray;">
                    <div class="mt-5 text-start d-flex justify-content-around">
                        <div>
                            <button class="btn btn-outline-dark disabled">
                                MY Teams
                            </button>
                            <hr style="width:100%; color:chocolate;">
                        </div>
                        <div>
                            <button class="btn btn-outline-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                    <path d="M5.648 8.14 4.267 6.76A.5.5 0 0 1 4.76 6.27l1.966 1.966 4.837-4.837a.5.5 0 0 1 .706 0l.708.708a.5.5 0 0 1 0 .707l-5.25 5.25a.5.5 0 0 1-.707 0z"/>
                                </svg>
                            </button>
                            <hr style="width:100%; color:brown;">
                        </div>
                    </div>

                    @foreach($teams->sortBy('title') as $index => $team)
                        <input type="hidden" value="{{ $team->id }}" name="teams[{{ $index }}][id]">

                        <div class="row mb-3">

                            <div class="input-group">
                                <a class="input-group-text bg-primary text-decoration-none fw-bolder" href="/teams/{{ $team->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill text-warning" viewBox="0 0 16 16">
                                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                    </svg>
                                </a>

                                <input type="text" class="form-control" value="{{ $team->title }}" name="teams[{{ $index }}][title]">
                                <div class="input-group-text">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input delete-checkbox" type="checkbox" id="{{ $team->id }}-{{ $league->id }}-delete" name="teams[{{ $index }}][delete]" value="true">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center py-1">
                                <div class="row">
                                    @foreach($leagues as $league)
                                        <div class="col text-center">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="{{ $team->id }}-{{ $league->id }}" name="teams[{{ $index }}][league_id][]" value="{{ $league->id }}" {{ in_array($league->id, $team->leagues->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                <label class="form-check-label text-truncate" for="{{ $team->id }}-{{ $league->id }}">
                                                    {{ $league->title }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr>

                        </div>
                    @endforeach
                </form>

            </div>
            <!-- records end -->
        @endif
        <!-- exist end -->
        </form>
    </x-board-setting>
@endsection
