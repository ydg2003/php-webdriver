<?php

// An example of using php-webdriver.
// Do not forget to run composer install before. You must also have Selenium server started and listening on port 4444.

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once 'vendor/autoload.php';

// This is where Selenium, Chromedriver and Geckodriver 4 listens by default. For Selenium 2/3, use http://localhost:4444/wd/hub
$host = 'http://localhost:4444/';

$capabilities = DesiredCapabilities::chrome();

$driver = RemoteWebDriver::create($host, $capabilities);

// navigate to Selenium page on my Web project 'Event Management System'
$driver->get('http://localhost/PHP-UTPCollege-Projects/LogIn(2)/');

// write 'PHP' in the search box
$driver->findElement(WebDriverBy::name('user_name')) // find search input element
    ->sendKeys('PHP') // fill the search box
    ->submit(); // submit the whole form

// print URL of current page to output
echo "The current URL is '" . $driver->getCurrentURL() . "'\n";