@props(['matches', 'team_id'])
<div class="table-responsive text-center">
    <table class="table table-striped border">
        <col style="width: 45%;">
        <col style="width: 10%;">
        <col style="width: 45%;">
        <tbody>
        @foreach($matches->sortByDesc('week') as $match)
            <tr>
                <td style="cursor: pointer;" class="{{ $match->homeTeam->id == $team_id ?
            ($match->home_team_score > $match->away_team_score ? 'text-success' :
             ($match->home_team_score < $match->away_team_score ? 'text-danger' : 'text-warning')) : '' }} fw-bolder text-center" onclick="window.location='/teams/{{ $match->homeTeam->id }}'">
                    {{ $match->homeTeam->title }}
                </td>
                <td class="text-nowrap text-center" style="cursor: pointer;" onclick="window.location='/match/{{ $match->id }}'">{{ $match->home_team_score }} - {{ $match->away_team_score }}</td>
                <td style="cursor: pointer;" class="text-center {{ $match->awayTeam->id == $team_id ?
            ($match->away_team_score > $match->home_team_score ? 'text-success' :
             ($match->away_team_score < $match->home_team_score ? 'text-danger' : 'text-warning')) : '' }} fw-bolder" onclick="window.location='/teams/{{ $match->awayTeam->id }}'">
                    {{ $match->awayTeam->title }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
