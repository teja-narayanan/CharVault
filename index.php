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
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.3);
            border-radius: 10px;
        }

        ul {
            list-style-type: disc;
            color: #666666;
            line-height: 1.6;
        }

    </style>
</head>

<?php
// Import required files
require_once 'header.inc.php';
?>

<body>
<div class="container">
    <h2>About</h2>
    <p>
        This is a simple PHP program that performs database operations
        using the mysqli library in PHP.
    </p>

    <p>
        <strong>This website implements the following required functionality:</strong>
        <ul>
            <li>Produce <strong>ONE</strong> meaningful list-based report based on the original artifacts.</li>
            <li>Produce <strong>ONE</strong> meaningful detail report based on the original artifacts.</li>
            <li>Be able to update at least <strong>ONE</strong> meaningful table related to your reports.</li>
        </ul>
    </p>

    <h3>List-Based Report</h3>
    <ul>
        <li>Produces a list of users from the database.</li>
        <li>Allows the optional filtering of users based on level or username.</li>
        <ul>
            <li><strong>level:</strong> returns all users who meet the minimum specified level.</li>
            <li><strong>username:</strong> returns all users whose username contains the provided string.</li>
            <li><strong>Note 1:</strong> If only one of the filter fields is filled out, it will only filter based on that parameter.</li>
            <li><strong>Note 2:</strong> If both fields are provided, it will return the set of users who meet both conditions in the filters.</li>
        </ul>
        <li>Allows sorting of users by username, level, or ID.</li>
        <li>Contains a hyperlink per row that expands into the next report.</li>
    </ul>

    <h3>Detail Report</h3>
    <ul>
        <li>Produces a detailed report on users, showing their stats, jobs, and inventory items.</li>
        <li>Joins details from the following tables:</li>
        <ul>
            <li>Character</li>
            <li>Stat</li>
            <li>Stack</li>
            <li>Item</li>
            <li>CharacterJobInfo</li>
            <li>Job</li>
        </ul>
    </ul>

    <h3>Table Update</h3>
    <ul>
        <li>Allows updating of usernames and stats information.</li>
    </ul>

</div>
</body>
</html>
