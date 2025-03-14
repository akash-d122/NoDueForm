<!-- sidebar.php -->

<div class="sidebar">
    <div>
        <img src="logo.png">
        <h3><a href="faculty_dashboard.php">Faculty Dashboard</a></h3>
        <a href="faculty_formList.php">List of Forms</a>
        <a href="facultyReset.php">Reset Password</a>
        <a href="#">Profile</a>
        <!-- <a href="edit_profile.php">Edit Profile</a> -->
    </div>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<style>
    .sidebar {
        height: 100%;
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #343a40;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-bottom: 20px;
        font-family: Poppins, sans-serif;
    }

    .sidebar img {
        height: 140px;
        width: auto;
        margin: 10px auto 20px auto;
        display: block;
        border-radius: 50%;
    }

    .sidebar a {
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        display: block;
    }

    .sidebar a:hover {
        background-color: #575757;
    }

    .sidebar a.text-danger {
        color: white !important; /* white text for the logout button */
        font-weight: bold;
        padding: 10px;
        margin: 10px;
        background-color: #8C0000;
        border-radius: 15px;
        margin-bottom: 30px; /* Add some spacing from the bottom */
        text-align: center;
        
    }

    .sidebar a.text-danger:hover {
        background-color: #CCCCCC;
        border: 1px solid black;
        color: red !important; /* Keep text white on hover */
    }
</style>
