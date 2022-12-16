<?php
    require_once realpath(__DIR__.'/..')."/vendor/autoload.php";
    require_once __DIR__."/html_tag_helpers.php";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Tubes Web Semantik</title>

  <link href="https://getbootstrap.com/docs/3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style type="text/css" media="all">
    #map
    {
        border: 1px gray solid;
        float: right;
        margin: 0 0 20px 20px;
    }
    th { text-align: right }
    td { padding: 5px; }

    #mapid { width:100%; height: 324px; }
  </style>

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
   integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
   crossorigin=""/>

   <!-- Make sure you put this AFTER Leaflet's CSS -->
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
   integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
   crossorigin=""></script>

</head>
<body style="background-color: #3e4444">

<?php
    // pastikan $uri_rdf sesuai dengan setting di komputer Anda
    $uri_rdf = 'http://localhost/semantiktubes2/slash.rdf';
    $data = \EasyRdf\Graph::newAndLoad($uri_rdf);
    $doc = $data->primaryTopic();

    // ambil data dbpedia Joe Satriani dari satriani_prj.rdf
    // BACA RDF
    $slash_uri = '';
    foreach ($doc->all('owl:sameAs') as $akun) {
        $slash_uri = $akun->get('foaf:homepage');
        break;
    }

    /*

    *** CONTOH QUERY: uji coba query dapat dilakukan via https://yasgui.triply.cc/ ***

    PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
    PREFIX foaf: <http://xmlns.com/foaf/0.1/>
    PREFIX dbp: <http://dbpedia.org/property/>
    PREFIX dbo: <http://dbpedia.org/ontology/>
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
    SELECT distinct * WHERE {
         <http://dbpedia.org/resource/Joe_Satriani> dbo:birthPlace ?tempat_lahir ;
           rdfs:comment ?info ;
           dbp:instrument ?instrumen ;
             foaf:isPrimaryTopicOf ?wiki .
         ?tempat_lahir rdfs:label ?tempat_lahir_label ;
             geo:lat ?lat ;
             geo:long ?long .
         ?album dbp:artist <http://dbpedia.org/resource/Joe_Satriani> ;
             rdfs:label ?album_label .
        OPTIONAL {?album dbp:released ?rilis_album .}
        FILTER (lang(?info) = "en" && lang(?tempat_lahir_label) = "en" && lang(?album_label) = "en")
    }
    ORDER BY DESC (?rilis_album)

    *** END QUERY ***

    */

    // BACA SPARQL

    // inisialisasi namespace untuk query rdf
    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
    \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
    \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
    \EasyRdf\RdfNamespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');

    // set sparql endpoint
    $sparql_endpoint = 'https://dbpedia.org/sparql';
    $sparql = new \EasyRdf\Sparql\Client($sparql_endpoint);

    $sparql_query = '
      SELECT distinct * WHERE {
           <' . $slash_uri . '> dbo:birthPlace ?tempat_lahir ;
               rdfs:comment ?info ;
               dbp:yearsActive ?debut ;
               foaf:isPrimaryTopicOf ?wiki .
           ?tempat_lahir rdfs:label ?tempat_lahir_label ;
               geo:lat ?lat ;
               geo:long ?long .
           ?album dbo:artist <' . $slash_uri . '> ;
               rdfs:label ?album_label .
          OPTIONAL {?album dbp:released ?rilis_album .}
          FILTER (lang(?info) = "en" && lang(?tempat_lahir_label) = "en" && lang(?album_label) = "en")
      }
      ORDER BY DESC (?rilis_album)
    ';
    // Dibawah rdfs:comment ada dbp:instrumen

    $result = $sparql->query($sparql_query);

    // ambil detail joe dari $result sparql
    $detail = [];
    foreach ($result as $row) {
      $detail = [
        'tempat_lahir'=>$row->tempat_lahir_label,
        'debut'=>$row->debut,
        'info'=>$row->info,
        'lat'=> $row->lat,
        'long'=> $row->long,
        'wiki'=> $row->wiki,
      ];

      break;
    }

?>

