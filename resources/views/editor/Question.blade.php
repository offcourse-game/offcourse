@extends('mainEditor')

@section('title')
    @empty($answersQuestion)
        Fragen hinzufügen
    @endempty @isset($answersQuestion)
        Frage bearbeiten
    @endisset
@endsection

@section('activeSideNavItem1') active @endsection
@section('sessionId'){{$sessionId}}@endsection {{-- yields the sessionId to the sidebar--}}
@section('HeaderBoxHeadline') {{$sessionName}} @endsection

@section('mainEditorHead')
    <script src="{{URL::asset('js/editorQuestion.js')}}"></script>
@endsection

@section('mainEditorBody')
    <div class="wrapperOuterMargin">
        @empty($answersQuestion)
            <h3>Fragen hinzufügen</h3>
        @endempty @isset($answersQuestion)
            <h3>Frage bearbeiten</h3>
        @endisset

        <hr/>
        <div class="sessionItem">
            @empty($answersQuestion)
            <form id="form" enctype="multipart/form-data" onsubmit="return validateForm()" action="/editorQuestion/{{$sessionId}}" method="post">
            @else
            <form id="form" enctype="multipart/form-data" onsubmit="return validateForm()" action="/updateQuestion/{{$sessionId}}/{{$answersQuestion['question']->question_id}}" method="post">
            @endempty
                <div class="editorGridQuarterResponsive">
                    <div class="editorGridDescription">
                        <h5>Frage:</h5>
                    </div>
                    <div class="editorGridContent">
                        <textarea class="form-control" type="text" rows="4" name="question" maxlength="200" required>@isset($answersQuestion){{$answersQuestion['question']->question_text}}@endisset</textarea>
                    </div>
                    <div class="editorGridDescription">
                        <label class="btn btn-info shaded bordered bg_pf_primary" data-balloon="Fügt dem Fragentext ein optionales Bild hinzu (maximale Bildgröße: 2 MB)" data-balloon-pos="up" >
                            <span class="iconify iconify_medium_text_mr" data-icon="mdi:cloud-upload-outline"></span>
                            Bild hochladen <input class="fileContainer" type="file" name="image" id="image" onchange="imageChange()" hidden accept="image/*">
                        </label>

                        <small class="form-text text-muted">optionales Bild zur Frage</small>

                        @empty($answersQuestion['question']->image_path)
                            <small class="form-text text-muted" id="smallImgInfo">(kein Bild hochgeladen)</small>
                        @else
                            <small class="form-text text-muted" id="smallImgInfo"></small>
                        @endempty

                        <small class="form-text text-muted">maximale Bildgröße: 2MB</small>
                        <small class="form-text smallAlert" style="visibility: hidden" id="smallImgAlert">Die Datei überschreitet die maximale Größe von 2 MB oder ist kein Bild!</small>
                    </div>
                    <div class="editorGridDescription">
                        @empty($answersQuestion['question']->image_path)
                            <img src="" id="imagePreview" class="editorPreviewImage" style="visibility: hidden">
                        @else
                            <img src="/storage/{{$answersQuestion['question']->image_path}}" id="imagePreview" class="editorPreviewImage float-right">
                        @endempty
                    </div>
                </div>
                <div class="editorGridLeft">
                    @for ($i = 0; $i < 4; $i++)
                    <div class="editorGridDescription">
                        <h5>Antwort {{ $i+1}}:</h5>
                        <div class="editorGridContent">
                            <div class="custom-checkbox">
                                <input type="hidden" name="checkAnswerCorrect{{$i}}" value="0">
                                <input type="checkbox" class="form-check-input" name="checkAnswerCorrect{{$i}}" id="checkAnswerCorrect{{$i}}" value="1" autocomplete="off"
                                @isset($answersQuestion) @if ($answersQuestion['answers'][$i]->correct == 1)
                                checked
                                @endif @endisset>
                                <label class="form-check-label" for="checkAnswerCorrect{{$i}}">richtig</label>
                            </div>
                        </div>
                    </div>
                    <div class="editorGridContent">
                        <textarea class="form-control" type="text" rows="3" name="answer{{$i}}" maxlength="175" required>@isset($answersQuestion){{$answersQuestion['answers'][$i]->answer_text}}@endisset</textarea>
                    </div>
                    @endfor
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="float-right" style="display:inline">
                    @empty($answersQuestion)
                        <button class="btn btn-info shaded bordered bg_pf_primary float-right" type="submit" data-balloon="Frage speichern und weitere Frage eingeben" data-balloon-pos="up" data-balloon-length='medium'>
                            <span class="iconify iconify_medium_text_mr" data-icon="mdi:content-save-outline"></span>
                            Speichern
                        </button>
                    @else
                        <button class="btn btn-info shaded bordered bg_pf_primary float-right" type="submit" data-balloon="Speichert die Änderungen" data-balloon-pos="up">
                            <span class="iconify iconify_medium_text_mr" data-icon="mdi:content-save-outline"></span>
                            Aktualisieren
                        </button>
                    @endempty
                    <small class="form-text smallAlert" style="visibility: hidden" id="smallCheckAlert">Bitte markieren Sie mindestens eine Antwort als richtig!</small>
                </div>
                <br>
                <br>
                <br>
            </form>
        </div>
        <hr/>
        @empty($answersQuestion)
            <a class="btn btn-success float-right shaded bordered bg_pf_correct" href="/editorQuestionSummary/{{$sessionId}}" data-balloon="Zeigt den Fragenpool an" data-balloon-pos="up">
                Keine weiteren Fragen eingeben
                <span class="iconify iconify_medium_text_ml" data-icon="mdi:arrow-right-thick"></span>
            </a>
        @else
            <a class="btn btn-danger float-right shaded bordered bg_pf_wrong" href="/editorQuestionSummary/{{$sessionId}}" data-balloon="Kehrt zum Fragenpool zurück ohne Änderungen zu speichern" data-balloon-pos="up" data-balloon-length='large'>
                <span class="iconify iconify_medium_text_mr" data-icon="bpmn:end-event-cancel"></span>
                Abbrechen
            </a>
        @endempty
        <div class="fillerBottom"></div>
    </div>
@endsection
