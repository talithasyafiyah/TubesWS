<?php
    require 'vendor/autoload.php';

    \EasyRdf\RdfNamespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    \EasyRdf\RdfNamespace::set('owl', 'http://www.w3.org/2002/07/owl#');
    \EasyRdf\RdfNamespace::set('dc', 'http://purl.org/dc/terms/');
    \EasyRdf\RdfNamespace::set('car', 'http://example.org/schema/car');
    \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
    \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
    \EasyRdf\RdfNamespace::setDefault('og');

    $sparql_jena = new \EasyRdf\Sparql\Client('http://localhost:3030/civic/sparql');

    $sparql_query = '
    SELECT ?m ?name ?manufacturer ?abstract
    WHERE {?m foaf:name ?name;
              dbo:manufacturer ?manufacturer;
              dbo:abstract ?abstract. }';

    // $sparql_query1 = '
    // SELECT ?m ?abstract
    // WHERE {?m dbo:abstract ?abstract.}';
    
    $result = $sparql_jena->query($sparql_query);
    // $result1 = $sparql_jena->query($sparql_query1);

    foreach ($result as $row) {
        echo $row->name;
        echo '<br>';
        echo $row->manufacturer;
        echo '<br>';
        echo $row->abstract;
    }

    // foreach ($result1 as $row1) {
    //     echo $row1->abstract;
    // }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
          ['Opening Move', 'Percentage'],
          ["King's pawn (e4)", 44],
          ["Queen's pawn (d4)", 31],
          ["Knight to King 3 (Nf3)", 12],
          ["Queen's bishop pawn (c4)", 10],
          ['Other', 3]
        ]);

        var options = {
          title: 'Chess opening moves',
          width: 900,
          legend: { position: 'none' },
          chart: { title: 'Chess opening moves',
                   subtitle: 'popularity by percentage' },
          bars: 'horizontal', // Required for Material Bar Charts.
          axes: {
            x: {
              0: { side: 'top', label: 'Percentage'} // Top x-axis.
            }
          },
          bar: { groupWidth: "90%" }
        };

        var chart = new google.charts.Bar(document.getElementById('top_x_div'));
        chart.draw(data, options);
      };
    </script>
</head>
<body>
    <div id="top_x_div" style="width: 900px; height: 500px;"></div>
</body>
</html>