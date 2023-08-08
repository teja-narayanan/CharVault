<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>Group 9 PHP Database Program</title>
    <link rel="stylesheet" href="base.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.3);
            border-radius: 10px;
        }
    </style>
</head>
<body>

<?php
// Import required files
require_once 'config.inc.php';
require_once 'header.inc.php';
require_once 'stat.inc.php';

// Get Character ID
$id = $_GET['id'];
if ($id === "" || $id === false || $id === null) {
    header('location: update_user.php');
    exit();
}
?>

<div class="container">
    <h2>Update User Info</h2>
    <?php

    // add button at top to allow user to go back to the show_user page
    echo "<a class=\"char-update-button\" href='show_user.php?id=$id'> back to user info </a><br><br>";

    // Create connection
    $conn = new mysqli($config_servername, $config_username, $config_password, $config_database, $config_port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check the Request is an Update from User -- Submitted via Form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Prepare the update statement
        $sql = "UPDATE `Character`
                INNER JOIN Stat USING(stat_id)
                SET username              = ?,
                    user_level            = ?,
                    hp                    = ?,
                    mp                    = ?,
                    strength              = ?,
                    dexterity             = ?,
                    vitality              = ?,
                    intelligence          = ?,
                    critical_hit          = ?,
                    determination         = ?,
                    direct_hit_rate       = ?,
                    defense               = ?,
                    magic_defense         = ?,
                    attack_power          = ?,
                    skill_speed           = ?,
                    attack_magic_potency  = ?,
                    healing_magic_potency = ?,
                    spell_speed           = ?,
                    tenacity              = ?,
                    piety                 = ?
                WHERE character_id        = ?";

        $stmt = $conn->stmt_init();
        if (!$stmt->prepare($sql)) {
            echo "Failed to prepare: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            // Bind the parameters
            $stmt->bind_param(
                'sssssssssssssssssssss',
                $_POST['username'],
                $_POST['user_level'],
                $_POST['hp'],
                $_POST['mp'],
                $_POST['strength'],
                $_POST['dexterity'],
                $_POST['vitality'],
                $_POST['intelligence'],
                $_POST['critical_hit'],
                $_POST['determination'],
                $_POST['direct_hit_rate'],
                $_POST['defense'],
                $_POST['magic_defense'],
                $_POST['attack_power'],
                $_POST['skill_speed'],
                $_POST['attack_magic_potency'],
                $_POST['healing_magic_potency'],
                $_POST['spell_speed'],
                $_POST['tenacity'],
                $_POST['piety'],
                $id
            );

            // Execute statement and commit transaction
            $success = $stmt->execute();
            $conn->commit();

            // report status of to user
            if ($success) {
                echo "Update successful.";
            } else {
                echo "Update failed: (" . $stmt->errno . ") " . $stmt->error;
            }
        }
    }

    // Refresh the data
    $sql = "SELECT character_id,
                   username,
                   user_level,
                   hp,
                   mp,
                   strength,
                   dexterity,
                   vitality,
                   intelligence,
                   critical_hit,
                   determination,
                   direct_hit_rate,
                   defense,
                   magic_defense,
                   attack_power,
                   skill_speed,
                   attack_magic_potency,
                   healing_magic_potency,
                   spell_speed,
                   tenacity,
                   piety
        FROM `Character`
        INNER JOIN Stat USING(stat_id)
        WHERE character_id = ?";

    $stmt = $conn->stmt_init();
    if (!$stmt->prepare($sql)) {
        echo "Failed to prepare: (" . $stmt->errno . ") " . $stmt->error;
    } else {
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->bind_result(
            $character_id,
            $username,
            $user_level,
            $hp,
            $mp,
            $strength,
            $dexterity,
            $vitality,
            $intelligence,
            $critical_hit,
            $determination,
            $direct_hit_rate,
            $defense,
            $magic_defense,
            $attack_power,
            $skill_speed,
            $attack_magic_potency,
            $healing_magic_potency,
            $spell_speed,
            $tenacity,
            $piety
        );
        $stmt->fetch();

    ?>

    <br>
    <form method="post">
        <button type="submit" class="update-button">Update</button>
        <input type="hidden" name="id" value="<?= $id ?>">

        <br>
        <br>

        <div class="table-container">
            <table>
                <tr class='table-row'>
                    <td><label>New Username:</label></td>
                    <td><input type="text" name="username" class="update-field" required pattern="[a-zA-Z0-9]+" value="<?= $username ?>"></td>
                </tr>

                <?php
                $skip_first  = true;
                foreach ($stats_array as $stats) {
                    foreach ($stats as $name => $label) {
                        // skip the first field: username
                        if ($skip_first) {
                            $skip_first = false;
                            continue;
                        }

                        $value = $$name;
                        echo "<tr class='table-row'>";
                        echo "<td><label for=\"$name\">New $label:</label></td>";
                        echo "<td><input type=\"text\" id=\"$name\" name=\"$name\" class=\"update-field\" required pattern=\"[0-9]+\" value=\"$value\"></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </table>
        </div>
    </form>

    <?php
    }

    $conn->close();
    ?>

</div>
</body>
</html>
