<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Scholarship Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Custom global styles for smooth UI */
        body { font-family: 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .rounded-4 { border-radius: 1rem; }
        
        /* Navbar Custom Styles */
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.9) !important;
            padding: 8px 16px !important;
            border-radius: 8px;
        }
        .nav-link:hover, .nav-link.active {
            color: #fff !important;
            background-color: rgba(0, 0, 0, 0.2); /* Slightly dark background for the button */
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 2px;
            left: 50%;
            background-color: white;
            transition: all 0.3s ease;
            transform: translateX(-50%);
            opacity: 0.8;
        }
        .nav-link:hover::after, .nav-link.active::after {
            width: 60%;
        }
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>