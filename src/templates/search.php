<!DOCTYPE html>
<html>
<head>
<title>Results</title>
<meta name="viewport" content="initial-scale=1.0">
<meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/style-map.css">
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <link rel="stylesheet" type="text/css" href="css/wide.css" media="screen and (min-width: 1000px)">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        

<style>
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    position:relative;
  }
  #roy {
    height: 100%;
  }
  #postcode-form {
  	position:absolute;
  	bottom:0;
  	right:0;
	  }
  #demodiv {
    position:absolute;
    top:10px;
    right:10px;
    width:200px;
    height:200px;
    background:white;
  }
</style>
</head>
<body>
   <header>
    <div class="ContentBox">
		<div class="SiteLogo"><a href="/"><img src="images/grey-areas-logo.png"></a></div>
        
        <div class="Social">
            <ul class="SocialIcons">
                <li><a href="https://www.facebook.com/almostanythingdesign"><img src="images/social-media-icons_01.png"></a></li>
                <li><a href="https://twitter.com/almostanything"  target="_blank"><img src="images/social-media-icons_02.png"></a></li>
                <li><a href="http://www.linkedin.com/company/almost-anything-web-&-graphic-design"  target="_blank"><img src="images/social-media-icons_03.png"></a></li>
            </ul>
            
             <nav>
            <ul class="MainMenu">
                <li><a href="/">Home</a></li>
                <li><a href="https://sites.google.com/almostanything.com.au/greyareas">About</a></li>
                <li><a href="http://www.almostanything.com.au/contact-us/">Contact</a></li>
                </ul>
        </nav>
        
         <div class="MobileMenu"></div>
        
        </div>
      
    </div><!---/ END OF HEADER / CONTENT BOX -->
</header>
    <div class="MapWrapper">
        <div id="roy"></div>
        <div class="DataBox">
                        <h1 id="data-field-postcode"></h1>
                        <div class="DataWrapper">
                            <div class="Data">
                                    <div class="stay-active"><h2>Stay Active:</h2><progress id="bar-active" value="0" max="100">55% / 100%</progress></div>
                                    <div class="stay-connected"><h2>Stay Connected:</h2><progress id="bar-connected" value="0" max="100"></progress></div>
                                    <div class="contribute-economically"><h2>Contribute Economically:</h2><progress id="bar-economic" value="0" max="100"></progress></div>
                                    <div class="contribute-socially"><h2>Contribute Socially:</h2><progress id="bar-social" value="0" max="100"></progress></div>
                                    <div class="contribute-culturally"><h2>Contribute Culturally:</h2><progress id="bar-cultural" value="0" max="100"></progress></div><div class="pensioner-count"><h2>Pensioner Count:</h2><progress value="0" max="100"></progress></div>
                            </div>
                            <div class="PostCodeSearch">
        	                   <form>
                                <input type="text" placeholder="Example: 4701"  name="postcode">
                                <input type="submit" class="button" value="SEARCH" formaction="search.html" method="get">
                                </form>
                            </div>
                            <div class="DataResults">
                                <div class="DataText">
                                    
                                </div>
                                <div class="GradeResult">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
    
    </div>
<footer>
<div class="SupportingHeading">
	<div class="ContentBox">
    	<h1>Made using open data from</h1>
    </div>
</div>

<div class="SupportingContent">
	<div class="ContentBox">
    	<ul class="DataLogos">
			<li><a href="https://www.qld.gov.au/" target="_blank"><img src="images/qld.jpg"></a></li>
			<li><a href="http://www.australia.gov.au" target="_blank"><img src="images/augov.jpg"></a></li>
			<li><a href="http:///www.abs.gov.au/" target="_blank"><img src="images/abs.jpg"></a></li>
			<li><a href="https://www.govhack.org" target="_blank"><img src="images/GovHackLogo.jpg"></a></li>
        </ul>
    </div>
</div>

<div class="FooterMenu">
	<div class="ContentBox">
    	 <a href="/">Home</a> |
     	<a href="https://sites.google.com/almostanything.com.au/greyareas">About</a> |
         <a href="http://www.almostanything.com.au/contact-us/">Contact</a>
    </div>
</div>


<div class="Credits">
	<div class="ContentBox">
		<a href="http://almostanything.com.au"><img src="images/aa-logo.jpg"></a>
        <p>GovHack 2016 | Designed by team: <a href="http://almostanything.com.au" target="_blank">5 Shades of Grey</a></p>
    </div>
</div>

<div class="Disclaimers">
	<div class="ContentBox">
		This web app was developed as a proof of concept for GovHack 2016. This web app is not endorsed or officially supported by the Queensland Government, Australia Government, Australian Bureau of Statistics or GovHack. All Characters portrayed are fictious and reference to those living or dead is purely coincidental. The cheetah is the fastest land animal.  Never whistle with a mouth full of jelly. Spoiler: Darth Vader is Luke's Father (too soon?)
		
		Photo credit: <a href="https://www.flickr.com/photos/adwriter/257937032/">adwriter</a> via <a href="https://visualhunt.com/photos/romance/">Visual Hunt</a> / <a href="http://creativecommons.org/licenses/by-nc/2.0/">CC BY-NC</a>
	</div>
</div>

</footer>
<script src="https://maps.googleapis.com/maps/api/js?callback=initMap&amp;key=AIzaSyDwq1vBN2Yslgozpw6IzWA6buqVyO52fkM" async defer></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>
  var map;
  var currentArea = null;
  var postcode = '<?php echo $postcode; ?>';

  function processPoints(geometry, callback, thisArg) {
    if (geometry instanceof google.maps.LatLng) {
      callback.call(thisArg, geometry);
    } else if (geometry instanceof google.maps.Data.Point) {
      callback.call(thisArg, geometry.get());
    } else {
      geometry.getArray().forEach(function(g) {
        processPoints(g, callback, thisArg);
      });
    }
  }
  function initMap() {
    
    map = new google.maps.Map(document.getElementById('roy'), {
		zoom: 4,
		center: {lat: -28, lng: 137}
    });

	  map.data.addListener('addfeature', function(e) {
      var bounds = new google.maps.LatLngBounds();
	    processPoints(e.feature.getGeometry(), bounds.extend, bounds);
	    map.fitBounds(bounds);
	  });

    google.maps.event.addListener(map, 'click', function( event ){
      var lat = event.latLng.lat();
      var lon = event.latLng.lng();

      $.getJSON('/'+lat+'/'+lon+'/topostcode.json').then(function(res){
        if(res.response != 200) {
          alert(res.message);
          return;
        }
        var newpostcode = res.postcode;
        window.location = '/search.html?postcode='+newpostcode;
      });

    });

    map.data.forEach(function(feature) {
      map.data.remove(feature);
    });

    if (postcode === '') {
      alert('Enter a postcode!');
      return;
    }

    $.getJSON('/'+postcode+'.json').then(function(res){
      if (res.response != 200) {
        alert(res.message);
      	return;
      }

      map.data.addGeoJson(res.geometry);

      $('#data-field-postcode').html(res.postcode);

      
      console.log('mins');
      console.log(res.min);
      console.log('maxs');
      console.log(res.max);
      console.log('values');
      console.log(res.values);
      console.log('averages');
      console.log(res.avgs);
      console.log('percentages');
      console.log(res.percentages);

      for (var key in res.percentages) {
        $('#bar-'+key).attr('value',res.percentages[key]);
      }

      var text = '';
      for (var n in res.counts) {
        text += n+' '+ res.counts[n]+'\n';
      }
      $('#demodiv').html('<pre>'+text+'</pre>');
    });
  }

</script>
</body>
</html>