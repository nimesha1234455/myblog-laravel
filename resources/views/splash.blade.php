<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBlog</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            overflow: hidden;
            font-family: Arial, sans-serif;
        }

        .splash-screen {
            position: fixed;
            inset: 0;
            z-index: 9999;

            display: flex;
            justify-content: center;
            align-items: center;

            background:
                radial-gradient(circle at center,
                rgba(80, 70, 180, 0.25),
                transparent 35%),
                linear-gradient(135deg, #070b18, #11172d, #17102b);

            transition:
                opacity 0.8s ease,
                transform 0.8s ease;
        }

        .splash-content {
            text-align: center;
            animation: contentIn 1s ease forwards;
        }

        /* Logo */
        .logo-box {
            width: 90px;
            height: 90px;
            margin: auto;

            display: flex;
            justify-content: center;
            align-items: center;

            border-radius: 24px;

            background: linear-gradient(
                135deg,
                #6366f1,
                #8b5cf6
            );

            box-shadow:
                0 0 25px rgba(99, 102, 241, 0.5),
                0 0 60px rgba(139, 92, 246, 0.25);

            animation:
                logoIn 1s ease,
                logoGlow 2s ease-in-out infinite 1s;
        }

        .logo-icon {
            font-size: 42px;
            color: white;
            transform: rotate(-10deg);
        }

        /* Brand */
        .brand {
            margin-top: 22px;

            color: white;
            font-size: 38px;
            font-weight: 700;
            letter-spacing: 1px;

            animation: fadeUp 1s ease 0.5s both;
        }

        .tagline {
            margin-top: 8px;

            color: #aeb7d4;
            font-size: 15px;
            letter-spacing: 2px;

            animation: fadeUp 1s ease 0.8s both;
        }

        /* Loading */
        .loader {
            width: 220px;
            height: 4px;

            margin: 30px auto 0;

            overflow: hidden;
            border-radius: 10px;

            background: rgba(255,255,255,0.12);

            animation: fadeUp 1s ease 1s both;
        }

        .loader-bar {
            width: 0%;
            height: 100%;

            border-radius: 10px;

            background: linear-gradient(
                90deg,
                #6366f1,
                #a855f7
            );

            box-shadow: 0 0 12px #6366f1;

            animation: loading 4s linear forwards;
        }

        .loading-text {
            margin-top: 12px;

            color: #7f8aad;
            font-size: 12px;
            letter-spacing: 1px;

            animation: fadeUp 1s ease 1.2s both;
        }

        /* Fade out */
        .splash-screen.hide {
            opacity: 0;
            transform: scale(1.04);
            pointer-events: none;
        }

        /* Animations */

        @keyframes logoIn {
            0% {
                opacity: 0;
                transform: scale(0.5) rotate(-15deg);
            }

            100% {
                opacity: 1;
                transform: scale(1) rotate(0);
            }
        }

        @keyframes logoGlow {
            0%, 100% {
                box-shadow:
                    0 0 25px rgba(99,102,241,.5),
                    0 0 60px rgba(139,92,246,.25);
            }

            50% {
                box-shadow:
                    0 0 35px rgba(99,102,241,.8),
                    0 0 90px rgba(139,92,246,.4);
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes loading {
            from {
                width: 0%;
            }

            to {
                width: 100%;
            }
        }

        @keyframes contentIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

<div class="splash-screen" id="splash">

    <div class="splash-content">

        <div class="logo-box">
            <div class="logo-icon">✎</div>
        </div>

        <div class="brand">
            MyBlog
        </div>

        <div class="tagline">
            Write. Share. Inspire.
        </div>

        <div class="loader">
            <div class="loader-bar"></div>
        </div>

        <div class="loading-text">
            Loading...
        </div>

    </div>

</div>


<script>

    window.addEventListener("load", function () {

        setTimeout(function () {

            const splash =
                document.getElementById("splash");

            splash.classList.add("hide");

            setTimeout(function () {

                window.location.href =
                    "{{ route('posts.index') }}";

            }, 800);

        }, 4200);

    });

</script>

</body>
</html>