<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once 'vendor/autoload.php';

// Configuration
$host = 'http://localhost:4444/'; // Selenium server address
$appURL = 'http://localhost/PHP-UTPCollege-Projects/Cinema%20Web%20App/log.html'; // Application login page URL

// Create WebDriver instance
$driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());

/**
 * Perform login action using provided credentials.
 *
 * @param RemoteWebDriver $driver
 * @param string $username
 * @param string $password
 * @return void
 */
function login(RemoteWebDriver $driver, string $username, string $password): void
{
    // Navigate to login page
    $driver->get($GLOBALS['appURL']);

    // Wait for username field to appear
    $driver->wait(10, 500)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('username'))
    );

    // Fill username
    $driver->findElement(WebDriverBy::id('username'))->sendKeys($username);

    // Fill password
    $driver->findElement(WebDriverBy::id('password'))->sendKeys($password);

    // Submit the form
    $driver->findElement(WebDriverBy::cssSelector('input[type=submit]'))->click();
}

/**
 * Validate if login was successful.
 *
 * @param RemoteWebDriver $driver
 * @return bool
 */
function isLoginSuccessful(RemoteWebDriver $driver): bool
{
    try {
        // Wait for a post-login element (e.g., home button or admin menu)
        $driver->wait(10, 500)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('button[onclick*="home.html"]'))
        );
        return true; // Login successful
    } catch (\Exception $e) {
        return false; // Login failed
    }
}

// Automated Test Execution
try {
    // Test case: Valid credentials
    echo "Testing valid login...\n";
    login($driver, 'testuser', 'testpass');

    if (isLoginSuccessful($driver)) {
        echo "Valid login test passed.\n";
    } else {
        echo "Valid login test failed.\n";
    }

    // Add more test cases as needed
    // Test case: Invalid credentials
    echo "Testing invalid login...\n";
    login($driver, 'fake_user', 'testpass');

    if (!isLoginSuccessful($driver)) {
        echo "Invalid login test passed.\n";
    } else {
        echo "Invalid login test failed.\n";
    }

} finally {
    // Close the browser session
    $driver->quit();
}