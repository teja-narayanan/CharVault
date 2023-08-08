<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>Group 9 PHP Database Program</title>
    <link rel="stylesheet" href="base.css">
    <style>
        /* Style container */
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 255, 0.3);
            border-radius: 10px;
        }

        /* Style tabs */
        .tabs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .tab {
            padding: 10px 20px;
            background-color: #eeeeee;
            border-radius: 10px;
            cursor: pointer;
        }

        /* Highlight active table */
        .tab.active {
            border-bottom: 4px solid rgba(0, 0, 255, 0.4);
            box-shadow: 0 0 10px rgba(0, 0, 255, 0.3);
            border-radius: 10px;
        }

        /* Style cards */
        .card {
            display: none;
            padding: 10px;
            border: 1px solid #ccc;
            border-top: none;
        }

        .card.active {
            display: block;
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
    header('location: show_user.php');
    exit();
}
?>

<div class="container">
    <h2>User Report</h2>

    <?php
    // Create connection
    $conn = new mysqli($config_servername, $config_username, $config_password, $config_database, $config_port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

	// Prepare SQL using Parameterized Form (Safe from SQL Injections)
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
                   piety,
                   job_name,
                   job_level,
                   class_name,
                   rolling_xp,
                   item_name,
                   item_description,
                   current_quantity 
            FROM `Character`
            INNER JOIN Stat             USING(stat_id)
            INNER JOIN Stack            USING(inventory_id)
            INNER JOIN Item             USING(item_id)
            INNER JOIN CharacterJobInfo USING(character_id)
            INNER JOIN Job              USING(job_name)
            WHERE character_id = ?";

    $stmt = $conn->stmt_init();
    if (!$stmt->prepare($sql)) {
        echo "Failed to prepare: (" . $stmt->errno . ") " . $stmt->error;
    } else {
        // Bind Parameters from user input
        $stmt->bind_param('s', $id);

        // Execute the Statement
        $stmt->execute();

        // Fetch the values
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
            $piety,
            $job_name,
            $job_level,
            $class_name,
            $rolling_xp,
            $item_name,
            $item_description,
            $current_quantity
        );

        // Create empty arrays to store all the jobs and items in character inventory
        $jobs      = [];
        $inventory = [];

        // Fetch inventory and job info of character
        while ($stmt->fetch()) {
            // Store job-related information
            $jobs[] = [
                'job_name'   => $job_name,
                'class_name' => $class_name,
                'rolling_xp' => $rolling_xp,
                'job_level'  => $job_level
            ];

            // Store inventory-related information
            $inventory[] = [
                'item_name'        => $item_name,
                'item_description' => $item_description,
                'current_quantity' => $current_quantity
            ];
        }

        // Remove duplicate values from the jobs and inventory array
        $jobs      = array_map("unserialize", array_unique(array_map("serialize", $jobs)));
        $inventory = array_map("unserialize", array_unique(array_map("serialize", $inventory)));

        // Display general user info
        echo "<table>";

        // Display first row, user ID (character_id)
        echo "<tr class='table-row'><td>User ID</td><td>" . $id . "</td></tr>";

        // Display general user info
        foreach ($general_info as $value => $name) {
            echo "<tr class='table-row'>";
            echo "<td>" . $name . "</td><td>" . $$value . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        ?>

        <!-- Create tabes for other character info: stats, jobs, inventory -->
        <div class="tabs">
            <div class="tab active" onclick="showCard(event, 'display-stats')">     Stat Info      </div>
            <div class="tab"        onclick="showCard(event, 'display-jobs')">      Jobs Info      </div>
            <div class="tab"        onclick="showCard(event, 'display-inventory')"> Inventory Info </div>
        </div>

        <?php

        // Display stats
        echo '<div class="card active" id="display-stats">';
        echo "<h2>User Stat Info</h2>";
        foreach ([$primary_stats, $secondary_stats] as $stats) {
            echo "<table>";
            echo "<tr><th colspan='2'>";
            if ($stats === $primary_stats)   echo "Primary Stats";
            if ($stats === $secondary_stats) echo "Secondary Stats";
            echo "</th></tr>";

            foreach ($stats as $value => $name) {
                echo "<tr class='table-row'>";
                echo "<td>" . $name . "</td><td>" . $$value . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // Create update button at the bottom of stats card to update user info
        echo "<br>";
        echo "<a class='char-update-button' href='update_user.php?id={$character_id}'>Update User Info and Stats</a>";
        echo "<br><br></div>";

        ?>

        <!--Display jobs-->
        <div class="card" id="display-jobs">
            <table>
                <h2>User Job Info</h2>
                <tr>
                    <th>Class</th>
                    <th>Job</th>
                    <th>Level</th>
                    <th>Rolling XP</th>
                </tr>
                <?php

                foreach ($jobs as $job) {
                    echo "<tr class='table-row'>";
                    echo "<td>{$job['class_name']}</td>";
                    echo "<td>{$job['job_name']}</td>";
                    echo "<td>{$job['job_level']}</td>";
                    echo "<td>{$job['rolling_xp']}</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>

        <!-- Display inventory -->
        <div class="card" id="display-inventory">
            <table>
                <h2>User Inventory Info</h2>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Quantity</th>
                </tr>
                <?php

                foreach ($inventory as $item) {
                    echo "<tr class='table-row'>";
                    echo "<td>{$item['item_name']}</td>";
                    echo "<td>{$item['item_description']}</td>";
                    echo "<td>{$item['current_quantity']}</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>

        <?php
    }

    // Close Connection
    $conn->close();
    ?>

</div>

<script>
    function showCard(event, cardId) {
        // Remove the "active" class from all tabs - deselect tabs
        const tabs = document.getElementsByClassName("tab");
        for (let i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove("active");
        }

        // Remove the "active" class from all cards - hide cards
        const cards = document.getElementsByClassName("card");
        for (let i = 0; i < cards.length; i++) {
            cards[i].classList.remove("active");
        }

        // Add the "active" class to the clicked tab - highlight the selected tab
        event.currentTarget.classList.add("active");

        // Add the "active" class to the corresponding card - display the selected card
        document.getElementById(cardId).classList.add("active");
    }
</script>

</body>
</html>
