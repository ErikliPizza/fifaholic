@extends('layouts.app')

@section('content')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <x-board-setting>
        @if($match)
            <h3 class="px-1">Last Match</h3>
            <div id="myLastMatch">
                <div id="myLastMatch">
                    <div class="table-responsive text-center">
                        <table class="table table-striped border">
                            <col style="width: 45%;">
                            <col style="width: 10%;">
                            <col style="width: 45%;">
                            <tbody>
                            <tr>
                                <td style="cursor: pointer;" class="fw-bolder text-center" onclick="window.location='/teams/{{ $match->homeTeam->id }}'">
                                    {{ $match->homeTeam->title }}
                                </td>
                                <td class="text-nowrap text-center" style="cursor: pointer;" onclick="window.location='/match/{{ $match->id }}'">{{ $match->home_team_score }} - {{ $match->away_team_score }}</td>
                                <td style="cursor: pointer;" class="text-center fw-bolder" onclick="window.location='/teams/{{ $match->awayTeam->id }}'">
                                    {{ $match->awayTeam->title }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

            @if($user->leagues->isNotEmpty())
                <h3 class="px-1 mt-3">My Leagues</h3>
                <div id="myLeagues">
                    <ul class="list-group">
                        @foreach($user->leagues as $league)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="col-6" style="cursor: pointer;" onclick="window.location='/league/{{ $league->id }}'">
                                    {{ $league->title }}
                                </div>
                                <div class="col-6 text-end">
                                    <button onclick="shareViaWhatsApp('{{ $league->id }}')" class="btn btn-sm btn-outline-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                            <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                        </svg>
                                    </button>

                                    <button onclick="copyToClipboard('{{ $league->id }}')" class="btn btn-sm btn-outline-dark" data-shared-id="{{ $league->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
                                            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <script>
                function shareViaWhatsApp(leagueId) {
                    // Replace the placeholders with your desired content
                    var message = "See my League!";
                    var url = window.location.origin + '/league/' + leagueId;

                    // Check if WhatsApp is installed on the device
                    if (typeof window.navigator.share !== "undefined" && window.navigator.share.share) {
                        // Use the Web Share API if available
                        window.navigator.share({
                            title: document.title,
                            text: message,
                            url: url
                        })
                            .catch(function(error) {
                                console.error("Error sharing via WhatsApp:", error);
                            });
                    } else {
                        // Fallback: Open WhatsApp with pre-filled message and URL
                        var whatsappUrl = "https://api.whatsapp.com/send?text=" + encodeURIComponent(message + " " + url);
                        window.open(whatsappUrl);
                    }
                }

                function copyToClipboard(leagueId) {
                    var url = window.location.origin + '/league/' + leagueId;

                    // Create a temporary input element
                    var input = document.createElement("input");
                    input.value = url;
                    document.body.appendChild(input);

                    // Select the content of the input element
                    input.select();
                    input.setSelectionRange(0, input.value.length);

                    try {
                        // Copy the selected text to the clipboard
                        var successful = document.execCommand("copy");
                        var message = successful ? "Copied to clipboard!" : "Copy failed.";
                        console.log(message);
                    } catch (error) {
                        console.error("Error copying to clipboard:", error);
                    }

                    // Remove the temporary input element
                    document.body.removeChild(input);
                }
            </script>
            @if($user->followedLeagues->isNotEmpty())
                <h3 class="px-1 mt-3">Followed Leagues</h3>
                <div id="myLeagues">
                    <ul class="list-group">
                        @foreach($user->followedLeagues as $league)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="col-9" style="cursor: pointer;" onclick="window.location='/league/{{ $league->id }}'">
                                    {{ $league->title }}
                                </div>
                                <div class="col-3 text-end">
                                    <button class="follow-button btn btn-sm btn-outline-dark" data-league-id="{{ $league->id }}" style="display: none;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                        </svg>
                                    </button>
                                    <button class="unfollow-button btn btn-sm btn-secondary" data-league-id="{{ $league->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
                                            <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

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

                    // Iterate over each followed league
                    @foreach($user->followedLeagues as $league)
                    var leagueId = {{ $league->id }};
                    $('.follow-button[data-league-id="' + leagueId + '"]').hide();
                    $('.unfollow-button[data-league-id="' + leagueId + '"]').show();
                    @endforeach
                });
            </script>
    </x-board-setting>
@endsection
