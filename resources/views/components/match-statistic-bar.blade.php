@props(['kind', 'home', 'away'])

<div class="container py-lg-2 py-2">
    <div class="text-center">
        <span>{{ $kind }}</span>
    </div>
    <div class="progress rounded-0 mx-auto">
        <div class="progress-bar bg-primary text-light fw-bolder" role="progressbar"
             style="width: {{ ($home + $away) !== 0 ? ($home / ($home + $away) * 100) : 0 }}%;"
             aria-valuenow="{{ $home }}" aria-valuemin="0" aria-valuemax="{{ $home + $away }}">
            {{ $home }}
        </div>
        <div class="progress-bar bg-danger text-light fw-bolder" role="progressbar"
             style="width: {{ ($home + $away) !== 0 ? ($away / ($home + $away) * 100) : 0 }}%;"
             aria-valuenow="{{ $away }}" aria-valuemin="0" aria-valuemax="{{ $home + $away }}">
            {{ $away }}
        </div>
    </div>
</div>

<style>
    @media (min-width: 992px) {
        :root {
            --progress-height: 13.5px;
            --progress-width: 50%;
        }

        .progress {
            height: var(--progress-height);
            width: var(--progress-width);
        }

        .progress-bar {
            height: var(--progress-height);
        }
    }

    @media (max-width: 991.98px) {
        :root {
            --progress-height: 11px;
            --progress-width: 100%;
        }

        .progress {
            height: var(--progress-height);
            width: var(--progress-width);
        }

        .progress-bar {
            height: var(--progress-height);
        }
    }




</style>





