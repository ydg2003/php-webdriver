<?php
namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

require_once 'vendor/autoload.php';

// Configuration
$host = 'http://localhost:4444/';
$loginURL = 'http://localhost/PHP-UTPCollege-Projects/Cinema%20Web%20App/log.html';
$buyTicketURL = 'http://localhost/PHP-UTPCollege-Projects/Cinema%20Web%20App/comprarboleto.php';

// Create WebDriver instance
$driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());

/**
 * Perform login action.
 *
 * @param RemoteWebDriver $driver
 * @param string $username
 * @param string $password
 * @return void
 */
function login(RemoteWebDriver $driver, string $username, string $password): void
{
    global $loginURL;

    // Navigate to login page
    $driver->get($loginURL);

    // Wait for login form
    $driver->wait(10, 500)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('username'))
    );

    // Fill in login credentials
    $driver->findElement(WebDriverBy::id('username'))->sendKeys($username);
    $driver->findElement(WebDriverBy::id('password'))->sendKeys($password);

    // Submit the form
    $driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'))->click();
}

/**
 * Buy a ticket.
 *
 * @param RemoteWebDriver $driver
 * @param array $ticketDetails
 * @return bool
 */
function buyTicket(RemoteWebDriver $driver, array $ticketDetails): bool
{
    global $buyTicketURL;

    // Navigate to Buy Ticket page
    $driver->get($buyTicketURL);

    // Wait for Buy Ticket form
    $driver->wait(10, 500)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('pelicula'))
    );

    // Select movie
    $driver->findElement(WebDriverBy::id('pelicula'))->sendKeys($ticketDetails['movie']);

    // Select branch
    $driver->findElement(WebDriverBy::id('sucursal'))->sendKeys($ticketDetails['branch']);

    // Select seat
    $driver->findElement(WebDriverBy::id('asiento'))->sendKeys($ticketDetails['seat']);

    // Submit the form
    $driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'))->click();

    // Check for success message
    try {
        $driver->wait(10, 500)->until(
            WebDriverExpectedCondition::textToBePresentInElement(
                WebDriverBy::cssSelector('div.success'),
                'Compra exitosa'
            )
        );
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

// Automated Test Execution
try {
    // Test case: Successful ticket purchase
    echo "Testing successful ticket purchase...\n";
    login($driver, 'johndoe', 'securepassword123'); // Replace with valid credentials

    $validTicketDetails = [
        'movie' => 'Avatar 2', // Replace with a valid movie name
        'branch' => 'Sucursal 1', // Replace with a valid branch name
        'seat' => 'A1' // Replace with a valid seat number
    ];

    if (buyTicket($driver, $validTicketDetails)) {
        echo "Successful ticket purchase test passed.\n";
    } else {
        echo "Successful ticket purchase test failed.\n";
    }

    // Test case: Failed ticket purchase (missing required details)
    echo "Testing failed ticket purchase...\n";
    login($driver, 'johndoe', 'securepassword123'); // Re-login for a new test

    $invalidTicketDetails = [
        'movie' => '',
        'branch' => '',
        'seat' => ''
    ];

    if (!buyTicket($driver, $invalidTicketDetails)) {
        echo "Failed ticket purchase test passed.\n";
    } else {
        echo "Failed ticket purchase test failed.\n";
    }
} finally {
    // Close the browser session
    $driver->quit();
}