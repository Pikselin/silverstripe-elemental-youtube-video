<% if $VideoID %>
    <div class="elemental-block elemental-block--video $TitleClass" id="element-$ID">
        <% if $addInnerDiv %><div class="inner"><% end_if %>
        $BlockTitle.RAW
        <div class="video">
            <div class="video__wrapper">
                <iframe
                        <% if $Title %>title="$Title"<% end_if %>
                        width="560"
                        height="315"
                        style="background: #ddd"
                        src="//www.youtube.com/embed/$YouTubeID?rel=0"
                        frameborder="0"
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                </iframe>
            </div>
            <div class="video__text">
                <div class="video__text__content">
                    $Content
                </div>
                <div class="video__text__duration">
                    Duration: $Duration
                </div>
            </div>
            <% if $Transcription %>
                <div class="video-transcription video-transcription--hidden">
                    <button class="button no-button text-left with-icon icon-left" type="button" aria-controls="transcription-$ID">
                        <img class="chevron-down" alt="chevron-down" src="{$ThemeDir}/images/chevron-down.svg">
                        Read transcription for this video
                    </button>
                    <div class="video-transcription__text" id="transcription-$ID" aria-hidden="false">
                        $Transcription
                    </div>
                </div>
            <% end_if %>
        </div>
        <% if $addInnerDiv %></div><% end_if %>
    </div>
<% end_if %>
