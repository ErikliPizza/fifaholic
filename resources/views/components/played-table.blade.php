@props(['matches'])

<select id="week-selector" class="form-select mb-3">
    <option selected>Select a week</option>
    @for($week = 1; $week <= collect($matches)->max('week'); $week++)
        <option value="{{ $week }}">{{ $week }}</option>
    @endfor
</select>


<div id="matches-table" class="table-responsive">
    <table class="table table-striped">
        <col style="width: 40%">
        <col style="width: 5%">
        <col style="width: 40%">
        <thead>
        </thead>
        <tbody>
        @foreach($matches->sortByDesc('created_at') as $match)
            <tr data-week="{{ $match->week }}">
                <td class="text-nowrap fw-bold text-center" style="cursor: pointer;" onclick="window.location='/teams/{{ $match->homeTeam->id }}'">{{ $match->homeTeam->title }}</td>
                <td class="text-nowrap" style="cursor: pointer;" onclick="window.location='/match/{{ $match->id }}'">{{ $match->home_team_score }} - {{ $match->away_team_score }}</td>
                <td class="text-nowrap fw-bold text-center" style="cursor: pointer;" onclick="window.location='/teams/{{ $match->awayTeam->id }}'">{{ $match->awayTeam->title }}</td>
                <td class="text-nowrap fst-italic">{{ $match->week }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script>
    // get a reference to the select element and the matches table
    const weekSelector = document.querySelector('#week-selector');
    const matchesTable = document.querySelector('#matches-table table');

    // add an event listener to the select element to filter matches on change
    weekSelector.addEventListener('change', () => {
        const selectedWeek = weekSelector.value;
        const matches = matchesTable.querySelectorAll('tbody tr');

        // iterate over each match row and toggle its visibility based on whether it matches the selected week
        matches.forEach(match => {
            const weekCell = match.querySelector('td:nth-child(4)');
            const isMatchForSelectedWeek = weekCell.textContent.trim() === selectedWeek;
            if (selectedWeek === "Select a week") {
                match.style.display = 'table-row';
            } else {
                match.style.display = isMatchForSelectedWeek ? 'table-row' : 'none';
            }
        });
    });
</script>

