<nav class="navbar navbar-expand-lg navbar-ligh bg-white border-bottom">
    <div class="sidebar-heading"><a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
    </div>
    <div class="nav-item">
        <a class="nav-link" id="dataRefresh" onclick="newData()">
            <i class="fa fa-refresh text-primary" aria-hidden="true" style="cursor:pointer;" title="Data refreshen"></i>
        </a>
    </div>
    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
        @guest
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
            @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
            @endif
        @else
            <li class="nav-item dropdown">

                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <span class="badge-pill badge badge-secondary">{{ (new \App\Bon)->getTotals() }}</span></a>
                <div class="dropdown-menu">
                    @foreach($bedrijven AS $bedrijf)
                        <a href="{{ route('bedrijf', $bedrijf->id) }}" class="dropdown-item"><span
                                class="badge-pill badge badge-secondary">{{ (new \App\Bon)->getTotalsCompany($bedrijf->soort) }}</span> {{ $bedrijf->naam }}
                        </a>
                    @endforeach
                </div>
            </li>
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->naam }} <span class="caret"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        @endguest
    </ul>
</nav>
