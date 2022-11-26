<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Presenter Launch Zoom</title>

  <link rel="stylesheet" href="{{asset('assets/fonts/simple-line-icons/css/simple-line-icons.css')}}">
  <link rel="stylesheet" href="{{asset('assets/fonts/iconsmind-s/css/iconsminds.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/vendor/bootstrap4/bootstrap.min.css')}}">
  <link href="{{asset('assets/font-awesome/css/fontawesome.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/font-awesome/css/solid.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/chosen/chosen.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="/js/videojs/video-js.css"/>
  <style>

    body {
        background-color: #c0c0c0!important;
        overflow-x: hidden;
    }

     div.register-box {
        background-color: #fff;
        padding: 25px;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.12), 0 2px 4px 0 rgba(0,0,0,0.08);
    }

    img {
        width: 100%;
    }

    div.chosen-container {
        font-size: 1.3rem;
        line-height: 1.6rem;
    }
    .chosen-container .chosen-results li, .chosen-container-multi .chosen-choices li.search-choice span {
        font-size: 1.3rem;
        line-height: 1.6rem;
    }
    .pac-container:after {
        background-image: none !important;
        height: 0px;
    }
    .chosen-container-active .chosen-choices, .chosen-container-multi .chosen-choices {
        border-radius: .25rem;
        border: 1px solid #ced4da;
        line-height: 1.6rem;
    }

</style>
</head>
<body class="">

<div class="container-flex">
<div class="row">
    <div class="col-sm-12">
        <img src="/assets/img/medweekbanner.jpg"/>
    </div>
</div>
<div class="row">
<div class="col-sm-12">&nbsp;</div>
</div>
<div class="row mx-3">
<div class="register-box col-sm-12">
<h3>Zoom Client Will Launch Automatically From This Page </h3>

<p><strong>Keep this page open!</strong> - client will launch 1 hour before your presentation is set to begin, you can do checks with staff to make sure the stream is good and you are ready, and then staff will make your stream live to attendees.</p>

<video id=example-video width=600 height=300 class="video-js vjs-default-skin d-none" controls></video>
<script src="/js/videojs/video.js"></script>
 
<!-- Dash.js -->
<script src="/js/dashjs/dash.all.min.js"></script>
 
<!-- videojs-contrib-dash script -->
<script src="/js/videojs-dash/videojs-dash.min.js"></script>
 
</div>
</div>
</div>
</body>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script>

$(function () {
  window.setInterval(function () {
    window.location.reload();
  }, 5000);
});

var token = '{{ $token }}';

if (token == 'CKjhJ65rUg2HDUmKz75OdsIfJQ7qDIjQ') {
  document.location.href = 'https://zoom.us/s/97242769717?zak=eyJ6bV9za20iOiJ6bV9vMm0iLCJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJjbGllbnQiLCJ1aWQiOiJ3VmdFMDM4Y1FJZUh0Tmo1MlRKRTN3IiwiaXNzIjoid2ViIiwic3R5IjoxLCJ3Y2QiOiJhdzEiLCJjbHQiOjAsInN0ayI6InNENUFGRGVLUGh1T3hmdUxIMXo3dDlWcUUzU1VkU0h3VExMWEdVMWIyM0UuRWdJQUFBRjBrcHZJelFBQUhDQWdhV0ZyWjJ0dWFtVmtVVmgxTDFGaWEzZG5OWGxtTjB4UlRXcHlhMU55VDJjQURETkRRa0YxYjJsWlV6TnpQUU5oZHpFIiwiZXhwIjoxNjAwMTk0NzE3LCJpYXQiOjE2MDAxODc1MTcsImFpZCI6IlhCNTg3UTlpUVJLZ0Z1UExZd1BUQ1EiLCJjaWQiOiIifQ.qCNOaxDGufhgiOaLv95EPm41KkPGzZ7RWxroNASt2OM';
}

var player = videojs('example-video');
 
player.ready(function() {
  player.src({
    src: 'http://3.135.178.90:1935/vizzi/myStream/manifest.mpd',
    type: 'application/dash+xml'
  });
 
  player.autoplay('any');
});
</script> 