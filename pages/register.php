<?php
// MongoDB prisijungimo duomenys
$host = '92.62.143.23';
$port = 27017;
$dbname = 'nd3projektas';
$user = 'nd3user';
$pass = 'nd3pass';
$uri = "mongodb://$user:$pass@$host:$port/?authMechanism=SCRAM-SHA-1&authSource=$dbname";
$collection = 'users';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Registracija</title>
<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head><body style='background:#1a1a1a;color:white;padding:20px;'>";

try {
    $manager = new MongoDB\Driver\Manager($uri);
} catch (Throwable $e) {
    echo "<div class='alert alert-danger'>Nepavyko prisijungti prie DB: " . $e->getMessage() . "</div></body></html>";
    exit;
}

$msg = "";

// Trynimo funkcija
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->delete(['_id' => new MongoDB\BSON\ObjectId($id)]);
    try {
        $manager->executeBulkWrite("$dbname.$collection", $bulk);
        $msg = "<div class='alert alert-success mt-3'>Vartotojas ištrintas!</div>";
    } catch (Throwable $e) {
        $msg = "<div class='alert alert-danger mt-3'>Klaida trinant: " . $e->getMessage() . "</div>";
    }
}

// Redagavimo veiksmas (rodyti formą su reikšmėmis)
$editUser = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $filter = ['_id' => new MongoDB\BSON\ObjectId($id)];
    $query = new MongoDB\Driver\Query($filter);
    $rows = $manager->executeQuery("$dbname.$collection", $query);
    foreach ($rows as $user) {
        $editUser = $user;
        break;
    }
}

// Redaguoti POST
if (isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $vardas = $_POST['vardas'] ?? '';
    $pavarde = $_POST['pavarde'] ?? '';
    $elpastas = $_POST['elpastas'] ?? '';
    $slaptazodis = $_POST['slaptazodis'] ?? '';
    if ($vardas && $pavarde && $elpastas && $slaptazodis) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => [
                'vardas' => $vardas,
                'pavarde' => $pavarde,
                'email' => $elpastas,
                'slaptazodis' => $slaptazodis
            ]]
        );
        try {
            $manager->executeBulkWrite("$dbname.$collection", $bulk);
            $msg = "<div class='alert alert-success mt-3'>Vartotojo duomenys atnaujinti!</div>";
        } catch (Throwable $e) {
            $msg = "<div class='alert alert-danger mt-3'>Klaida redaguojant: " . $e->getMessage() . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning mt-3'>Užpildykite visus laukus!</div>";
    }
}

// Naujo registravimas (tik jeigu ne redaguojame)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_id'])) {
    $vardas = $_POST['vardas'] ?? '';
    $pavarde = $_POST['pavarde'] ?? '';
    $elpastas = $_POST['elpastas'] ?? '';
    $slaptazodis = $_POST['slaptazodis'] ?? '';

    if ($vardas && $pavarde && $elpastas && $slaptazodis) {
        $doc = [
            'vardas' => $vardas,
            'pavarde' => $pavarde,
            'email' => $elpastas,
            'slaptazodis' => $slaptazodis
        ];
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($doc);
        try {
            $manager->executeBulkWrite("$dbname.$collection", $bulk);
            $msg = "<div class='alert alert-success mt-3'>Vartotojas sėkmingai užregistruotas!</div>";
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), 'E11000')) {
                $msg = "<div class='alert alert-danger mt-3'>Toks el. paštas jau užregistruotas!</div>";
            } else {
                $msg = "<div class='alert alert-danger mt-3'>Klaida registruojant: " . $e->getMessage() . "</div>";
            }
        }
    } else {
        $msg = "<div class='alert alert-warning mt-3'>Prašome užpildyti visus laukus!</div>";
    }
}

// Forma: jei redaguojame, užpildyta su reikšmėmis; jei ne – tuščia.
?>
<div class="container" style="max-width:500px;">
    <h2 class="mt-3"><?php echo $editUser ? "Redaguoti vartotoją" : "Registracija"; ?></h2>
    <?php echo $msg; ?>
    <form action="register.php<?php if($editUser) echo '?edit='.$editUser->_id; ?>" method="post">
        <?php if ($editUser) echo '<input type="hidden" name="edit_id" value="'.$editUser->_id.'">'; ?>
        <input class="form-control my-2" type="text" name="vardas" placeholder="Vardas" required value="<?php echo htmlspecialchars($editUser->vardas ?? $_POST['vardas'] ?? '') ?>">
        <input class="form-control my-2" type="text" name="pavarde" placeholder="Pavardė" required value="<?php echo htmlspecialchars($editUser->pavarde ?? $_POST['pavarde'] ?? '') ?>">
        <input class="form-control my-2" type="email" name="elpastas" placeholder="El. paštas" required value="<?php echo htmlspecialchars($editUser->email ?? $_POST['elpastas'] ?? '') ?>">
        <input class="form-control my-2" type="password" name="slaptazodis" placeholder="Slaptažodis" required value="">
        <button type="submit" class="btn btn-success"><?php echo $editUser ? "Išsaugoti" : "Registruoti"; ?></button>
        <?php if ($editUser): ?>
            <a href="register.php" class="btn btn-secondary ms-2">Atšaukti</a>
        <?php endif; ?>
    </form>
</div>

<div class='container' style='max-width:900px;'>
    <h3 class='mt-5'>Vartotojų sąrašas</h3>
    <table class='table table-dark table-bordered'>
        <tr>
            <th>Vardas</th><th>Pavardė</th><th>El. paštas</th><th>Veiksmai</th>
        </tr>
<?php
$query = new MongoDB\Driver\Query([]);
try {
    $rows = $manager->executeQuery("$dbname.$collection", $query);
    foreach ($rows as $row) {
        echo "<tr>";
        echo "<td>".htmlspecialchars($row->vardas ?? '')."</td>";
        echo "<td>".htmlspecialchars($row->pavarde ?? '')."</td>";
        echo "<td>".htmlspecialchars($row->email ?? '')."</td>";
        echo "<td>
                <a class='btn btn-warning btn-sm' href='register.php?edit=".$row->_id."'>Redaguoti</a>
                <a class='btn btn-danger btn-sm' href='register.php?delete=".$row->_id."' onclick='return confirm(\"Ar tikrai norite ištrinti?\")'>Trinti</a>
              </td>";
        echo "</tr>";
    }
} catch (Throwable $e) {
    echo "<tr><td colspan='4'><div class='alert alert-danger'>Nepavyko gauti vartotojų: " . $e->getMessage() . "</div></td></tr>";
}
?>
    </table>
    <a href="register.html" class="btn btn-secondary mt-3">Grįžti į registraciją</a>
</div>
</body></html>
