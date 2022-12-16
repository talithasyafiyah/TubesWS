<?php

use EasyRdf\RdfNamespace;
    require 'vendor/autoload.php';

    \EasyRdf\RdfNamespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('owl', 'http://www.w3.org/2002/07/owl#');
    \EasyRdf\RdfNamespace::set('dc', 'http://purl.org/dc/terms/');
    \EasyRdf\RdfNamespace::set('car', 'http://example.org/schema/car');
    \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
    \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
    \EasyRdf\RdfNamespace::set('sale', 'http://example.org/schema/sale');
    \EasyRdf\RdfNamespace::setDefault('og');

    $sparql_jena = new \EasyRdf\Sparql\Client('http://localhost:3030/civic/sparql');

    $sparql_query = '
    SELECT DISTINCT ?label ?comment ?name ?manufacturer ?designer
    ?fProduction ?assembly ?longitude ?latitude ?year18 ?year19 ?year20 ?year21 ?year22
    WHERE {?m rdfs:label ?label;
              rdfs:comment ?comment;
              foaf:name ?name;
              dbo:manufacturer ?manufacturer;
              dbp:designer ?designer;
              dbp:longitude ?longitude;
              dbp:latitude ?latitude;
              dbo:productionStartYear ?fProduction;
              dbp:assembly ?assembly;
              sale:year18 ?year18;
              sale:year19 ?year19;
              sale:year20 ?year20;
              sale:year21 ?year21;
              sale:year22 ?year22. }';

    // $sparql_query1 = '
    // SELECT DISTINCT ?year18 ?year19 ?year20 ?year21 ?year22
    // WHERE {?m sale:year18 ?year18;
    //           sale:year19 ?year19;
    //           sale:year20 ?year20;
    //           sale:year21 ?year21;
    //           sale:year22 ?year22. }';

    // $sparql_query1 = '
    // SELECT ?m ?abstract
    // WHERE {?m dbo:abstract ?abstract. }';
    
    $result = $sparql_jena->query($sparql_query);
    // $result1 = $sparql_jena->query($sparql_query1);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Civic</title>

        <!-- ############### Leaflet Map ################ -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>

        <link rel="icon" type="image/x-icon" href="assets/favicon1.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawStuff);

        <?php
            foreach ($result as $row) {
        ?>
        function drawStuff() {
            var data = new google.visualization.arrayToDataTable([
            ['Year', 'Sales'],
            ["2018", <?= $row->year18; ?>],
            ["2019", <?= $row->year19; ?>],
            ["2020", <?= $row->year20; ?>],
            ["2021", <?= $row->year21; ?>],
            ['2022', <?= $row->year22; ?>]
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
                0: { side: 'top', label: 'Amount'} // Top x-axis.
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
                            <?= $row->label; ?>
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
                            <?= $row->label; ?>
                        </h2>
                        <p class="text-white-50">
                            <?= $row->comment; ?>
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
                    <div class="col-xl-7 col-lg-6"><img class="img-fluid mb-3 mb-lg-0" src="assets/img/bg-masthead.jpg" alt="..."/></div>
                    <div class="col-xl-5 col-lg-6">
                        <div class="featured-text text-lg-left">
                            <!-- <h4>Shoreline</h4> -->
                            <p class="text-black-50 mb-2" style="font-size: xx-large;"><?= $row->name; ?></p>
                            <table>
                                <tr>
                                    <td>Designer</td>
                                    <td>:</td>
                                    <td><?= $row->designer; ?></td>
                                </tr>
                                <tr>
                                    <td>First Production</td>
                                    <td>:</td>
                                    <td>
                                        <?php
                                            $date = date_create($row->fProduction);
                                            echo date_format($date, "d F Y"); 
                                        ?></td>
                                </tr>
                                <tr>
                                    <td>Manufacturer</td>
                                    <td>:</td>
                                    <td><?= $row->manufacturer; ?></td>
                                </tr>
                                <tr>
                                    <td>First Assembly</td>
                                    <td>:</td>
                                    <td><?= $row->assembly; }?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Project One Row-->
                <div class="row gx-0 mb-5 mb-lg-0 justify-content-center">
                    <div class="col-lg-6"><img class="img-fluid" src="assets/img/demo-image-01.jpg" alt="..." /></div>
                    <div class="col-lg-6">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-left">
                                    <h6 class="text-white-50">First assembly in Suzuka, Mie, Japan.</h6>
                                    <hr class="d-none d-lg-block mb-0 ms-0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Project Two Row-->
                <div class="row gx-0 justify-content-center">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="leaflet-container" id="map" style="width: 610px; height: 400px;"></div>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-first">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-right">
                                    <h4 class="text-white">Chart</h4>
                                    <p class="mb-0 text-white-50">Honda Civic US Sales by Year</p>
                                    <p class="mb-0 text-white-50">2018 - 2022</p>
                                    <hr class="d-none d-lg-block mb-0 me-0" />
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
                                <h4 class="text-uppercase m-0">Talitha</h4>
                                <hr class="my-4 mx-auto" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Icha</h4>
                                <hr class="my-4 mx-auto" />

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Al Anhar</h4>
                                <hr class="my-4 mx-auto" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row gx-4 gx-lg-5 mt-5">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Erastus</h4>
                                <hr class="my-4 mx-auto" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Deni</h4>
                                <hr class="my-4 mx-auto" />

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4">
                            <div class="card-body text-center">
                                <i class="fas fa-user text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Joel</h4>
                                <hr class="my-4 mx-auto" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="social d-flex justify-content-center">
                    <a class="mx-2" href="#!"><i class="fab fa-twitter"></i></a>
                    <a class="mx-2" href="#!"><i class="fab fa-facebook-f"></i></a>
                    <a class="mx-2" href="#!"><i class="fab fa-github"></i></a>
                </div> -->
            </div>
        </section>
        <!-- Footer-->
        <footer class="footer bg-black small text-center text-white-50"><div class="container px-4 px-lg-5">Copyright &copy; Kelompok 7 2022</div></footer>

        <!-- ########## Leaflet Map Script ######### -->
        <?php 
            $longitude = $row->longitude;
            $latitude = $row->latitude;
            // $longitude = '34.700001';
            // $latitude = '136.500000';
            $map_script = "
            const map = L.map('map').setView([". $longitude . ", " . $latitude ."], 13);

            const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a>'
            }).addTo(map);
        
            const marker = L.marker([". $longitude . ", " . $latitude ."]).addTo(map)
                .bindPopup('<b>Honda Headquarters</b><br />2 Chome-1-1 Minamiaoyama, Minato City, Tokyo 107-0062, Japan.').openPopup();
            "
        ?>
    
<script>
    <?= $map_script; ?>
</script>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    </body>
</html>
