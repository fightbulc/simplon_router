<?php

  require __DIR__ . '/../vendor/autoload.php';

  echo '<h1>Simplon/Router Test</h1>';
  echo '<p>In general routes should work with URL_Rewrite. Simplon/Routes does that but to present a simple test I implemented the possibility to switch to "QueryString" as route input.</p>';

  echo '<h3>Valid testing routes. Click them:</h3>';

  echo '<ul>';
    echo '<li><a href="test.php?/love/milk/honey">/love/milk/honey</a></li>';
    echo '<li><a href="test.php?/love/chocolate/6/summer">/love/chocolate/6/summer</a></li>';
    echo '<li><a href="test.php?/love/beer/heineken/weizen/bit/sagres">/love/beer/heineken/weizen/bit/sagres</a></li>';
    echo '<li><a href="test.php?/love/whatever/else/comes/around">/love/whatever/else/comes/around</a></li>';
  echo '</ul>';

  echo '<hr>';
  
  // ############################################

  echo '<h1>Callback Output</h1>';

  $milk = function ($with)
  {
    echo '<h3>Milk Route</h3>';
    echo "I love milk with: " . $with;
  };

  $chocolate = function ($amount, $season)
  {
    echo '<h3>Chocolate Route</h3>';
    echo "I love chocolate but had only " . $amount . " of them during " . $season;
  };

  $beer = function ()
  {
    $types = func_get_args();

    echo '<h3>Beer Route</h3>';
    echo "I love beer. Especially: <strong>" . implode(', ', $types) . "</strong>";
  };

  $default = function ()
  {
    echo '<h3>Default Route</h3>';
    echo "I love what's left in case nothing else matched";
  };

  // ############################################

  \Simplon\Router\Router::init()
    ->setUseQueryString(TRUE)
    ->addRoute('GET', '/love/milk/:alpha', $milk)
    ->addRoute('GET', '/love/chocolate/:num/:alpha', $chocolate)
    ->addRoute('GET', '/love/beer/:all', $beer)
    ->addRoute('GET', ':all', $default)
    ->run();
