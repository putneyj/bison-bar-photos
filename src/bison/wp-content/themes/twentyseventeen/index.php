<!doctype html>
<html class="no-js" lang="">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://cdn.rawgit.com/nnattawat/flip/master/dist/jquery.flip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/lodash/4.17.4/lodash.min.js"></script>

    <style>
      body {
        height: 100%;
        width: 100%;
        margin: 0;
      }

      .image-div {
        height: 100%;
        width: 100%;
      }

      .absolute-center {
        text-align: center;
        margin: auto;
        position: absolute;
        top: 0; left: 0; bottom: 0; right: 0;
      }

      .hidden {
        display: none;
      }

      #no-images-message {
        height: 100px;
      }

      #card > div {
        background-size: 100%;
      }
    </style>
	</head>
	<body>
    <div class="hidden absolute-center" id="no-images-message">
      <h1>There are no images to load!</h1>
    </div>
    <div class="hidden image-div absolute-center" id="card"> 
      <div class="front"> 
        
      </div> 
      <div class="back">
        
      </div> 
    </div>
    <script type="text/javascript">
      var images = [];
      var minutesBetweenUpdates = 1;
      var secondsBetweenTransitions = 10;
      var currentImageIndex = -1;

      updateImageList();
      $("#card").flip({
        'trigger': 'manual',
        'speed': 1250
      });

      function updateImageList() {
        $.getJSON( "wp-json/wp/v2/media", function( data ) {
          var freshStart = images.length === 0;

          if(!_.isEqual(images, data)) {
            difference = _.differenceBy(data, images, 'id');
            images = data;
            console.log('Difference: ' + difference);
            console.log('Image Data: ' + images);

            if(difference) {
              if(freshStart) {
                console.log("Setting first image");
                setDivBackground($("#card .front"), images[getNextImageIndex()]);
              }
              for(i = 0; i < difference.length; i++) {
                preload(difference[i].source_url);
              }
              swapImages();
            }
          } else if (freshStart) {
            swapImages();
          }
        });
        setTimeout(updateImageList, minutesBetweenUpdates * 60 * 1000);
      }

      function swapImages() {
        if(images.length > 0) {
          $('#card').removeClass('hidden');
          $('#no-images-message').addClass('hidden');

          var flip = $("#card").data("flip-model");
          if(flip.isFlipped) {
            setDivBackground($("#card .back"), images[getNextImageIndex()]);
          } else {
            setDivBackground($("#card .front"), images[getNextImageIndex()]);
          }
          setTimeout(transitionToNextImage, secondsBetweenTransitions * 1000);
        } else {
          $('#card').addClass('hidden');
          $('#no-images-message').removeClass('hidden');
        }
      }

      function transitionToNextImage() {
        $("#card").flip('toggle');
        swapImages();
      }

      function setDivBackground(div, imageObj) {
        $(div).css({
          'background-image': 'url(' + imageObj.source_url + ')'
        });
      }

      function getNextImageIndex() {
        if(images) {
          currentImageIndex += 1;
          if(currentImageIndex >= images.length) {
            currentImageIndex = 0;
          }
        } else {
          currentImageIndex = -1;
        }

        return currentImageIndex;
      }

      function preload(url) {
        $('<img/>')[0].src = url;
      }

      setTimeout(function() {
        location.reload();
      }, 5 * 60 * 1000);
    </script>
	</body>
</html>
