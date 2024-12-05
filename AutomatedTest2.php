<?php
// AutomatedTest2.php
namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

require_once 'vendor/autoload.php';

// Configuration
$host = 'http://localhost:4444/'; // Selenium server address
$signupURL = 'http://localhost/PHP-UTPCollege-Projects/Cinema%20Web%20App/reg.php'; // Sign-up page URL

// Create WebDriver instance
$driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());

/**
 * Perform sign-up action using provided data.
 *
 * @param RemoteWebDriver $driver
 * @param array $userData
 * @return void
 */
function signup(RemoteWebDriver $driver, array $userData): void
{
    global $signupURL;

    // Navigate to sign-up page
    $driver->get($signupURL);

    // Wait for the sign-up form to appear
    $driver->wait(10, 500)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('firstname'))
    );

    // Fill the form fields
    $driver->findElement(WebDriverBy::name('firstname'))->sendKeys($userData['firstname']);
    $driver->findElement(WebDriverBy::name('lastname'))->sendKeys($userData['lastname']);
    $driver->findElement(WebDriverBy::name('email'))->sendKeys($userData['email']);
    $driver->findElement(WebDriverBy::name('phone'))->sendKeys($userData['phone']);
    $driver->findElement(WebDriverBy::name('fecha_nacimiento'))->sendKeys($userData['fecha_nacimiento']);
    $driver->findElement(WebDriverBy::name('direccion'))->sendKeys($userData['direccion']);
    $driver->findElement(WebDriverBy::name('username'))->sendKeys($userData['username']);
    $driver->findElement(WebDriverBy::name('password'))->sendKeys($userData['password']);

    // Submit the form
    $driver->findElement(WebDriverBy::cssSelector('button.registerbtn'))->click();
}

/**
 * Validate if sign-up was successful.
 *
 * @param RemoteWebDriver $driver
 * @return bool
 */
function isSignupSuccessful(RemoteWebDriver $driver): bool
{
    try {
        // Wait for the redirection to the home page or a success message
        $driver->wait(10, 500)->until(
            WebDriverExpectedCondition::urlContains('home.html')
        );
        return true; // Sign-up successful
    } catch (\Exception $e) {
        return false; // Sign-up failed
    }
}

// Automated Test Execution
try {
    // Test case: Valid sign-up
    echo "Testing valid sign-up...\n";
    $validUserData = [
        'firstname' => 'Puff Diddy', // Required name
        'lastname' => 'Doe',
        'email' => 'puff.diddy@example.com',
        'phone' => '1234567890',
        'fecha_nacimiento' => '1990-01-01',
        'direccion' => '123 Test St, Test City',
        'username' => 'puffdiddy',
        'password' => 'securepassword123'
    ];
    signup($driver, $validUserData);

    if (isSignupSuccessful($driver)) {
        echo "Valid sign-up test passed.\n";
    } else {
        echo "Valid sign-up test failed.\n";
    }

    // Test case: Invalid sign-up (missing required fields)
    echo "Testing invalid sign-up...\n";
    $invalidUserData = [
        'firstname' => '',
        'lastname' => '',
        'email' => 'invalidemail', // Invalid email format
        'phone' => '',
        'fecha_nacimiento' => '',
        'direccion' => '',
        'username' => '',
        'password' => ''
    ];
    signup($driver, $invalidUserData);

    if (!isSignupSuccessful($driver)) {
        echo "Invalid sign-up test passed.\n";
    } else {
        echo "Invalid sign-up test failed.\n";
    }
} finally {
    // Close the browser session
    $driver->quit();
}