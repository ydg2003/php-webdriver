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
$consultTicketURL = 'http://localhost/PHP-UTPCollege-Projects/Cinema%20Web%20App/infoboleto.php';

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
 * Consult ticket details.
 *
 * @param RemoteWebDriver $driver
 * @param string $ticketID
 * @return string
 */
function consultTicket(RemoteWebDriver $driver, string $ticketID): string
{
    global $consultTicketURL;

    // Navigate to Consult Ticket page
    $driver->get($consultTicketURL);

    // Wait for Consult Ticket form
    $driver->wait(10, 500)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('boleto'))
    );

    // Enter ticket ID and submit
    $driver->findElement(WebDriverBy::id('boleto'))->sendKeys($ticketID);
    $driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'))->click();

    // Wait for result message
    try {
        $driver->wait(10, 500)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('p'))
        );
        return $driver->findElement(WebDriverBy::cssSelector('p'))->getText();
    } catch (\Exception $e) {
        return "No result found.";
    }
}

// Automated Test Execution
try {
    // Test case: Successful ticket consult
    echo "Testing successful ticket consult...\n";
    login($driver, 'johndoe', 'securepassword123'); // Replace with valid credentials

    $validTicketID = '1'; // Replace with an actual valid ticket ID
    $result = consultTicket($driver, $validTicketID);

    if (strpos($result, 'ID de Boleto') !== false) {
        echo "Successful ticket consult test passed: $result\n";
    } else {
        echo "Successful ticket consult test failed: $result\n";
    }

    // Test case: Failed ticket consult (invalid ticket ID)
    echo "Testing failed ticket consult...\n";
    $invalidTicketID = '999999'; // Replace with an ID that doesn't exist
    $result = consultTicket($driver, $invalidTicketID);

    if (strpos($result, 'Boleto no encontrado') !== false) {
        echo "Failed ticket consult test passed: $result\n";
    } else {
        echo "Failed ticket consult test failed: $result\n";
    }
} finally {
    // Close the browser session
    $driver->quit();
}