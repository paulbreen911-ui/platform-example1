<?php
// Ensure session handling for Railway
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>


<!-- NAV -->
<nav class="nav">
  <a class="nav-logo" href="#">PRODUCTION<span>.</span>CENTRAL</a>
  <div class="nav-links">
    <a class="nav-link" href="#forum">Forum</a>
    <a class="nav-link" href="#education">Education</a>
    <a class="nav-link" href="#reference">Reference</a>
    <a class="nav-link" href="#technology">Technology</a>
    <a class="nav-link" href="tools/index.html">Tools</a>
    <a class="nav-link" href="#life">Life</a>
    <a class="nav-link store" href="#store">Store</a>
  </div>
  <div class="nav-right">
    <a class="btn-demo" href="app/dashboard.html">
      <div class="btn-demo-dot"></div>
      Try demo login
    </a>

    <!-- Attempt to join in old code -->

    <!-- -->

    <a class="btn-signin" href="app/dashboard.html">Sign in</a>
    <a class="btn-join" href="app/dashboard.html">Join free →</a>
  </div>
</nav>

<!-- This is the original code -->
<header>
    <h1>Welcome to Production Central</h1>
    <nav>
        <ul>

        </ul>
    </nav>
</header>

            <!-- -->

<main>
