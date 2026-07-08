<?php
    // If the user is already logged in, redirect them directly to the app dashboard
    if (session('uid')) {
        header("Location: " . cn('statistics'));
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title><?=get_option('website_title', "New View Order")?></title>
    
    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6366f1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="/assets/images/pwa-icon-192.png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Reset & App Setup */
        body, html { 
            margin: 0; padding: 0; background-color: #0B0B0C; /* Obsidian Black */
            height: 100vh; display: flex; justify-content: center; align-items: center; overflow: hidden;
        }

        /* Floating Social & Wealth Icons Effect */
        .floating-elements {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; z-index: 1; pointer-events: none;
        }
        .float-icon {
            position: absolute; color: #D4AF37; font-size: 24px;
            animation: float-up linear infinite; opacity: 0;
            filter: drop-shadow(0 0 5px rgba(212, 175, 55, 0.4));
        }
        @keyframes float-up {
            0% { transform: translateY(100vh) rotate(0deg) scale(0.8); opacity: 0; }
            15% { opacity: 0.15; }
            85% { opacity: 0.15; }
            100% { transform: translateY(-20vh) rotate(360deg) scale(1.2); opacity: 0; }
        }

        /* Randomizing icon paths, speeds, and sizes */
        .fi-1 { left: 10%; font-size: 34px; animation-duration: 14s; animation-delay: 0s; }
        .fi-2 { left: 25%; font-size: 22px; animation-duration: 18s; animation-delay: 3s; }
        .fi-3 { left: 45%; font-size: 28px; animation-duration: 15s; animation-delay: 1s; }
        .fi-4 { left: 65%; font-size: 40px; animation-duration: 20s; animation-delay: 5s; }
        .fi-5 { left: 80%; font-size: 26px; animation-duration: 16s; animation-delay: 2s; }
        .fi-6 { left: 90%; font-size: 30px; animation-duration: 19s; animation-delay: 6s; }
        .fi-7 { left: 15%; font-size: 20px; animation-duration: 17s; animation-delay: 8s; }
        .fi-8 { left: 55%; font-size: 35px; animation-duration: 22s; animation-delay: 4s; }
        .fi-9 { left: 35%; font-size: 25px; animation-duration: 25s; animation-delay: 10s; }
        /* Ambient Background Glow */
        .ambient-glow {
            position: absolute; width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, rgba(11, 11, 12, 0) 70%);
            border-radius: 50%; top: 50%; left: 50%; transform: translate(-50%, -50%);
            animation: pulse-glow 4s ease-in-out infinite alternate;
            z-index: 0;
        }
        @keyframes pulse-glow {
            0% { transform: translate(-50%, -50%) scale(1); opacity: 0.5; }
            100% { transform: translate(-50%, -50%) scale(1.5); opacity: 1; }
        }

        .logo-wrapper {
            display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 10;
        }
        .floating-logo {
            width: 140px; height: auto; margin-bottom: 20px;
            animation: levitate-logo 4s ease-in-out infinite;
            filter: drop-shadow(0px 10px 15px rgba(0,0,0,0.8));
        }
        @keyframes levitate-logo {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }

        /* Branding Text */
        .brand-title {
            font-size: 24px; font-weight: 700; color: #fff; letter-spacing: 3px;
            margin-bottom: 5px; text-transform: uppercase;
            animation: fade-up 1s ease-out forwards; opacity: 0; transform: translateY(10px);
        }
        .brand-subtitle {
            font-size: 12px; font-weight: 500; color: #8E8E93; letter-spacing: 5px;
            text-transform: uppercase; margin-bottom: 40px;
            animation: fade-up 1s ease-out 0.3s forwards; opacity: 0; transform: translateY(10px);
        }

        /* Sleek Loading Bar */
        .loading-container {
            width: 220px; height: 4px; background: #18181A; border-radius: 6px; overflow: hidden;
            animation: fade-up 1.2s ease-out 0.8s forwards; opacity: 0; transform: translateY(15px);
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.5);
        }
        .loading-bar {
            width: 0%; height: 100%; background: linear-gradient(90deg, #D4AF37, #FFD700, #D4AF37);
            background-size: 200% 100%;
            animation: load-progress 6s cubic-bezier(0.6, 0.05, 0.2, 1) forwards, gradient-shift 2s linear infinite;
        }

        @keyframes fade-up {
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes load-progress {
            0% { width: 0%; }
            30% { width: 45%; }
            70% { width: 85%; }
            100% { width: 100%; }
        }
        @keyframes gradient-shift {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }
    </style>
</head>
<body>
    <!-- Floating Background Icons -->
    <div class="floating-elements">
        <i class="fa-brands fa-instagram float-icon fi-1"></i>
        <i class="fa-solid fa-coins float-icon fi-2"></i>
        <i class="fa-brands fa-tiktok float-icon fi-3"></i>
        <i class="fa-solid fa-chart-line float-icon fi-4"></i>
        <i class="fa-brands fa-facebook-f float-icon fi-5"></i>
        <i class="fa-solid fa-gem float-icon fi-6"></i>
        <i class="fa-brands fa-youtube float-icon fi-7"></i>
        <i class="fa-solid fa-coins float-icon fi-8"></i>
        <i class="fa-solid fa-rocket float-icon fi-9"></i>
    </div>

    <!-- Core UI -->
    <div class="ambient-glow"></div>
    <div class="logo-wrapper">
        <img src="<?php echo BASE; ?>themes/nico/assets/images/site_logo_gold.png" alt="Logo" class="floating-logo">
        <div class="brand-title">New View Order</div>
        <div class="brand-subtitle">Exclusive</div>
        <div class="loading-container">
            <div class="loading-bar"></div>
        </div>
    </div>
    
    <script>
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('PWA: SW registered');
                        window.addEventListener('beforeinstallprompt', (e) => {
                            console.log('PWA: beforeinstallprompt fired');
                        });
                    })
                    .catch(err => console.log('PWA: SW registration failed: ', err));
            });
        }

        // Redirect to the actual Login UI page after the premium 6.5s loading sequence finishes
        setTimeout(function() {
            window.location.href = '<?php echo cn("auth/login"); ?>';
        }, 6500); 
    </script>
</body>
</html>