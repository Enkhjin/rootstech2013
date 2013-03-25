<?php
include_once "modules/db/DAOFactory.php";

include_once "modules/graph/PmfSimile.php";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"

 "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>
   <!-- See http://developer.yahoo.com/yui/grids/ for info on the grid layout -->

   <title>Timeline</title>

   <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />


   <!-- See http://developer.yahoo.com/yui/ for info on the reset, font and base css -->

   <link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css">
   <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/base/base-min.css"> 


   <!-- Load the Timeline library after reseting the fonts, etc -->

   <script src="http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js?bundle=true" type="text/javascript"></script>

 
   <link rel="stylesheet" href="styles/simile/timeline.css" type="text/css">

   <!-- Since we don't have our own server, we do something tricky and load our data here as if it were a library file -->
   <script type="text/javascript">
<?php

	$g = new PmfSimile();
	$g->addPeople();
	$g->display('');
?>
</script>


   <script>        
        var tl;
        function onLoad() {
            var tl_el = document.getElementById("tl");
            var eventSource1 = new Timeline.DefaultEventSource();
            
            var theme1 = Timeline.ClassicTheme.create();
            theme1.autoWidth = true; // Set the Timeline's "width" automatically.
                                     // Set autoWidth on the Timeline's first band's theme,
                                     // will affect all bands.
            theme1.timeline_start = new Date(Date.UTC(1200, 0, 1));
            theme1.timeline_stop  = new Date(Date.UTC(2100, 0, 1));
            
            var d = Timeline.DateTime.parseGregorianDateTime("1900")
            var bandInfos = [
                Timeline.createBandInfo({
                    width:          45, // set to a minimum, autoWidth will then adjust
                    intervalUnit:   Timeline.DateTime.DECADE, 
                    intervalPixels: 200,
                    eventSource:    eventSource1,
                    date:           d,
                    theme:          theme1,
                    layout:         'original'  // original, overview, detailed
                })
            ];
                                                            
            // create the Timeline
            tl = Timeline.create(tl_el, bandInfos, Timeline.HORIZONTAL);
            
            var url = '.'; // The base url for image, icon and background image
                           // references in the data
            eventSource1.loadJSON(timeline_data, url); // The data was stored into the 
                                                       // timeline_data variable.
            tl.layout(); // display the Timeline
        }
        
        var resizeTimerID = null;
        function onResize() {
            if (resizeTimerID == null) {
                resizeTimerID = window.setTimeout(function() {
                    resizeTimerID = null;
                    tl.layout();
                }, 500);
            }
        }
   </script>

</head>

<body onload="onLoad();" onresize="onResize();">

<div id="doc3" class="yui-t7">

   <div id="hd" role="banner">
     <h1>Local Timeline Example</h1>
   </div>

   <div id="bd" role="main">

	   <div class="yui-g">

	     <div id='tl'></div>
	     <p>To move the Timeline: use the mouse scroll wheel, the arrow keys or grab and drag the Timeline.</p>

	   </div>

	 </div>

   <div id="ft" role="contentinfo">
     <p>Thanks to the <a href=''>Simile Timeline project</a> Timeline version <span id='tl_ver'></span></p>
     <script>Timeline.writeVersion('tl_ver')</script> 
   </div>

</div>


</body>

</html>
