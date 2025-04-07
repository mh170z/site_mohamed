<?php
//  Connexion à la base de données
include('session_start.php');
header('Content-Type: application/json');

// Récupère la requête en JSON
$request = json_decode(file_get_contents('php://input'), true);

// Vérification des données reçues
if (!isset($request['row'])) {
    echo json_encode(["error" => "Données manquantes"]);
    exit;
}

$row = $request['row'];

try {
    // Vérifier que le vacancier n'est pas déjà inscrit à cette activité
    $query0 = "SELECT count(*) AS n FROM INSCRIPTION WHERE USER=? AND CODEANIM=? AND DATEACT=?";
    $stmt0 = $conn->prepare($query0);
    if (!$stmt0)
        throw new Exception("Erreur de préparation de la requête: " . $conn->error);
    $stmt0->bind_param("sss", $row['USER'], $row['CODEANIM'], $row['DATEACT']);
    $stmt0->execute();
    $result0 = $stmt0->get_result();
    $row0 = $result0->fetch_assoc();
    if ($row0['n'] > 0) {
        echo json_encode(['error' => "Vacancier déjà inscrit à l'activité (" . $row['CODEANIM'] . "," . $row['DATEACT'] . ")"]);
        exit;
    }

    // Vérifier la période de séjour
    $query1 = "SELECT DATEDEBSEJOUR, DATEFINSEJOUR FROM COMPTE WHERE USER=?";
    $stmt1 = $conn->prepare($query1);
    if (!$stmt1)
        throw new Exception("Erreur de préparation de la requête: " . $conn->error);
    $stmt1->bind_param("s", $row['USER']);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    if ($result1->num_rows > 0) {
        $row1 = $result1->fetch_assoc();
        $dateDebSejour = $row1['DATEDEBSEJOUR'] ?? '1900-01-01';
        $dateFinSejour = $row1['DATEFINSEJOUR'] ?? '3000-01-01';

        if ($row['DATEACT'] < $dateDebSejour || $row['DATEACT'] > $dateFinSejour) {
            echo json_encode(["error" => "L'utilisateur ne peut pas s'inscrire : la date de l'activité ne correspond pas à sa période de séjour."]);
            exit();
        }
    } else {
        echo json_encode(["error" => "L'utilisateur <" . $row['USER'] . "> n'est pas dans la base.", "sql" => $stmt1]);
        exit();
    }

    // Vérifier la capacité restante de l'animation
    $query2 = "SELECT NBREPLACEANIM FROM ANIMATION WHERE CODEANIM=?";
    $stmt2 = $conn->prepare($query2);
    if (!$stmt2)
        throw new Exception("Erreur de préparation de la requête: " . $conn->error);
    $stmt2->bind_param("s", $row['CODEANIM']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
        $nbreplaceanim = $row2['NBREPLACEANIM'];

        $query3 = "SELECT COUNT(*) AS n FROM INSCRIPTION WHERE CODEANIM=? AND DATEACT=?";
        $stmt3 = $conn->prepare($query3);
        if (!$stmt3)
            throw new Exception("Erreur de préparation de la requête: " . $conn->error);
        $stmt3->bind_param("ss", $row['CODEANIM'], $row['DATEACT']);
        $stmt3->execute();
        $result3 = $stmt3->get_result();
        $row3 = $result3->fetch_assoc();
        $inscriptionsCount = $row3['n'];

        if ($inscriptionsCount == $nbreplaceanim) {
            echo json_encode(['error' => "Il n'y a plus de place disponible"]);
        } else {

            // Vérifier les conflits d'horaire
            $query4 = "SELECT a.HRDEBUTACT, a.HRFINACT
                       FROM INSCRIPTION i
                       JOIN ACTIVITE a ON i.CODEANIM = a.CODEANIM AND i.DATEACT = a.DATEACT
                       WHERE i.USER = ? AND i.DATEACT = ?";
            $stmt4 = $conn->prepare($query4);
            if (!$stmt4)
                throw new Exception("Erreur de préparation de la requête: " . $conn->error);
            $stmt4->bind_param("ss", $row['USER'], $row['DATEACT']);
            $stmt4->execute();
            $result4 = $stmt4->get_result();

            $newStart = $row['HRDEBUTACT'];
            $newEnd = $row['HRFINACT'];

            while ($existing = $result4->fetch_assoc()) {
                $existingStart = $existing['HRDEBUTACT'];
                $existingEnd = $existing['HRFINACT'];

                if ($newStart < $existingEnd && $newEnd > $existingStart) {
                    echo json_encode([
                        "error" => "Conflit d'horaires avec une autre activité réservée entre $existingStart et $existingEnd."
                    ]);
                    exit;
                }
            }

            // Vérifier la limite d'âge
            $query5 = "SELECT a.LIMITEAGE, c.DATENAISCOMPTE 
           FROM ANIMATION a, COMPTE c 
           WHERE a.CODEANIM = ? AND c.USER = ?";
            $stmt5 = $conn->prepare($query5);
            if (!$stmt5) {
                throw new Exception("Erreur de préparation de la requête: " . $conn->error);
            }
            $stmt5->bind_param("ss", $row['CODEANIM'], $row['USER']);
            $stmt5->execute();
            $result5 = $stmt5->get_result();

            if ($result5->num_rows > 0) {
                $row5 = $result5->fetch_assoc();
                $limiteAge = (int) $row5['LIMITEAGE'];
                $dateNaissance = $row5['DATENAISCOMPTE'];

                if ($dateNaissance) {
                    $age = date_diff(date_create($dateNaissance), date_create('today'))->y;

                    if ($age < $limiteAge) {
                        echo json_encode(["error" => "L'âge requis est de $limiteAge ans minimum. Vous avez $age ans."]);
                        exit();
                    }
                } else {
                    echo json_encode(["error" => "Date de naissance manquante pour le vacancier."]);
                    exit();
                }
            } else {
                echo json_encode(["error" => "Données introuvables pour vérifier l'âge."]);
                exit();
            }

            // Insérer l'inscription
            $insertQuery = "INSERT INTO INSCRIPTION (USER, CODEANIM, DATEACT, DATEINSCRIP) VALUES (?, ?, ?, ?)";
            $stmt5 = $conn->prepare($insertQuery);
            if (!$stmt5)
                throw new Exception("Erreur de préparation de la requête: " . $conn->error);
            $stmt5->bind_param("ssss", $row['USER'], $row['CODEANIM'], $row['DATEACT'], $row['DATEINSCRIP']);
            $stmt5->execute();

            $last_id = $stmt5->insert_id;
            echo json_encode([
                'success' => "Inscription enregistrée avec succès. Le numéro d'inscription est " . $last_id,
                'NOINSCRIP' => $last_id
            ]);
        }
    } else {
        echo json_encode(["error" => "Aucune animation trouvée."]);
        exit();
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur serveur: " . $e->getMessage()]);
}

$conn->close();
