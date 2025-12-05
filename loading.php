<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <title>Smart Pantry Chef</title>
    <link rel="icon" type="image/png" href="/Applications/XAMPP/xamppfiles/htdocs/PHP-frontend/logo.png"/>

    

           <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #f8e9ee;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .logo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .logo img {
            width: 220px;
        }

        h1 {
            margin-top: 20px;
            font-size: 26px;
            color: #444;
        }

        
        .slogan {
            margin-top: 10px;
            font-size: 16px;
            font-weight:bold;
            color: #5D4037;
            white-space: nowrap;
            overflow: hidden;
            border-right: 3px solid #d36a8a;
            width: 0;
            animation: typing 4s steps(40) forwards, blinkCursor 0.8s infinite;
        }

        @keyframes typing {
            from { width: 0; }
            to { width: 280px; }
        }

        @keyframes blinkCursor {
            50% { border-color: transparent; }
        }

        /* Dots */
        .loading {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }

        .dot {
            width: 13px;
            height: 13px;
            background: #d36a8a;
            border-radius: 50%;
            animation: blink 1.2s infinite;
        }

        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes blink {
            0% { opacity: 0.2; }
            50% { opacity: 1; }
            100% { opacity: 0.2; }
        }
    </style>
       
</head>

<body>
      <div class="logo">
       <img src="logo.png" alt="logo">
    </div>

    <div id="slogan"></div>

    <script>
        const logo = document.querySelector('.logo img');
        
        const sloganText = "Cook what you have, buy what you need";
        const sloganElement = document.getElementById("slogan");

        let index = 0;

        
        function typeSlogan() {
            if (index < sloganText.length) {
                sloganElement.innerHTML = sloganText.substring(0, index + 1) + '<span class="dots">...</span>';
                index++;
                setTimeout(typeSlogan, 70); 
            }
        }

        typeSlogan();

        
        setTimeout(function() {
            window.location.href = "login.php";
        }, 3000);
    </script>

</body>
</html>