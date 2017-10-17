(function ( $ ) {
	"use strict";

	$(function () {

		var arrMarkers = [];
		var markersArray = [];
		var markerCluster,map, bounds, boxText, i, text;
	    var circle = null;

    	var ib = new InfoBox();

	    var boxText = document.createElement("div");
	    boxText.className = 'map-box';

	     var currentInfobox;

		if(wsmap.centerPoint) {
			var latlngStr = wsmap.centerPoint.split(",",2);
			var lat = parseFloat(latlngStr[0]);
			var lng = parseFloat(latlngStr[1]);

			var center = new google.maps.LatLng(lat, lng);
		} else {
			var center = new google.maps.LatLng(-33.92, 151.25);
		}

	    var geocoder = new google.maps.Geocoder();	

	    var boxOptions = {
            content: boxText,
			disableAutoPan: true,
			alignBottom : true,
			maxWidth: 0,
			pixelOffset: new google.maps.Size(-60, -5),
			zIndex: null,
				boxStyle: { 
				width: "450px"
			},
			closeBoxMargin: "0",
			closeBoxURL: "",
			infoBoxClearance: new google.maps.Size(1, 1),
			isHidden: false,
			pane: "floatPane",
			enableEventPropagation: false,
      };

	    function iconColor(color) {
		    return {
		        path: 'M19.9,0c-0.2,0-1.6,0-1.8,0C8.8,0.6,1.4,8.2,1.4,17.8c0,1.4,0.2,3.1,0.5,4.2c-0.1-0.1,0.5,1.9,0.8,2.6c0.4,1,0.7,2.1,1.2,3 c2,3.6,6.2,9.7,14.6,18.5c0.2,0.2,0.4,0.5,0.6,0.7c0,0,0,0,0,0c0,0,0,0,0,0c0.2-0.2,0.4-0.5,0.6-0.7c8.4-8.7,12.5-14.8,14.6-18.5 c0.5-0.9,0.9-2,1.3-3c0.3-0.7,0.9-2.6,0.8-2.5c0.3-1.1,0.5-2.7,0.5-4.1C36.7,8.4,29.3,0.6,19.9,0z M2.2,22.9 C2.2,22.9,2.2,22.9,2.2,22.9C2.2,22.9,2.2,22.9,2.2,22.9C2.2,22.9,3,25.2,2.2,22.9z M19.1,26.8c-5.2,0-9.4-4.2-9.4-9.4 s4.2-9.4,9.4-9.4c5.2,0,9.4,4.2,9.4,9.4S24.3,26.8,19.1,26.8z M36,22.9C35.2,25.2,36,22.9,36,22.9C36,22.9,36,22.9,36,22.9 C36,22.9,36,22.9,36,22.9z M13.8,17.3a5.3,5.3 0 1,0 10.6,0a5.3,5.3 0 1,0 -10.6,0',
				strokeOpacity: 0,
				strokeWeight: 1,
				fillColor: color,
				fillOpacity: 1,
				rotation: 0,
				scale: 1,
				anchor: new google.maps.Point(19,52)
		   };
		}

	    var clusterStyles = [
		  {
		    textColor: 'white',
		    url: ws.theme_url+'/images/m1.png',
		    height: 50,
		    width: 50
		  },
		 {
		    textColor: 'white',
		    url: ws.theme_url+'/images/m2.png',
		    height: 50,
		    width: 50
		  },
		 {
		    textColor: 'white',
		    url: ws.theme_url+'/images/m3.png',
		    height: 50,
		    width: 50
		  },
		 {
		    textColor: 'white',
		    url: ws.theme_url+'/images/m4.png',
		    height: 50,
		    width: 50
		  },
		  {
		    textColor: 'white',
		    url: ws.theme_url+'/images/m5.png',
		    height: 50,
		    width: 50
		  }
		];
		var set_zoom = parseInt(wsmap.default_zoom);
		var maptype = wsmap.map_type;

      	function initialize() {
	        map = new google.maps.Map(document.getElementById('ws-map'), {
	          	center: center,
	          	zoom: set_zoom,
	          	backgroundColor: '#fff',
				scrollwheel: wsmap.scroll_zoom,
				panControl: false,
				mapTypeId: google.maps.MapTypeId[maptype],
				zoomControl: true,
			    zoomControlOptions: {
			        style: google.maps.ZoomControlStyle.LARGE,
			        position: google.maps.ControlPosition.LEFT_CENTER
			    }
		
	        });
	

			var bounds = new google.maps.LatLngBounds();
			markersArray = [];

			for (var key in ws_big_map) {
			    var data = ws_big_map[key];
			    
			    var marker_content;
			   	if(data['ismerged'] ==="yes") {
			   		marker_content = '<ul class="same-spot-markers">'+data['ibmergecontent']+'</ul><div class="infoBox-close"><i class="fa fa-times"></i></div>';
			   	} else {
			   		marker_content = data['ibcontent'];
			   	}
			    var marker = new google.maps.Marker({
			        position: new google.maps.LatLng (data['lat'], data['lng']),
			        map: map,
		           
		          	icon: iconColor(wsmap.marker_color), 
		          	id: data['id'],
		          	ibcontent: marker_content,
			    });
			    markersArray.push(marker);

			    
			    arrMarkers[data['id']] = marker;
			    //extend the bounds to include each marker's position
			   
				bounds.extend(marker.position);

				//add infoboxes


		        google.maps.event.addListener(marker, 'click', (function(marker, i) {
		          return function() {
		            ib.setOptions(boxOptions);
		            boxText.innerHTML = this.ibcontent;
		            ib.open(map, this);
		            currentInfobox = this.id;
		            var latLng = this.getPosition();
		            map.panTo(latLng);
		            map.panBy(90,-115);

		            google.maps.event.addListener(ib,'domready',function(){
		              $('.infoBox-close').click(function() {
		                  ib.close();
		              });
		            });

		          }
		        })(marker, i));

	            if(wsmap.autofit) {
			    	map.fitBounds(bounds); 
			    	//map.setZoom(set_zoom);
			    }
		    }

		

	        var options = {
	            imagePath: ws.theme_url+'/images/m',
	            styles : clusterStyles,
	            minClusterSize : 2,
	            maxZoom: 19

	        };

	        if(wsmap.use_clusters){
	        	markerCluster = new MarkerClusterer(map, markersArray, options); 
	        }

        	google.maps.event.addDomListener(window, "resize", function() {
					var center = map.getCenter();
					google.maps.event.trigger(map, "resize");
					map.setCenter(center); 
		      	});

      	 	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			    map.setOptions( { draggable: false });
			}

	    }

		google.maps.event.addDomListener(window, 'load', initialize);

		  $('#nextpoint').click(function(e){
	          e.preventDefault();
	          var index = currentInfobox;
	          if (index+1 < arrMarkers.length ) {
	              google.maps.event.trigger(arrMarkers[index+1],'click');
	          } else {
	              google.maps.event.trigger(arrMarkers[0],'click');
	          }
      	})


      	$('#prevpoint').click(function(e){
          e.preventDefault();
          if ( typeof(currentInfobox) == "undefined" ) {
               google.maps.event.trigger(arrMarkers[arrMarkers.length-1],'click');
          } else {
               var index = currentInfobox;
               if(index-1 < 0) {
                  //if index is less than zero than open last marker from array
                 google.maps.event.trigger(arrMarkers[arrMarkers.length-1],'click');
               } else {
                  google.maps.event.trigger(arrMarkers[index-1],'click');
               }
          }

      	})
	/*eof*/

	});
}(jQuery));
