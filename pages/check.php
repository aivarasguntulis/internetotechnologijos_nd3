<?php
// MongoDB prisijungimo duomenys
$host = '92.62.143.23';
$port = 27017;
$dbname = 'nd3projektas';
$user = 'nd3user';
$pass = 'nd3pass';

echo "<h2>MongoDB prisijungimo tikrinimas</h2>";

try {
    // Naudojam SCRAM-SHA-1 autentifikavimą (be SSL)
    $uri = "mongodb://nd3user:nd3pass@92.62.143.23:27017/?authMechanism=SCRAM-SHA-1&authSource=nd3projektas";
    $manager = new MongoDB\Driver\Manager($uri);

    // Tikrinam ar galim perskaityti kolekcijų sąrašą
    $command = new MongoDB\Driver\Command(["listCollections" => 1]);
    $manager->executeCommand($dbname, $command);

    echo "<h3 style='color:green'>✅ Prisijungimas prie MongoDB sėkmingas!</h3>";
} catch (Throwable $e) {
    echo "<h3 style='color:red'>❌ Prisijungti prie MongoDB nepavyko: " . $e->getMessage() . "</h3>";
}

echo '<br><a href="register.html">Grįžti į registraciją</a>';
?>
