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

        /* Style drop-down menu */
        .drop-down {
            align-items: center;
            padding: 8px 16px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            background-color: #fff;
        }

        /* Change color of drop-down menu on hover */
        .drop-down:hover {
            background-color: #f1f1f1;
        }

        /* Remove default styling for drop-down menu */
        select {
            -moz-appearance: none;
            -webkit-appearance: none;
        }

        /* Style filter fields */
        .filter-row {
            display: flex;
            flex-wrap: wrap;
        }

        .filter-item {
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php
// Import required files
require_once 'config.inc.php';
require_once 'header.inc.php';
?>

<div class="container">
    <h2>User List</h2>

    <!-- Create Filters -->
    <form method="get" action="">
        <div class="filter-row">
            <!-- Filter by Level -->
            <div class="filter-item">
                <label for="level_filter">Minimum Level:</label>
                <br>
                <input type="text" name="level_filter" id="level_filter" class="filter-field" pattern="[0-9]*"
                    value="<?= isset($_GET['level_filter']) ? $_GET['level_filter'] : '' ?>">
            </div>

            <!-- Filter by username -->
            <div class="filter-item">
                <label for="username_filter">Username Contains:</label>
                <br>
                <input type="text" name="username_filter" id="username_filter" class="filter-field" pattern="[a-zA-Z0-9]*"
                    value="<?= isset($_GET['username_filter']) ? $_GET['username_filter'] : '' ?>">
            </div>

            <!-- Apply Filter button -->
            <div class="filter-item">
                <br>
                <input type="submit" value="Apply Filter" class="filter-button">
            </div>
        </div>

        <br>
        <br>

        <!-- Create sort by username or level option -->
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort" class="drop-down">
            <option value="username" <?= isset($_GET['sort']) && $_GET['sort'] === 'username' ? 'selected' : '' ?>>Username</option>
            <option value="level"    <?= isset($_GET['sort']) && $_GET['sort'] === 'level'    ? 'selected' : '' ?>>Level</option>
            <option value="uid"      <?= isset($_GET['sort']) && $_GET['sort'] === 'uid'      ? 'selected' : '' ?>>ID</option>
        </select>

        <!-- Apply Sort button -->
        <input type="submit" value="Sort" class="filter-button">
    </form>

    <?php
    // Create connection
    $conn = new mysqli($config_servername, $config_username, $config_password, $config_database, $config_port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL Statement
    $sql = "SELECT character_id, username, user_level FROM `Character`";

    // Check if level filter is provided - if so, add it to the query
    if (isset($_GET['level_filter']) && !empty($_GET['level_filter'])) {
        $level_filter = $_GET['level_filter'];
        $sql .= " WHERE user_level >= ?";
    }

    // Check if username filter is provided - if so, add it to the query
    if (isset($_GET['username_filter']) && !empty($_GET['username_filter'])) {
        $username_filter = $_GET['username_filter'];
        if (isset($level_filter) && !empty($level_filter)) {
            $sql .= " AND username LIKE ?";
        } else {
            $sql .= " WHERE username LIKE ?";
        }
    }

    // Sort result by the selected sort option
    $sort = $_GET['sort'];
    if ($sort === 'level') {
        $sql .= " ORDER BY user_level, username, character_id"; // sort by level
    } else if ($sort === 'uid') {
        $sql .= " ORDER BY character_id, username, user_level"; // sort by character id
    } else {
        $sql .= " ORDER BY username, user_level, character_id"; // default: sort by username
    }

    // Process query
    $stmt = $conn->stmt_init();
    if (!$stmt->prepare($sql)) {
        echo "Failed to prepare: (" . $stmt->errno . ") " . $stmt->error;
    } else {
        // Bind level filter
        if (isset($level_filter) && !isset($username_filter)) {
            $stmt->bind_param('s', $level_filter);
        }
        // Bind username filter
        else if (isset($username_filter) && !isset($level_filter)) {
            $username_filter = '%' . $username_filter . '%';
            $stmt->bind_param('s', $username_filter);
        }
        // Bind level and username filter - both provided
        else if (isset($username_filter) && isset($level_filter)) {
            $username_filter = '%' . $username_filter . '%';
            $stmt->bind_param('ss', $level_filter, $username_filter);
        }

        // Execute the statement
        $stmt->execute();

        // Bind the result columns
        $stmt->bind_result($character_id, $username, $user_level);

        // Display list of users
        echo "<table><tr>  <th>Username</th> <th>ID</th>  <th>Level</th>  </tr>";

        // Loop through result and print users
        while ($stmt->fetch()) {
            // make entire row into a button
            echo "<tr class='table-row' onclick=\"window.location='show_user.php?id=$character_id'\">";
            echo "<td><a href='show_user.php?id=$character_id'>$username</a></td>";
            echo "<td>$character_id</td>";
            echo "<td>$user_level</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Close Connection
    $conn->close();
    ?>

</div>
</body>
</html>
