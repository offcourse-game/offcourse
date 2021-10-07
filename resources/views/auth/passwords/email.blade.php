@extends('mainEditor')

@section('title')
    Zurücksetzen
@endsection

@section('HideEditorSidenav')hidden @endsection
@section('Hidelogout')hidden @endsection

@section('mainEditorBody')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <br>
                <br>
                <h3>Passwort zurücksetzen</h3>
                <hr/>
                <br>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    Bitte melden Sie sich bei Henrik Wesseloh unter <a href="mailto:henrik.wesseloh@uni-goettingen.de">henrik.wesseloh@uni-goettingen.de</a> und lassen Sie Ihr Passwort manuell zurücksetzen.
                    <br>
                    <br>
                    <a class="btn btn-info shaded bordered bg_pf_primary" href="/editorHome"><span class="iconify" data-icon="dashicons:arrow-left-alt" data-inline="false"></span> zurück</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
