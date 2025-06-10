<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Garden Library</title>    <link rel="icon" href="<?php echo $base_path; ?>favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <script src="<?php echo $base_path; ?>assets/js/script.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <div class="row">                
                <div class="col-6">
                    <div class="logo">
                        <img src="<?php echo $base_path; ?>assets/img/LogoCat.png" alt="Library Logo">
                        <h1>Garden Library</h1>
                    </div>
                </div>                  <div class="col-6">
                    <div class="auth-links">
                        <nav class="main-menu">
                            <a href="<?php echo $base_path; ?>index.php">Home</a>
                            <a href="<?php echo $base_path; ?>index.php#features">Features</a>
                            <a href="<?php echo $base_path; ?>index.php#features">About</a>
                        </nav>
                        <a href="<?php echo $base_path; ?>pages/signup/index.php" class="auth-button signup">SIGN UP NOW</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
