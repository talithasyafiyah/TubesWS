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
    \EasyRdf\RdfNamespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    \EasyRdf\RdfNamespace::setDefault('og');

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

    // pastikan $uri_rdf sesuai dengan setting di komputer Anda
    $uri_rdf = 'http://localhost/tubesWS/tes.rdf';
    $data = \EasyRdf\Graph::newAndLoad($uri_rdf);
    $doc = $data->primaryTopic();

    // ambil data dbpedia Joe Satriani dari satriani_prj.rdf
    // BACA RDF
    $slash_uri = '';
    foreach ($doc->all('owl:sameAs') as $akun) {
        $slash_uri = $akun->get('foaf:homepage');
        break;
    }

    // BACA SPARQL

    // set sparql endpoint
    $sparql_endpoint = 'https://dbpedia.org/sparql';
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
    // Dibawah rdfs:comment ada dbp:instrumen

    $result = $sparql->query($sparql_query);

    // ambil detail joe dari $result sparql
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
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body id="page-top">
        <header class="masthead">
            <div class="container px-4 px-lg-5 d-flex h-100 align-items-center justify-content-center">
                <div class="d-flex justify-content-center">
                    <div class="text-center">
                        <h1 class="mx-auto my-0 text-uppercase">Civic</h1>
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
                        <h2 class="text-white mb-4">Civic</h2>
                        <p class="text-white-50">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Totam, dolor, vel tempora illum, iste expedita ad quibusdam laborum sit soluta aut dolore eveniet inventore vitae. Perferendis eaque voluptates atque libero?
                        </p>
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
                    <div class="col-xl-8 col-lg-7"><img class="img-fluid mb-3 mb-lg-0" src="assets/img/bg-masthead.jpg" alt="..." /></div>
                    <div class="col-xl-4 col-lg-5">
                        <div class="featured-text text-center text-lg-left">
                            <h4>Shoreline</h4>
                            <p class="text-black-50 mb-0">Grayscale is open source and MIT licensed. This means you can use it for any project - even commercial projects! Download it, customize it, and publish your website!</p>
                             <div class="col-lg-12">
      <?php
        // BACA OGP

        \EasyRdf\RdfNamespace::setDefault('og');

        $project_url = '';
        foreach ($doc->all('owl:extra') as $akun) {
            $project_url = $akun->get('foaf:homepage');

            $ogp = \EasyRdf\Graph::newAndLoad($project_url);

            ?>
            <h4><?= $ogp->title ?></h4>
            <p><?= $ogp->description; ?></p>
            <p>Sumber: <a href="<?= $ogp->url ?>" target="_blank"><?= $ogp->site_name ?></a></p>
             <img src="<?= $ogp->image ?>" width="100%"/>
            <?php
         
        }
        ?>

        </div>
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
                                    <h4 class="text-white">Map</h4>
                                    <p class="mb-0 text-white-50">An example of where you can put an image of a project, or anything else, along with a description.</p>
                                    <hr class="d-none d-lg-block mb-0 ms-0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Project Two Row-->
                <div class="row gx-0 justify-content-center">
                    <div class="col-lg-6"><img class="img-fluid" src="assets/img/demo-image-02.jpg" alt="..." /></div>
                    <div class="col-lg-6 order-lg-first">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-right">
                                    <h4 class="text-white">Chart</h4>
                                    <p class="mb-0 text-white-50">Another example of a project with its respective description. These sections work well responsively as well, try this theme on a small screen!</p>
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
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    </body>
</html>
