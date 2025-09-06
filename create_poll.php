<?php
session_start(); // Start the session to store dynamic options

// Database connection
include("dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get poll question, date, and duration
    $poll_question = $_POST['poll_question'];
    $poll_date = $_POST['poll_date'];
    $duration = $_POST['duration'];
    
    // Insert the poll data into the database
    $sql = "INSERT INTO activity_poll (title, poll_date, duration) VALUES ('$poll_question', '$poll_date', '$duration')";
    if ($conn->query($sql) === TRUE) {
        $poll_id = $conn->insert_id; // Get the last inserted poll ID

        // Insert options stored in the session into the database
        if (isset($_SESSION['options'])) {
            foreach ($_SESSION['options'] as $option) {
                $insert_option_sql = "INSERT INTO poll_options (poll_id, option_text, vote_count, budget, currency) 
                                      VALUES ('$poll_id', '$option[0]', 0, '$option[1]', '$option[2]')";
                $conn->query($insert_option_sql);
            }
        }
        
        // Clear options after storing them in the database
        unset($_SESSION['options']);
        
        // Redirect to view the poll page
        header("Location: view_poll.php?poll_id=$poll_id");
        exit();
    }
}

// Handle adding options to the session
if (isset($_POST['add_option'])) {
    $new_option = $_POST['option_title'];
    $new_budget = $_POST['option_budget'];  // Budget for the new option
    $new_currency = $_POST['currency'];    // Currency for the budget
    
    if (!empty($new_option) && !empty($new_budget) && !empty($new_currency)) {
        if (!isset($_SESSION['options'])) {
            $_SESSION['options'] = [];
        }
        // Store the option text, budget, and currency in the session
        $_SESSION['options'][] = [$new_option, $new_budget, $new_currency]; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Poll</title>
    <link rel="stylesheet" href="create_poll.css">
    <script>
        function addOption() {
            // Get the input values
            var optionTitle = document.getElementById('option_title').value;
            var optionBudget = document.getElementById('option_budget').value;
            var currency = document.getElementById('currency').value;

            // Create new option row
            var newOptionDiv = document.createElement('div');
            newOptionDiv.classList.add('input-group');

            // Add option title and budget
            var optionInput = document.createElement('input');
            optionInput.type = 'text';
            optionInput.value = optionTitle;
            optionInput.readOnly = true;
            newOptionDiv.appendChild(optionInput);

            var budgetInput = document.createElement('input');
            budgetInput.type = 'text';
            budgetInput.value = optionBudget + " " + currency;
            budgetInput.readOnly = true;
            newOptionDiv.appendChild(budgetInput);

            // Add new option to options container
            document.getElementById('options-container').appendChild(newOptionDiv);

            // Optionally, reset input fields
            document.getElementById('option_title').value = '';
            document.getElementById('option_budget').value = '';
        }
    </script>
</head>
<body>
    <nav>
        <div class="logo">CHOL</div>
        <ul>
            <li><a href="home.php">HOME</a></li>
        </ul>
    </nav>

    <div class="poll-form-container">
        <h1>Create Poll</h1>
        <form action="create_poll.php" method="POST">
            <div class="input-group">
                <label for="poll_question">Poll Question</label>
                <input type="text" id="poll_question" name="poll_question" required>
            </div>

            <div class="input-group">
                <label for="poll_date">Poll Date</label>
                <input type="date" id="poll_date" name="poll_date" required>
            </div>

            <div class="input-group">
                <label for="duration">Duration (hh:mm)</label>
                <input type="time" id="duration" name="duration" required>
            </div>

            <!-- Display added options dynamically -->
            <div id="options-container">
                <!-- New options will appear here -->
            </div>

            <!-- Option to add new option and budget -->
            <div class="input-group">
                <label for="option_title">Add Option</label>
                <input type="text" id="option_title" name="option_title" required>
                <label for="option_budget">Budget</label>
                <input type="text" id="option_budget" name="option_budget" required>

                <!-- Currency Selector -->
                <label for="currency">Currency</label>
                <select id="currency" name="currency" required>
                    <option value="BDT">BDT</option>
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="ALL">ALL</option>
                    <!-- Add more currencies as needed -->
                </select>

                <button type="button" onclick="addOption()">ADD</button>
            </div>

            <button type="submit" name="add_option">Create Poll</button>
        </form>
    </div>

</body>
</html>


