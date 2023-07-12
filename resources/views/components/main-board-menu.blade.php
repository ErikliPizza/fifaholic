<ul class="nav nav-pills nav-justified p-1">
    <li class="nav-item px-1">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active':'' }}" aria-current="page" href="/dashboard">{{ __('Dashboard') }}</a>
    </li>
    <div class="vr"></div>
    <li class="nav-item px-1">
        <a class="nav-link {{ request()->routeIs('teams') ? 'active':'' }}" aria-current="page" href="/teams">{{ __('Teams') }}</a>
    </li>
</ul>
