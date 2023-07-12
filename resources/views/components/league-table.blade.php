@props(['teams', 'prefix'])
<div class="table-responsive">
<table class="table table-striped">
    <col style="width: 5%;">
    <col style="width: 55%;">
    <col style="width: 5%;">
    <col style="width: 5%;">
    <col style="width: 5%;">
    <col style="width: 5%;">
    <col style="width: 5%;">
    <col style="width: 5%;">
    <col style="width: 5%;">
    <col style="width: 5%;">

    <thead class="thead-dark">
    <tr class="text-center text-secondary">
        <th>#</th>
        <th class="text-start">Team</th>
        <th>MP</th>
        <th>W</th>
        <th>D</th>
        <th>L</th>
        <th>GF</th>
        <th>GA</th>
        <th>GD</th>
        <th>PTS</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($teams->sortByDesc($prefix.'points') as $team)
        <tr class="text-center">
            <td>{{ $loop->index+1 }}</td>
            <td style="cursor: pointer;" class="text-nowrap fw-bold text-start" onclick="window.location='/teams/{{ $team->id }}'">{{ $team->title }}</td>
            <td>{{ $team->{$prefix.'played'} }}</td>
            <td>{{ $team->{$prefix.'won'} }}</td>
            <td>{{ $team->{$prefix.'drawn'} }}</td>
            <td>{{ $team->{$prefix.'lost'} }}</td>
            <td>{{ $team->{$prefix.'scored'} }}</td>
            <td>{{ $team->{$prefix.'conceded'} }}</td>
            <td>{{ $team->{$prefix.'scored'} - $team->{$prefix.'conceded'} }}</td>
            <td class="fw-bold">{{ $team->{$prefix.'points'} }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
