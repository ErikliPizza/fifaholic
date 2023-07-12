<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card">
                @if(request()->routeIs('dashboard', 'teams'))
                    <x-main-board-menu/>
                @endif
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>

