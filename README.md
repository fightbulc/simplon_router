<pre>
     _                 _                               _
 ___(_)_ __ ___  _ __ | | ___  _ __    _ __ ___  _   _| |_ ___ _ __
/ __| | '_ ` _ \| '_ \| |/ _ \| '_ \  | '__/ _ \| | | | __/ _ \ '__|
\__ \ | | | | | | |_) | | (_) | | | | | | | (_) | |_| | ||  __/ |
|___/_|_| |_| |_| .__/|_|\___/|_| |_| |_|  \___/ \__,_|\__\___|_|
                |_|
</pre>

# Simplon Router

A lightweight, quick and easy to understand router.

Current version: 0.5.2

-------------------------------------------------

## What it does

It hooks in to requests (GET|POST or both) and a watches a defined url pattern.
If the pattern matches it calls a closure function in order to process the route.

### Example

```php
require __DIR__ . '/../vendor/autoload.php';

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

(new Router())
  ->enableRoutingViaQueryString(TRUE)
  ->addRoute('GET', '/love/milk/:alpha', $milk)
  ->addRoute('GET', '/love/chocolate/:num/:alpha', $chocolate)
  ->addRoute('GET', '/love/beer/:all', $beer)
  ->addRoute('GET', ':all', $default)
  ->run();
```

-------------------------------------------------

## Clarification

Lets say we get a GET request on the url ```/love/milk/cookies```. In that case the following route
```addRoute('GET', '/love/milk/:alpha', $milk)``` would be triggered which prints in turn:

```html
<h3>Milk Route</h3>
I love milk with: cookies
```

That's the magic!

-------------------------------------------------

# License

Cirrus is freely distributable under the terms of the MIT license.

Copyright (c) 2014 Tino Ehrich ([opensource@efides.com](mailto:opensource@efides.com))

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/fightbulc/simplon_router/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
