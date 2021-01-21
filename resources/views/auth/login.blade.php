@extends('layouts.login')

@section('content')
<div class="container-fluid">
  <div class="row no-gutter">
    <div class="col-md-8 col-lg-6" style="background-color: #f2f2f2;">
      <div class="login d-flex align-items-center py-5">
        <div class="container">
          <div class="row">
            <div class="col-md-9 col-lg-8 mx-auto m-4 p-4 ss-card">
              <h3 class="login-heading mb-4 text-center">Management panel</h3>
              <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group row">
                        <label for="username" class="col-md-4 col-form-label text-md-right">Gebruikersnaam</label>

                        <div class="col-md-6">
                            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-md-4 col-form-label text-md-right">Wachtwoord</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6 offset-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    Onthouden
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                Inloggen
                            </button>
                        </div>
                    </div>
                </form>
                @if (session('status'))
                    <div class="alert alert-success mt-4">
                        {{ session('status') }}
                    </div>
                @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="d-none d-md-flex col-md-4 col-lg-6 login-bg-image shadow shadow-lg"></div>
  </div>
</div>

@endsection