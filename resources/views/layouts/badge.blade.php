{{--
    blade template / component to easily generate badge visuals

    uses:   $color {"white", "bronze", "silver", "gold", "emerald", "sapphire"}
            $animId                 delay for opening /unfolding animation
            $badgeImageClass        name of image without _color suffix / ending
            $title                  title of badge
            $formattedText          text in badge, can be formatted with html <b> is colored with value of $color  [USE MAXIMUM ~115 CHARACTERS]
            $starCount              amount of stars displayed in the right of image; maximum is 5
            $hidden {"hidden", ""}  is badge hidden?


    USAGE EXAMPLE:

        @component('layouts.badge')
            @slot('color') silver @endslot
            @slot('animId') 1 @endslot
            @slot('badgeImageClass') card @endslot
            @slot('title') auch images können @endslot
            @slot('formattedText') unterschiedlich je nach <b>Farbe</b> dargestellt werden @endslot
            @slot('starCount') 2 @endslot
            @slot('hidden') hidden @endslot
        @endcomponent
--}}


<div class="wrapperTrophy {{"badge_" . $color}}" animID="{{$animId}}" {{$hidden}}>
    <div class="fillTrophyImage">
        <img src="{{ URL::asset('images/badges_svg/' . $badgeImageClass . "_" . $color . ".svg") }}" class="imageTrophy" alt="badge">
    </div>
    <div class="wrapperTrophyDiscription" id="0">
        <h4>{{$title}}</h4>
        {{$formattedText}}
    </div>
    <div class="trophyStars">
        @for($i = 0; $i < html_entity_decode($starCount); $i++)
            <img src="{{ URL::asset('images/badges_svg/' . "star_" . $color . ".svg") }}" class="imageTrophyStar" alt="star">
        @endfor
    </div>
</div>