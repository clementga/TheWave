<?php
session_start();
session_regenerate_id();
require("inc/checkauth.inc.php");
require("inc/connexiondb.inc.php");
require("inc/header.inc.php");

if (!isset($_GET['idal'])) {
    echo '<main class="container"><div class="alert alert-danger" role="alert">
            Identifiant d\'album manquant
            </div></main>';
    require("inc/footer.inc.php");
    die();
}
else {
    $requete = $db->prepare("SELECT * FROM album NATURAL JOIN groupe WHERE idal=:idal;");
    $requete->bindParam(':idal', $_GET['idal']);

    $requete->execute();
    $album = $requete->fetch();
    $requete->closeCursor();

    if ($album == FALSE) {
        echo '<main class="container"><div class="alert alert-danger" role="alert">
            Identifiant d\'album incorrect
            </div></main>';
        require("inc/footer.inc.php");
        die();
    }

    $morceaux = $db->prepare("SELECT idmo, titrem, duree, num FROM morceau NATURAL JOIN albumcontient WHERE idal=:idal ORDER BY num;");
    $morceaux->bindParam(':idal', $_GET['idal']);
    $morceaux->execute();
}
?>

<main class="container">
    <div class="row">
        <div class="col text-center">
            <h1><?php echo $album['titrea']; ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-9">
            <h3>Informations sur l'album</h3>
            <table class="table table-sm">
                <tr class="table-primary">
                    <td>Titre</td>
                    <td><?php echo $album['titrea']; ?></td>
                </tr>
                <tr>
                    <td>Groupe</td>
                    <td><?php echo $album['nomg']; ?></td>
                </tr>
                <tr>
                    <td>Genre</td>
                    <td><?php echo ucwords($album['genre']) ?></td>
                </tr>
                <tr>
                    <td>Date de parution</td>
                    <td><?php echo $album['dateparu']; ?></td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td><?php echo $album['desca']; ?></td>
                </tr>
            </table>
        </div>
        <div class="col-3">
            <h3>Couverture</h3>
            <?php
            if (!empty($album['couv'])) {
                echo '<img src="img/couv/' . $album['couv'] . '" class="img-fluid" alt="couverture">';
            }
            else {
                echo '<small class="text-muted">Couverture non disponible</small>';
            }
            ?>
        </div>
    </div>
    <div class="row">
        <h3>Morceaux</h3>
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Titre</th>
                    <th>Durée</th>
                </tr>
            </thead>
            <?php
            foreach($morceaux as $morceau) {
                $duree = explode(":", $morceau['duree']);
                echo '<tr><td>' . $morceau['num'] . '</td>';
                echo '<td><a href="morceau.php?idmo=' . $morceau['idmo'] . '">' . $morceau['titrem'] . '</a></td>';
                echo '<td>' . $duree[1] . ':' . $duree[2] . '</td>';
            }
            $morceaux->closeCursor();
            ?>
        </table>
    </div>
</main>

<?php
require("inc/footer.inc.php");
?>