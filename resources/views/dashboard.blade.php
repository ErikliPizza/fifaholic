@extends('layouts.app')

@section('content')
    <x-board-setting>
        <form method="POST" action="/dashboard/add">
            @csrf
            <div class="row">
                <label for="title" class="col-md-2 col-5 col-form-label">League Title</label>
                <div class="col-md-8 col-5">
                    <input type="text" class="form-control" id="title" placeholder="MFL League" name="title">
                </div>
                <button type="submit" class="btn btn-sm btn-outline-dark col-md-2 col-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-send-plus-fill" viewBox="0 0 16 16">
                        <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47L15.964.686Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
                        <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm-3.5-2a.5.5 0 0 0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-.5-.5Z"/>
                    </svg>
                </button>
            </div>
        </form>

        <!-- exist start -->
        @if($leagues->count())
            <hr style="color:lightgray;">
            <div class="mt-5">
                <span class="fst-italic fw-bold">My Leagues</span>
                <hr style="width:13%; color:chocolate;">
            </div>

            <!-- records start -->
            <div class="container text-center mt-2">
                <tbody class="table-group-divider">
                @foreach($leagues as $league)
                    <div class="row p-1">

                        <div class="col-8 col-md-8">
                            <form method="POST" action="/dashboard/{{ $league->id }}/update">
                                @csrf
                                @method('PATCH')
                                <div class="form-group row">
                                    <div class="col-md-10 col-9">
                                        <input type="text" class="form-control" value="{{ $league->title }}" name="title">
                                    </div>
                                    <button class="btn btn-outline-dark col-md-2 col-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                                            <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z"/>
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-2 col-md-2">
                            <form method="POST" action="/dashboard/{{ $league->id }}/delete">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-outline-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <div class="col-2 col-md-2">
                            <a class="btn btn-outline-success ms-1" href="/league/{{ $league->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart-fill" viewBox="0 0 16 16">
                                    <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V2z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <hr>
                @endforeach
            </div>
            <!-- records end -->
        @endif
        <!-- exist end -->

    </x-board-setting>
@endsection
