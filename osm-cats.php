<?php
/*
Plugin Name: OpenStreetMap Categories with Leaflet.js
Plugin URI: http://keikreutler.github.com/OSM-Categories/
Description: OpenStreetMap plugin to embed a map with dynamic markers, displaying article excerpts and linking to posts. You can sort map content by categories and tags.
Version: 0.1
Author: Kei Kreutler
Author http://ourmachine.net
License: GPL2
*/

/*  Copyright 2014 Kei Kreutler  (email : kei@ourmachine.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action( 'admin_menu', 'osm_cats_menu' );
add_action( 'admin_init', 'register_osm_cats_settings' );

function register_osm_cats_settings() {
  register_setting( 'osm_cats', 'osm_cats_map_width' );
  register_setting( 'osm_cats', 'osm_cats_map_height' );
  register_setting( 'osm_cats', 'osm_cats_center_lon' );
  register_setting( 'osm_cats', 'osm_cats_center_lat' );
  register_setting( 'osm_cats', 'osm_cats_zoom_level' );
  register_setting( 'osm_cats', 'osm_cats_disable_zoom_wheel' );
  register_setting( 'osm_cats', 'osm_cats_include_cats' );
  register_setting( 'osm_cats', 'osm_cats_exclude_cats' );
  register_setting( 'osm_cats', 'osm_cats_marker_custom_field' );
  register_setting( 'osm_cats', 'osm_cats_marker_show_thumbnail' );
  register_setting( 'osm_cats', 'osm_cats_marker_show_excerpt' );
  register_setting( 'osm_cats', 'osm_cats_marker_images_path' );
  register_setting( 'osm_cats', 'osm_cats_marker_icon' );
}

function osm_cats_menu() {
  add_options_page( 'OSM Categories Plugin Options', 'OSM Categories', 'manage_options', 'osm_cats_plugin', 'osm_cats_plugin_options' );
}

function osm_cats_plugin_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  ?>
  <div class="wrap">
  <h2>OSM Categories Settings</h2>

  <form method="post" action="options.php">
    <?php settings_fields( 'osm_cats' ); ?>
    
    <h3>General Map settings</h3>
    <table class="form-table">

      <tr valign="top">
        <th scope="row">OSM map width</th>
        <td>
          <input type="text" name="osm_cats_map_width" value="<?php echo get_option('osm_cats_map_width'); ?>" />
          <small>Default is 100%, don't forget the unit.</small>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">OSM map height</th>
        <td>
          <input type="text" name="osm_cats_map_height" value="<?php echo get_option('osm_cats_map_height'); ?>" />
          <small>Default is 300px, don't forget the unit.</small>
        </td>
      </tr>
      
      <tr valign="top">
        <th scope="row">Zoom Level</th>
        <td>
          <input type="text" name="osm_cats_zoom_level" value="<?php echo get_option('osm_cats_zoom_level'); ?>" />
          <small>Default is 12, OSM zoom values range from 0 to 18, with 18 being the most zoomed in.</small>
        </td>
      </tr>
      
      <tr valign="top">
        <th scope="row">Zoom Wheel</th>
        <td>
          <input type="checkbox" name="osm_cats_disable_zoom_wheel" id="osm_cats_disable_zoom_wheel" value="1" <?php checked( '1', get_option( 'osm_cats_disable_zoom_wheel' ) ); ?> />
          <label for="osm_cats_disable_zoom_wheel">Disable zoom by mouse wheel or touchpad.</label><br />
        </td>
      </tr>
      
      <tr valign="top">
        <th scope="row">Center Lon</th>
        <td><input type="text" name="osm_cats_center_lon" value="<?php echo get_option('osm_cats_center_lon'); ?>" /></td>
      </tr>
      
      <tr valign="top">
        <th scope="row">Center Lat</th>
        <td><input type="text" name="osm_cats_center_lat" value="<?php echo get_option('osm_cats_center_lat'); ?>" /></td>
      </tr>
    </table>


    <h3>Category settings</h3>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Include categories</th>
        <td>
          <input type="text" name="osm_cats_include_cats" value="<?php echo get_option('osm_cats_include_cats'); ?>" />
          <small>A comma separated list of category IDs.</small>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Exclude categories</th>
        <td>
          <input type="text" name="osm_cats_exclude_cats" value="<?php echo get_option('osm_cats_exclude_cats'); ?>" />
          <small>A comma separated list of category IDs.</small>
        </td>
      </tr>
    </table>


    <h3>Marker settings</h3>
    <table class="form-table">  

      <tr valign="top">
      <th scope="row">Marker popup settings</th>
        <td>
          <input type="checkbox" name="osm_cats_marker_show_thumbnail" id="osm_cats_marker_show_thumbnail" value="1" <?php checked( '1', get_option( 'osm_cats_marker_show_thumbnail' ) ); ?> />
          <label for="osm_cats_marker_show_thumbnail">Show article thumbnail</label><br />
          <input type="checkbox" name="osm_cats_marker_show_excerpt" id="osm_cats_marker_show_excerpt" value="1" <?php checked( '1', get_option( 'osm_cats_marker_show_excerpt' ) ); ?> />
          <label for="osm_cats_marker_show_excerpt">Show article excerpt</label>
        </td>
      </tr>
    </table> 
    
    <h4>How to create your own marker images</h4>
    <ol>
      <li>Create your own marker images for each category or one for all.</li>
      <li>Name your images: marker_CATEGORY-ID.png or just marker.png</li>
      <li>Create a folder on your webserver, for example: /wp-content/osm-marker</li>
      <li>Copy your images to this folder.</li>
      <li>Enter the folder path below.</li>
    </ol>
    <p>If you don't create your own images, the default OSM marker image will be used.</p>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Marker images path</th>
        <td>
          <input type="text" name="osm_cats_marker_images_path" value="<?php echo get_option('osm_cats_marker_images_path'); ?>" />
          <small>The absolute path to your marker images folder. If the path is correct you can see all your marker images below after saving.</small>
          <?php
          if (get_option('osm_cats_marker_images_path') && $handle = opendir($_SERVER['DOCUMENT_ROOT'].get_option('osm_cats_marker_images_path'))) {
            echo '<p>';
            while (false !== ($entry = readdir($handle))) {
              if ($entry != "." && $entry != "..") {
                if ($entry == 'marker.png') {
                  echo "<img src='".get_option('osm_cats_marker_images_path')."/".$entry."' alt='$entry' /> Marker for all categories.<br />";
                } elseif (strpos($entry,'marker') === 0) {
                  echo "<img src='".get_option('osm_cats_marker_images_path')."/".$entry."' alt='$entry' /> Marker for category with ID ".str_replace('marker_','',str_replace('.png','',$entry)).".<br />";
                }
              }
            }
            echo '</p>';
            closedir($handle);
          }
          ?>
        </td>
      </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

  </form>
  <!-- external scripts used to be here -->
  </div>
  <?php
}

function osm_cats_code( $atts ){
  //
  $message = '';


  // Get option for map width, if not set switch to 100% by default
  $map_width_check = get_option('osm_cats_map_width');
  $map_width = ($map_width_check)?$map_width_check:'100%';

  // Get option for map height, if not set switch to 300px by default
  $map_height_check = get_option('osm_cats_map_height');
  $map_height = ($map_height_check)?$map_height_check:'300px';

  // Get option for map center
  $map_center = get_option('osm_cats_center_lat').','.get_option('osm_cats_center_lon');

  // Get option for map zoom level, if not set switch to 12 by default
  $zoom_level_check = get_option('osm_cats_zoom_level');
  $zoom_level = ($zoom_level_check)?$zoom_level_check:12;
  
  // Get options for disable zoom wheel
  $disable_zoom_wheel = get_option('osm_cats_disable_zoom_wheel');

  // Get exluded categories
  $exclude_cats = get_option('osm_cats_exclude_cats');
  $include_cats = get_option('osm_cats_include_cats');

  // Get option for article latlng custom field, if not set switch to latlng by default
  $latlng_custom_field_check = get_option('osm_cats_marker_custom_field');
  $latlng_custom_field = ($latlng_custom_field_check)?$latlng_custom_field_check:'latlng';

  
  // Get options for marker popup
  $show_thumbnail = get_option('osm_cats_marker_show_thumbnail');
  $show_excerpt = get_option('osm_cats_marker_show_excerpt');
  
  // Get option for marker image path
  $marker_image_path = get_option('osm_cats_marker_images_path');
  
  // If no center is defined in the settings set center to 0,0 and zoom to 0, echo info message
  if($map_center == ',') {
    $map_center = '0,0';
    $zoom_level = 0;
    $message = 'Please define the center of your map on the <a href="/wp-admin/options-general.php?page=osm_cats_plugin">plugin settings page</a>.';
  }

  // Get categories for map layers
  $args = array(
    'exclude' => $exclude_cats,
  );
  $categories=get_categories($args);
  $tags = get_tags($args);
  
  // Get posts for markers
  $args = array('posts_per_page' => -1);
  if( $include_cats ) $args['category__in'] = explode(',',$include_cats);
  if( $exclude_cats ) $args['category__not_in'] = explode(',',$exclude_cats);
  query_posts($args);
  
  // The markup starts here
  ?>



  <div id="map" style="height: <?php echo $map_height; ?>; width: <?php echo $map_width; ?>;">
    <div style="position: absolute; bottom: 0; left: 10px; z-index: 10000;">
        <form method="post" name="geojson_exporter_form" onclick="download(geoJSONData)">
            <p class="submit"><input type="submit" name="Submit" value="Export to GeoJSON" /></p>
        </form>
    </div>
  </div>



  <?php echo ($message)?"<p>$message</p>":""; ?>

  <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
  <script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
  <script src='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.js'></script>
  <link href='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.css' rel='stylesheet' />

  <script>
    var map;
    var layer, markers, tags, size, offset, icon;
    var currentPopup;
    var zoom;
    var center;
    var layer_control, tag_control;
    var baseLayer;
    var geoJSONData = [];
    
    
    // Initalize function for the OSM map
    function init(){
      map = L.map('map');
      
      // Add an OpenStreetMap tile layer
      baseLayer = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
      })
        .addTo(map);

      // Create box to change overlays
      layer_control = L.control.layers(null, null, {
        collapsed: false
      })
        .addTo(map);

      // Create box to change overlays
      tag_control = L.control.layers(null, null, {
        collapsed: true
      })
        .addTo(map);

      <?php
      foreach($categories as $category) {
        // Create a layer for every category
        echo "markers_$category->cat_ID = L.layerGroup();";
        // Add layers to map
        echo "markers_$category->cat_ID.addTo(map);";
        // Add layers to directory (control.layers)
        echo "layer_control.addOverlay(markers_$category->cat_ID, '$category->cat_name');";
      };
      ?>
      
      <?php
      foreach($tags as $tag) {
          echo "tags_$tag->term_id = L.layerGroup();";
          // Add layers to map
          echo "tags_$tag->term_id.addTo(map);";
          // Add layers to directory (control.layers)
          echo "tag_control.addOverlay(tags_$tag->term_id, '$tag->name');";
        };
      
      ?>

      <?php
      // Disable mouse zoom wheel if checked
      if ($disable_zoom_wheel) {
        echo "map.scrollWheelZoom.disable();" ;
      }
      ?>
      
      // Set map center and zoom
      center = L.latLng( <?php echo $map_center; ?> );    
      map.setView(center, <?php echo $zoom_level; ?>);

      addMarkers();
    }
    
    function addMarkers() {
      var ll, layer, popupContentHTML, parsedContent, marker_icon;
      
      <?php
      // Add a marker for every post
      if (have_posts()) {
        while (have_posts()) {
          $custom_icon = false;
          the_post();

          if(get_field('coordinates')) {
            $values = get_field('coordinates');
            $lat = $values['lat'];
            $lng = $values['lng'];
            $latlng_value = "[".$lat.",".$lng."]";
          } else if(get_post_meta(get_the_ID(), $latlng_custom_field, true)) {
            $latlng_value = '['.get_post_meta(get_the_ID(), $latlng_custom_field, true).']';
          } else {
            $latlng_value = '[,]';
          }


          if($latlng_value != '[,]') {
            echo "ll = L.latLng($latlng_value);";

            $show_thumbnail_markup = ($show_thumbnail)?"<a href=\'".get_permalink()."\'>".get_the_post_thumbnail(get_the_ID(),'thumbnail')."</a>":"";
            echo "var title = '".get_the_title()."';";          
            $show_title_markup = "<a href=\'".get_permalink()."\'><h3>".get_the_title()."</h3></a>";
            // Parse the excerpt for invalid characters !important
            $find_text = array("'",'<br />',' />','&',';');
            $replace_text = array("\'",'','>','','');
            $post_excerpt = str_replace($find_text,$replace_text,get_the_excerpt());
            $show_excerpt_markup = ($show_excerpt)?"<p><small>".$post_excerpt."</small></p>":"";
            
            echo "popupContentHTML = '".$show_thumbnail_markup.$show_title_markup.$show_excerpt_markup."';";

            $category = get_the_category();

            $category_name = $category[0]->cat_name; 
            
            echo "layer = markers_".$category[0]->term_id.";";

            echo "var new_marker = addMarker(ll, popupContentHTML, layer, title, '".$category_name."');";
            
            $posttags = wp_get_post_tags( get_the_ID() );
            if ($posttags) {
              foreach($posttags as $tag) {
                echo "tags_$tag->term_id.addLayer(new_marker);";
              }
            }
            
            $image_path = $_SERVER['DOCUMENT_ROOT'].$marker_image_path."/marker_".$category[0]->term_id.".png";
            if(file_exists($image_path)) {
              list($width, $height)= getimagesize($image_path); 
              echo "marker_icon = L.icon({
                  iconUrl: '".$marker_image_path."/marker_".$category[0]->term_id.".png',
                  iconSize: [".$width.", ".$height."],
                  iconAnchor: [0, 0],
                  popupAnchor: [15, 5]
              });";
              $custom_icon = true;
            } else {
              $image_path = $_SERVER['DOCUMENT_ROOT'].$marker_image_path."/marker.png";
              if(file_exists($image_path)) {
                list($width, $height)= getimagesize($image_path); 
                echo "marker_icon = L.icon({
                    iconUrl: '".$marker_image_path."/marker.png',
                    iconSize: [".$width.", ".$height."],
                    iconAnchor: [9, 21],
                    popupAnchor:  [0, -".$height."]
                });";
                $custom_icon = true;
              }
            }

            if($custom_icon) {
              echo "new_marker.setIcon(marker_icon);";
            };

            // echo ($custom_icon)?"addMarker(ll, popupContentHTML, layer, marker_icon);":"addMarker(ll, popupContentHTML, layer, false);";
          }
        }
      }
      ?>
    }
    
    function addMarker(ll, popupContentHTML, layer, title, category) {

      var marker = L.marker(ll);

      if(popupContentHTML.search('iframe') > 0) {
        var popup = L.popup({
          maxWidth: 275,
          minWidth: 275
        });
        popup.setContent(popupContentHTML);
        marker.bindPopup(popup);
      } else {
        marker.bindPopup(popupContentHTML);
      }

      layer.addLayer(marker);

      makeJSON(marker, title, category, popupContentHTML);

      return marker;
    }

    function makeJSON(marker, title, category, content){
      var markerJSON = marker.toGeoJSON();
      markerJSON["title"] = title;
      markerJSON["category"] = category;
      markerJSON["description"] = content;
      geoJSONData.push(markerJSON);
    }
    
    function geoJSON_export(){
              // A name with a time stamp, to avoid duplicate filenames
        <?php
        $filename = "geojson-$ts.csv";

        // Tells the browser to expect a CSV file and bring up the
        // save dialog in the browser
        header( 'Content-Type: text' );
        header( 'Content-Disposition: attachment;filename='.$filename);

        // This opens up the output buffer as a "file"
        $fp = fopen('php://output', 'w');


        // Then, write every record to the output buffer in CSV format
        //foreach ($result as $data) {
          //  fputcsv($fp, $data);
        //}
        // Close the output buffer (Like you would a file)
        fclose($fp);
        ?>

    }

    function download(text) {
      text = JSON.stringify(text, null, 1);
      text = text.substring(1, text.length-1);
      var pom = document.createElement('a');
      pom.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
      pom.setAttribute('download', 'hello.txt');
      pom.click();
    }

    // Init call
    init();
  </script>
  <?php
  wp_reset_query();
}

add_shortcode( 'osm-cats', 'osm_cats_code' );
?>
