<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #fff;
            background:
                radial-gradient(circle at 15% 40%, rgba(255, 107, 138, .28), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(255, 107, 138, .18), transparent 45%),
                #1f2133;
        }
        .card {
            width: 100%;
            max-width: 480px;
            background: rgba(30, 30, 45, .85);
            border: 1px solid rgba(255, 107, 138, .25);
            border-radius: 28px;
            padding: 48px 40px 32px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .45);
        }
        .logo {
            width: 96px; height: 96px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #ff6b8a;
            display: flex; align-items: center; justify-content: center;
            font-size: 38px;
            box-shadow: 0 0 30px rgba(255, 107, 138, .55);
        }
        h1 { text-align: center; font-size: 28px; font-weight: 700; }
        .subtitle { text-align: center; color: #ffa9bd; font-size: 14px; margin: 8px 0 32px; }

        .alert {
            background: rgba(239, 68, 68, .15);
            border: 1px solid rgba(239, 68, 68, .4);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .field { margin-bottom: 22px; }
        label {
            display: block;
            font-size: 12px; font-weight: 600;
            letter-spacing: .08em; text-transform: uppercase;
            color: #ffa9bd;
            margin-bottom: 8px;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 18px; top: 50%;
            transform: translateY(-50%);
            color: #ff6b8a;
        }
        input {
            width: 100%;
            padding: 16px 18px 16px 50px;
            font: inherit; font-size: 16px;
            color: #fff;
            background: #1a1a28;
            border: 1px solid rgba(255, 107, 138, .25);
            border-radius: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus {
            border-color: #ff6b8a;
            box-shadow: 0 0 0 3px rgba(255, 107, 138, .2);
        }
        /* cegah warna putih/biru bawaan browser saat autofill */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px #1a1a28 inset;
            -webkit-text-fill-color: #fff;
        }
        button {
            width: 100%;
            padding: 16px;
            margin-top: 6px;
            font: inherit; font-size: 16px; font-weight: 700;
            text-transform: uppercase;
            color: #fff;
            background: linear-gradient(135deg, #ff8fa8, #ff5c7c);
            border: none;
            border-radius: 14px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(255, 92, 124, .35);
            transition: transform .15s;
        }
        button:hover { transform: translateY(-2px); }
        .footer { text-align: center; color: #8a8aa0; font-size: 13px; margin-top: 28px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"><i class="fa-solid fa-cash-register"></i></div>
        <h1>POS SYSTEM</h1>
        <p class="subtitle">Sistem Kasir Minimarket Retail</p>

        @if ($errors->any())
            <div class="alert">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}" placeholder="nama@indomaret.com"
                           required autofocus>
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-key"></i>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit">
                Masuk ke Sistem <i class="fa-solid fa-right-to-bracket"></i>
            </button>
        </form>

        <p class="footer">© 2026 Indomaret POS Application. All Rights Reserved.</p>
    </div>
</body>
</html>