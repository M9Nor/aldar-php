<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="canonical" href="https://ampbyexample.com/stories/monetization/doubleclick/">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <title>AMP Stories</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{\Module::asset('frontend:assets/fav.svg')}}">
    <script async custom-element="amp-story" src="https://cdn.ampproject.org/v0/amp-story-1.0.js"></script>
    <script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>
    <script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>

    <script async custom-element="amp-story-auto-ads" src="https://cdn.ampproject.org/v0/amp-story-auto-ads-0.1.js">
    </script>

    <style amp-custom>
        * {
            box-sizing: border-box;
        }

        amp-story * {
            font-family: 'Helvitica Nueve', sans-serif;
            color: #fff;
        }

        amp-story-page {
            background: #000;
        }

        amp-story h1 {
            font-size: 46px;
        }

        amp-story h2 {
            font-size: 36px;
        }

        amp-story p {
            font-size: 16px;
            line-height: 24px;
        }

        .bold {
            font-weight: bold;
        }

        .bottom {
            align-content: end;
        }

        .medium {
            font-weight: 600;
        }

        .first {
            padding-top: 65px;
        }

        .last {
            padding-bottom: 65px;
        }

        .blue {
            color: #4285F4;
        }

        .twenty-px {
            font-size: 20px;
        }

        .center {
            text-align: center;
        }

        .lh30 {
            line-height: 30px;
        }

        .icon {
            background-image: url(https://ampbyexample.com/img/AMP-Brand-White-Icon.svg);
            background-repeat: no-repeat;
            background-size: 50px 50px;
            height: 50px;
            object-fit: contain;
            width: 50px;
        }

        .byline {
            letter-spacing: 1.28px;
            padding-bottom: 58px;
        }

        .introducing * {
            line-height: 42px;
        }

        .subtitle-page {
            padding-top: 80px;
        }

        .button {
            align-items: center;
            border: 4px solid #FFFFFF;
            color: #FFFFFF;
            display: flex;
            height: 60px;
            margin: 0 auto;
            max-width: 240px;
            text-decoration: none;
        }

        .button p {
            font-size: 20px;
            width: 100%;
        }

        amp-ad[template="image-template"] img,
        amp-ad[template="video-template"] {
            object-fit: cover;
        }

        ::cue {
            background-color: rgba(0, 0, 0, 0.75);
            font-size: 24px;
            line-height: 1.5;
        }
    </style>
</head>
{{-- {{dd($model)}} --}}

<body>
    <amp-story standalone title="Key Highlights of AMP Conf 2018" publisher="The AMP team"
        publisher-logo-src="https://ampbyexample.com/img/AMP-Brand-White-Icon.svg"
        poster-portrait-src="https://ampbyexample.com/img/overview.jpg">

    @php
    $page_number = 0;
    @endphp

    @foreach ($model->attachments as $attachment)

        @php
            $page_number++
        @endphp

        <amp-story-page id="page-{{$page_number}}">
            <amp-story-grid-layer template="fill">
                <amp-img width="400" height="750" layout="fill" src="{{ $attachment->getUid('420x700') }}"></amp-img>
            </amp-story-grid-layer>
        </amp-story-page>

    @endforeach

    @foreach ($model->externalAttachments as $externalAttachment)

        @php
            $page_number++
        @endphp

        @if($externalAttachment->type == 'video')
          {{-- {{dd($externalAttachment->link)}} --}}

          <amp-story-page id="page-{{$page_number}}">
              <amp-story-grid-layer template="fill">
                
                <amp-youtube autoplay loop width="400" height="750"
                  layout="responsive"
                  data-videoid="{{$externalAttachment->link}}">
                </amp-youtube>
                <amp-img
                  src="{{$externalAttachment->description}}"
                  placeholder
                  layout="fill"
                />
              </amp-story-grid-layer>
          </amp-story-page>
        @elseif('image')
          <amp-story-page id="page-{{$page_number}}">
            <amp-story-grid-layer template="fill">
                <amp-img width="400" height="750" layout="fill" src="{{$externalAttachment->link}}"></amp-img>
            </amp-story-grid-layer>
            <amp-story-grid-layer template="vertical" class="bottom">
                <h2 class="bold test">{{$externalAttachment->title}}</h2>
                <p>{{$externalAttachment->description}}</p>
            </amp-story-grid-layer>
        </amp-story-page>
      @endif
    @endforeach
</body>

</html>