<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Scholarship Management System</title>
    <link href="<?php echo BASE_PATH; ?>assets/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>assets/css/bootstrap-icons.css">
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
            padding: 8px 16px !important;
            border-radius: 8px;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd !important;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 2px;
            left: 50%;
            background-color: #0d6efd;
            transition: all 0.3s ease;
            transform: translateX(-50%);
            opacity: 1;
        }
        .nav-link:hover::after, .nav-link.active::after {
            width: 60%;
        }

        /* Modern Light Impact Section */
        .impact-section {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            position: relative;
            overflow: hidden;
        }
        .impact-section::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.05) 0%, rgba(255,255,255,0) 60%);
            animation: pulse-bg 10s infinite alternate;
        }
        @keyframes pulse-bg {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }
        .modern-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.03);
            border-radius: 1.25rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }
        .modern-card:hover {
            transform: translateY(-10px);
            border-color: rgba(99, 102, 241, 0.1);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.1);
        }
        .icon-wrapper-primary {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            border-radius: 50%;
            width: 75px; height: 75px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem auto;
            transition: all 0.3s ease;
        }
        .modern-card:hover .icon-wrapper-primary {
            background: #0d6efd;
            color: #ffffff;
            transform: scale(1.1);
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
        }
        .icon-wrapper-success {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
            border-radius: 50%;
            width: 75px; height: 75px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem auto;
            transition: all 0.3s ease;
        }
        .modern-card:hover .icon-wrapper-success {
            background: #198754;
            color: #ffffff;
            transform: scale(1.1);
            box-shadow: 0 10px 20px rgba(25, 135, 84, 0.2);
        }
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>