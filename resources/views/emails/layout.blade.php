<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Campus Digitale Forma' }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #14161A;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #0B3B5E 0%, #00A7B7 100%);
            padding: 32px 24px;
            text-align: center;
        }
        .logo {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            text-decoration: none;
        }
        .tagline {
            color: #ffffff;
            font-size: 14px;
            margin: 8px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 32px 24px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0B3B5E;
            margin: 0 0 16px 0;
        }
        .message {
            font-size: 16px;
            color: #334155;
            margin: 0 0 24px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #0B3B5E 0%, #00A7B7 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 16px 0;
            transition: transform 0.2s ease;
        }
        .cta-button:hover {
            transform: translateY(-1px);
        }
        .course-card {
            background-color: #F4F1EA;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .course-title {
            font-size: 18px;
            font-weight: 600;
            color: #0B3B5E;
            margin: 0 0 8px 0;
        }
        .course-description {
            font-size: 14px;
            color: #64748B;
            margin: 0 0 12px 0;
        }
        .progress-bar {
            background-color: #E2E8F0;
            border-radius: 4px;
            height: 8px;
            overflow: hidden;
            margin: 8px 0;
        }
        .progress-fill {
            background: linear-gradient(90deg, #00A7B7 0%, #FFC857 100%);
            height: 100%;
            transition: width 0.3s ease;
        }
        .footer {
            background-color: #F4F1EA;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #E2E8F0;
        }
        .footer-text {
            font-size: 14px;
            color: #64748B;
            margin: 0 0 8px 0;
        }
        .social-links {
            margin: 16px 0 0 0;
        }
        .social-link {
            display: inline-block;
            margin: 0 8px;
            color: #00A7B7;
            text-decoration: none;
            font-size: 14px;
        }
        .unsubscribe {
            font-size: 12px;
            color: #94A3B8;
            margin: 16px 0 0 0;
        }
        .unsubscribe a {
            color: #94A3B8;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .header, .content, .footer {
                padding: 24px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="logo">Campus Digitale Forma</h1>
            <p class="tagline">Trasforma l'apprendimento in valore</p>
        </div>
        
        <div class="content">
            @yield('content')
        </div>
        
        <div class="footer">
            <p class="footer-text">
                <strong>Campus Digitale Forma</strong><br>
                La tua piattaforma di formazione online
            </p>
            
            <div class="social-links">
                <a href="#" class="social-link">Sito Web</a>
                <a href="#" class="social-link">Supporto</a>
                <a href="#" class="social-link">Privacy</a>
            </div>
            
            <p class="unsubscribe">
                Ricevi questa email perché hai un account su Campus Digitale Forma.<br>
                <a href="{{ url('/unsubscribe') }}">Disiscriviti</a> dalle notifiche email.
            </p>
        </div>
    </div>
</body>
</html>
