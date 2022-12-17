<?php

use EasyRdf\RdfNamespace;
use LDAP\Result;

    require 'vendor/autoload.php';
    //----NameSpace-------
    \EasyRdf\RdfNamespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('owl', 'http://www.w3.org/2002/07/owl#');
    \EasyRdf\RdfNamespace::set('dc', 'http://purl.org/dc/terms/');
    \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
    \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
    \EasyRdf\RdfNamespace::set('car', 'http://example.org/schema/car/');
    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::set('sale', 'http://example.org/schema/sale/');
    \EasyRdf\RdfNamespace::setDefault('og');

    //---------Inisialisasi arah sparql untuk data dari dbpedia ---
    $sparql_endpoint = 'https://dbpedia.org/sparql';
    $sparql_dbpedia = new \EasyRdf\Sparql\Client($sparql_endpoint);
    //query dbpedia
    $query_dbpedia = "
        Select * WHERE {
            ?civic  rdfs:label 'Honda Civic'@en.
            ?civic dbo:abstract ?abstract.
            ?civic dbo:thumbnail ?image.
            FILTER( LANG (?abstract) = 'en')
        }";

    $result_dbpedia = $sparql_dbpedia->query($query_dbpedia);
    //menyimpan hasil query ke dalam array dbpedia
    $dbpedia = [];
    foreach ( $result_dbpedia as $row ) { 
        $dbpedia = [
        'abstract' => $row->abstract, //abstract civic
        'image' => $row->image,
        ];
        break;
    }
    
    //---------Inisialisasi arah sparql untuk map ---
    $query_dbpedia_map = "
        Select * WHERE {
            ?mie rdfs:label 'Mie Prefecture'@en.
            ?mie geo:long ?long.
            ?mie geo:lat ?lat.
        }";

    $result_map = $sparql_dbpedia->query($query_dbpedia_map);
    //menyimpan hasil query ke dalam array dbpedia
    $map = [];
    foreach ( $result_map as $field ) { 
        $map = [
        'long' => $field->long,
        'lat' => $field->lat,
        ];
        break;
    }

    //---------Mengkoneksikan dengan civic.rdf di folder local ---
    $uri_rdf = 'http://localhost/tubesWS/civic.rdf';
    $data = \EasyRdf\Graph::newAndLoad($uri_rdf);
    $doc = $data->primaryTopic();

    // ambil data dbpedia Honda civic dari civic.rdf
    $slash_uri = '';
    foreach ($doc->all('owl:sameAs') as $akun) {
        $slash_uri = $akun->get('foaf:homepage');
        break;
    }
    
    // set sparql endpoint
    // $sparql_endpoint = 'https://dbpedia.org/sparql';
    $sparql = new \EasyRdf\Sparql\Client($sparql_endpoint);

    $sparql_query = '
      SELECT distinct * WHERE {
           <' . $slash_uri . '> dbo:manufacturer ?manufacturer ;
               rdfs:comment ?info ;
               dbo:productionStartYear ?production ;
               foaf:isPrimaryTopicOf ?wiki .
           ?manufacturer rdfs:label ?manufacturer_label.
          FILTER (lang(?info) = "en" && lang(?manufacturer_label) = "en")
      }
    ';

    $result = $sparql->query($sparql_query);

    // ambil detail civic dari $result sparql
    $detail = [];
    foreach ($result as $row) {
      $detail = [
        'manufacturer'=>$row->manufacturer_label,
        'production'=>$row->production,
        'info'=>$row->info,
        'wiki'=> $row->wiki,
      ];

      break;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Civic</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon1.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
        <!-- leaflet JS --->
         <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
                integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
                crossorigin=""/>
        <!-- Make sure you put this AFTER Leaflet's CSS -->
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
            integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
            crossorigin="">
        </script>
        <!--custom css--->
        <link href="css/tes.css" rel="stylesheet" /> 
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawStuff);

        // Chart
        function drawStuff() {
            var data = new google.visualization.arrayToDataTable([
            ['Year', 'Sales'],
            ["2018",<?= $doc->get('sale:year18') ?>],
            ["2019",<?= $doc->get('sale:year19') ?>],
            ["2020",<?= $doc->get('sale:year20') ?>],
            ["2021",<?= $doc->get('sale:year21') ?>],
            ['2022',<?= $doc->get('sale:year22') ?>]
            ]);

            var options = {
            title: 'Honda Civic Sales in US',
            width: 500,
            legend: { position: 'none' },
            chart: { title: 'Honda Civic Sales in US',
                    subtitle: 'by Year' },
            bars: 'horizontal', // Required for Material Bar Charts.
            axes: {
                x: {
                0: { side: 'top', label: 'Sales'} // Top x-axis.
                }
            },
            bar: { groupWidth: "90%" }
            };

            var chart = new google.charts.Bar(document.getElementById('top_x_div'));
            chart.draw(data, options);
        };
        </script>
    </head>

    <body id="page-top">
        <header class="masthead">
            <div class="container px-4 px-lg-5 d-flex h-100 align-items-center justify-content-center">
                <div class="d-flex justify-content-center">
                    <div class="text-center">
                        <h1 class="mx-auto my-0 text-uppercase">
                            <?= $doc->get('car:name') ?>
                        </h1>
                        <h2 class="text-white-50 mx-auto mt-2 mb-5">An elegant, cool, handsome car.</h2>
                        <a class="btn btn-primary" href="#about">Get Started</a>
                    </div>
                </div>
            </div>
        </header>
        <!-- About-->
        <section class="about-section text-center" id="about">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-lg-8">
                        <h2 class="text-white mb-4">
                            <?= $doc->get('car:name') ?>
                        </h2>
                        <p class="text-white-50">
                            <?= $dbpedia['abstract'];?>
                        </p>
                    </div>
                </div>
                <img class="img-fluid" src="assets/img/ipad-car.png" alt="..." />
            </div>
        </section>
        <!-- Projects-->
        <section class="projects-section bg-light" id="projects">
            <div class="container px-4 px-lg-5">
                <!-- Open Graph Protocol -->
                <div class="row gx-0 mb-4 mb-lg-5 align-items-center">
                    <!--Mengambil gambar dari wiki-->
                         <?php
                        \EasyRdf\RdfNamespace::setDefault('og');
                        $wiki = \EasyRdf\Graph::newAndLoad($detail['wiki']);
                        $foto_url = $wiki->image;
                        ?>

                        <div class="col-xl-7 col-lg-6"><img class="img-fluid mb-3 mb-lg-0"
                        src="<?= $foto_url ?>" width="800" alt="..."/>
                        </div>
                    <!----end---->
                    <div class="col-xl-5 col-lg-6">
                        <div class="featured-text text-lg-left">
                            <!-- <h4>Shoreline</h4> -->
                            <p class="text-black-50 mb-2" style="font-size: xx-large;">About <?= $doc->get('car:name') ?></p>
                            <table>
                                <tr>
                                    <td>Designer</td>
                                    <td>:</td>
                                    <td><?= $doc->get('car:designer') ?></td>
                                </tr>
                                <tr>
                                    <td>First Production</td>
                                    <td>:</td>
                                    <td><?= $doc->get('car:productionStartYear') ?></td>
                                </tr>
                                <tr>
                                    <td>Manufacturer</td>
                                    <td>:</td>
                                    <td><?= $doc->get('car:manufacturer') ?></td>
                                </tr>
                                <tr>
                                    <td>First Assembly</td>
                                    <td>:</td>
                                    <td><?= $doc->get('car:assembly') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!---The Reall OGP untuk mengambil title deskripsi dan gambar serta link dari website lain--->
                <div class="row gx-0 mb-4 mb-lg-5 align-items-center">           
                    <?php
                    \EasyRdf\RdfNamespace::setDefault('og');
                    $project_url = '';
                    foreach ($doc->all('car:extra') as $akun) {
                    $project_url = $akun->get('foaf:homepage');
                    $ogp = \EasyRdf\Graph::newAndLoad($project_url);
                    ?>

                    <!--content--->
                    <div class="col-xl-5 col-lg-6">
                        <div class="featured-text text-lg-left px-4">
                            <h4><?= $ogp->title ?></h4>
                            <p><?= $ogp->description; ?></p>
                            <p>Sumber: <a href="<?= $ogp->url ?>" target="_blank"><?= $ogp->site_name ?></a></p>
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-6">
                        <img src="<?= $ogp->image ?>" width="100%"/>
                    </div>
                    
                    <!---end--->

                    <?php } ?>
                </div>
            
                <!---end---->
                <!-- Project One Row-->
                <div class="row gx-0 mb-5 mb-lg-0 justify-content-center">
                    <div class="col-lg-6">
                        <!-- Map -->
                        <div id="map"></div>
                            <script>
                                var map = L.map('map').setView([<?= $map['lat']; ?>, <?= $map['long']; ?>], 13);

                                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
                                    attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
                                    id: 'mapbox/streets-v11', tileSize: 512, zoomOffset: -1
                                }).addTo(map);
                                L.marker([<?= $map['lat']; ?>, <?= $map['long']; ?>]).addTo(map)
                                .bindPopup('<b><?= $doc->get('car:assembly') ?>.')
                                .openPopup();
                            </script>
                    </div>
                    <div class="col-lg-6">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-left">
                                    <h6 class="text-white-50">First assembly in <?= $doc->get('car:assembly') ?>.</h6>
                                    <p class="mb-0 text-white-50"><?= $map['lat']; ?> - <?= $map['long']; ?></p>
                                    <hr class="d-none d-lg-block mb-0 ms-0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Project Two Row-->
                <div class="row gx-0 justify-content-center">
                    <div class="col-lg-6">
                        <div class="card mx-4 mt-4">
                            <div id="top_x_div" style="width: 500px; height: 400px;"></div>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-first">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-right">
                                    <p class="mb-0 text-white-50"><?= $doc->get('car:name') ?> US Sales by Year</p>
                                    <p class="mb-0 text-white-50">2018 - 2022</p>
                                    <hr class="d-none d-lg-block mb-0 me-0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           </div>
        </section>
        <!-- Members-->
        <section class="contact-section bg-black">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Talitha Syafiyah</h4>
                                <hr class="my-4 mx-auto" />
                                <h4 class="text-uppercase m-0">211402018</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Al Anhar Sufi</h4>
                                <hr class="my-4 mx-auto" />
                                <h4 class="text-uppercase m-0">211402045</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Icha Frabila</h4>
                                <hr class="my-4 mx-auto" />
                                <h4 class="text-uppercase m-0">211402012</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row gx-4 gx-lg-5 mt-5">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Erastus Keytaro Bangun</h4>
                                <hr class="my-4 mx-auto" />
                                <h4 class="text-uppercase m-0">211402042</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Deni Putra Sitanggang</h4>
                                <hr class="my-4 mx-auto" />
                                <h4 class="text-uppercase m-0">211402150</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Joel Tigor</h4>
                                <hr class="my-4 mx-auto" />
                                <h4 class="text-uppercase m-0">211402129</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer-->
        <footer class="footer bg-black small text-center text-white-50"><div class="container px-4 px-lg-5">Copyright &copy; Kelompok 7 2022</div></footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    </body>
</html>
