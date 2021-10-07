@extends('mainEditor')

@section('title')
    Sessioninformationen
@endsection

@section('activeSideNavItem0') active @endsection

@section('mainEditorBody')
    <div class="wrapperOuterMargin">
        <h3>Sessioninformationen eingeben</h3>
        <hr/>
        <form action="/editorSessionSetup" method="post">
            <div class="sessionItem">
                <div class="editorGridHalf">
                    <div class="editorGridContent">
                        <div class="form-group">
                            <label for="nameInput"><h5>Sessionname:</h5></label>
                            <input type="text" maxlength="50" class="form-control" name="nameInput" aria-describedby="nameHelp" placeholder="..." required>
                            <small class="form-text text-muted">Wählen Sie einen geeigneten Namen für Ihre Session.</small>
                        </div>
                    </div>
                </div>
                <br>
                <br>
            </div>
            <hr/>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <button type="submit" class="btn btn-success float-right shaded bordered bg_pf_correct">
                Weiter
                <span class="iconify iconify_medium_text_ml" data-icon="mdi:arrow-right-thick"></span>
            </button>
        </form>
    </div>
@endsection
