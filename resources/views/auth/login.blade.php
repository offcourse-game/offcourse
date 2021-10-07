@extends('mainEditor')

@section('title') Anmelden @endsection

@section('HideEditorSidenav')hidden @endsection
@section('Hidelogout')hidden @endsection

@section('mainEditorBody')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <br>
                <br>
                <h3>
                    Login für Dozierende
                </h3>
                <hr/>
                <p>Bitte melden Sie sich mit Ihrer GWDG-Kennung an (z. B. mmuster).</p>
                <img class="float-right" style="max-width: 25%; transform: scaleX(-1);" src="{{ URL::asset('images/boss_lowRes.png')}}" alt="">

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label p-0">GWDG-Kennung</label>
                            <div class="col-md-6 p-0">
                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label p-0">GWDG-Passwort</label>
                            <div class="col-md-6 p-0">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>


                        </div>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4 p-0">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Angemeldet bleiben
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4 p-0">
                                <button type="submit" class="btn btn-info shaded bordered bg_pf_primary">
                                    <span class="iconify" data-icon="fe:login" data-inline="false"></span> Anmelden
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