<div class="container">
      <div class="header clearfix">

        <h3 class="text-muted" style="color: white" >Tubes Web Semantik</h3>
      </div>

      <div class="jumbotron">
        <h1><?= $doc->get('foaf:name') ?></h1>
        <p class="lead"><?= $detail['info']; ?></p>
      </div>

      <div class="row">
        <div class="col-lg-4">
          <?php
            /* foto joe satriani terletak di laman wikipedia (hasil sparql) yang dapat diekstrak menggunakan ogp
               laman wiki: https://en.wikipedia.org/wiki/Joe_Satriani */

            // BACA OGP

            \EasyRdf\RdfNamespace::setDefault('og');

            $wiki = \EasyRdf\Graph::newAndLoad($detail['wiki']);
            $foto_url = $wiki->image;

          ?>

          <img src="<?= $foto_url ?>" width="100%"/>
        </div>
        <div class="col-lg-8">
          <h4 style="color: white">Nama Depan: <?= $doc->get('foaf:givenName') ?></h4>
          <h4 style="color: white">Nama Belakang: <?= $doc->get('foaf:familyName') ?></h4>
          <h4 style="color: white">Debut: <?= $detail['debut'] ?></h4>
          <br>
          <h4 style="color: white">Tempat Lahir: <?= $detail['tempat_lahir'] ?></h4>

          <?php
          print "<div id='mapid'></div>";
          $map_script = "var mymap = L.map('mapid').setView([" . $detail['lat'] . ", " . $detail['long'] . "], 13);
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
                maxZoom: 18,
                attribution: 'Map data &copy; <a href=\"https://www.openstreetmap.org/\">OpenStreetMap</a> contributors, ' +
                          '<a href=\"https://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>, ' +
                             'Imagery © <a href=\"https://www.mapbox.com/\">Mapbox</a>',
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1
               }).addTo(mymap);

               L.marker([" . $detail['lat'] . ", " . $detail['long'] . "]).addTo(mymap)
               .bindPopup(\"<b>" . $detail['tempat_lahir'] . "</b>\").openPopup();";

          print "<script>" . $map_script . "</script>";

          ?>
        </div>

      </div>
      <br>

      <h2 style="color: white">DISCOGRAPHY TIMELINE</h2>

      <?php
          // ambil data album
          $album_timeline = [];
          foreach ($result as $row) {
              // jika rilis_album tidak memiliki isi dan kurang dari 8 karakter, maka tidak dimasukkan pada timeline
              if ($row->rilis_album != '' && strlen($row->rilis_album) >= 8) {
                  $tmp['album'] = $row->album_label;
                  $tmp['tahun'] = date("Y", strtotime($row->rilis_album));
                  $tmp['bulan'] = date("m", strtotime($row->rilis_album));
                  $tmp['hari'] = date("d", strtotime($row->rilis_album));
                  $album_timeline[] = $tmp;
              }
          }
      ?> 

      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
      <div id="timeline" style="height: 480px;"></div>

      <script>
      google.charts.load('current', {'packages':['timeline']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var container = document.getElementById('timeline');
        var chart = new google.visualization.Timeline(container);
        var dataTable = new google.visualization.DataTable();

        dataTable.addColumn({ type: 'string', id: 'No' });
        dataTable.addColumn({ type: 'string', id: 'Name' });
        dataTable.addColumn({ type: 'date', id: 'Start' });
        dataTable.addColumn({ type: 'date', id: 'End' });
        dataTable.addRows([
          <?php
          $j = count($album_timeline);
          for ($i=0;$i<$j;$i++) {
            if ($i+1 >= $j) {
              ?>
                [ "<?= $i+1 ?>", "<?= $album_timeline[$i]['album'] ?>", new Date(<?= $album_timeline[$i]['tahun'] ?>, <?= $album_timeline[$i]['bulan'] ?>, <?= $album_timeline[$i]['hari'] ?>), new Date(<?= $album_timeline[$i]['tahun'] ?>, <?= $album_timeline[$i]['bulan'] ?>, <?= $album_timeline[$i]['hari'] ?>) ],
              <?php
            } else {
              ?>
                [ "<?= $i+1 ?>", "<?= $album_timeline[$i]['album'] ?>", new Date(<?= $album_timeline[$i+1]['tahun'] ?>, <?= $album_timeline[$i+1]['bulan'] ?>, <?= $album_timeline[$i+1]['hari'] ?>), new Date(<?= $album_timeline[$i]['tahun'] ?>, <?= $album_timeline[$i]['bulan'] ?>, <?= $album_timeline[$i]['hari'] ?>) ],
              <?php
            }
          }
          ?>
			]);

        chart.draw(dataTable);
      }
      </script>



      <br>
      <h2 style="color: white">DISCOGRAPHY DETAIL</h2>
      <div class="row" style="color: white">
        <div class="col-lg-12 ">

          <?php
              // ambil data album
              foreach ($result as $row) {
                  echo '<h4>' . $row->album_label . '</h4>';
                  echo '<p>Tanggal Rilis: ';
                  echo $row->rilis_album != '' ? $row->rilis_album : '<em>data tidak tersedia</em>';
                  echo '</p>';
                  echo '<hr>';
              }
          ?>

        </div>
      </div>

      <br>
      <h2 style="color: white">PROJECT</h2>
      <div class="row" style="color: white">
        <div class="col-lg-12">
      <?php
        // BACA OGP

        \EasyRdf\RdfNamespace::setDefault('og');

        $project_url = '';
        foreach ($doc->all('foaf:pastProject') as $akun) {
            $project_url = $akun->get('foaf:homepage');

            $ogp = \EasyRdf\Graph::newAndLoad($project_url);

            ?>
            <h4><?= $ogp->title ?></h4>
            <p><?= $ogp->description; ?></p>
            <p>Sumber: <a href="<?= $ogp->url ?>" target="_blank"><?= $ogp->site_name ?></a></p>

            <?php
        }
        ?>

        </div>
      </div>

      <hr>
      <br>
      <footer class="footer" style="color: white; text-align: right">
        <p>&copy; <?= date("Y") ?> Universitas Sumatera Utara.</p>
      </footer>

</div> <!-- /container -->

</body>
</html>
