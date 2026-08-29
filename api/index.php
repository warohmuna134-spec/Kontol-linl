<?php
// ============================================================
// MALZ BYPASS URL - XEMOZ API (ULTIMATE FIX)
// Support: SFL.gl, Linkvertise, YorURL, LootLabs, dll
// ============================================================

$bypass_result = '';
$error_message = '';
$original_url = '';
$loading = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $original_url = trim($_POST['url']);
    $loading = true;
    
    if (filter_var($original_url, FILTER_VALIDATE_URL)) {
        $api_endpoint = 'https://api-xemoz-official.my.id/api/tools/bypasslink_izenlol.php?url=' . urlencode($original_url);
        
        $response = false;
        $httpCode = 0;
        $curl_error = '';

        // Metode 1: cURL dengan Header Lengkap (Anti-Block)
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $api_endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 25,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json, text/plain, */*',
                    'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Cache-Control: no-cache'
                ]
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
        }

        // Metode 2: Fallback ke file_get_contents jika cURL gagal / dibatasi Vercel
        if ($response === false || $httpCode !== 200) {
            $opts = [
                "http" => [
                    "method" => "GET",
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n" .
                                "Accept: application/json\r\n",
                    "timeout" => 20,
                    "ignore_errors" => true
                ],
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false
                ]
            ];
            $context = stream_context_create($opts);
            $fallbackResponse = @file_get_contents($api_endpoint, false, $context);
            if ($fallbackResponse !== false) {
                $response = $fallbackResponse;
                $httpCode = 200;
            }
        }

        // Parsing Data JSON
        if ($response && ($httpCode === 200 || !empty($response))) {
            $data = json_decode($response, true);
            
            if (is_array($data)) {
                // Ekstraksi berbagai kemungkinan struktur response dari API
                if (!empty($data['result']['destination_url']) && filter_var($data['result']['destination_url'], FILTER_VALIDATE_URL)) {
                    $bypass_result = $data['result']['destination_url'];
                } elseif (!empty($data['destination']) && filter_var($data['destination'], FILTER_VALIDATE_URL)) {
                    $bypass_result = $data['destination'];
                } elseif (!empty($data['url']) && filter_var($data['url'], FILTER_VALIDATE_URL)) {
                    $bypass_result = $data['url'];
                } elseif (!empty($data['result']) && is_string($data['result']) && filter_var($data['result'], FILTER_VALIDATE_URL)) {
                    $bypass_result = $data['result'];
                } elseif (!empty($data['bypass']) && filter_var($data['bypass'], FILTER_VALIDATE_URL)) {
                    $bypass_result = $data['bypass'];
                } elseif (!empty($data['link']) && filter_var($data['link'], FILTER_VALIDATE_URL)) {
                    $bypass_result = $data['link'];
                } else {
                    $msg = isset($data['message']) ? $data['message'] : (isset($data['error']) ? $data['error'] : 'URL tidak ditemukan dalam respon.');
                    $error_message = 'Gagal Bypass: ' . htmlspecialchars($msg);
                }
            } else {
                $error_message = 'Respon API tidak sah atau berupa teks kosong. API Xemoz mungkin sedang down.';
            }
        } else {
            $error_message = 'Koneksi ke API gagal (HTTP: ' . $httpCode . ($curl_error ? ' | Err: ' . htmlspecialchars($curl_error) : '') . '). IP server kemungkinan diblokir oleh provider API.';
        }
    } else {
        $error_message = 'URL tidak sah. Sila masukkan URL yang betul.';
    }
    $loading = false;
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Malz Bypass URL - Bypass Semua Short Link</title>
    <link rel="icon" type="image/png" href="/images/logo-web.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0a0a0a;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(255,255,255,0.02) 0%, rgba(0,0,0,0.95) 100%);
            pointer-events: none;
            z-index: 1;
        }
        .container {
            max-width: 720px;
            width: 100%;
            padding: 48px 36px;
            background: rgba(10,10,10,0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 32px;
            position: relative;
            z-index: 10;
            animation: fadeIn 0.6s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo { text-align: center; margin-bottom: 12px; }
        .logo img {
            width: 80px;
            height: 80px;
            border-radius: 24px;
            object-fit: cover;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 8px;
        }
        .logo h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff 0%, #888888 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .logo span {
            color: rgba(255,255,255,0.3);
            font-size: 0.75rem;
            display: block;
            margin-top: 4px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            color: rgba(255,255,255,0.4);
            font-size: 0.85rem;
            margin-bottom: 28px;
            line-height: 1.7;
            font-weight: 300;
        }
        .subtitle strong { color: rgba(255,255,255,0.6); font-weight: 600; }
        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .input-group input {
            flex: 1;
            padding: 16px 22px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 60px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            min-width: 200px;
            transition: all 0.3s;
            font-family: inherit;
        }
        .input-group input::placeholder { color: rgba(255,255,255,0.25); }
        .input-group input:focus {
            border-color: rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.06);
            box-shadow: 0 0 30px rgba(255,255,255,0.03);
        }
        .input-group button {
            padding: 16px 36px;
            background: linear-gradient(135deg, #ffffff 0%, #999999 100%);
            border: none;
            border-radius: 60px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            color: #000000;
            white-space: nowrap;
            transition: all 0.3s;
            font-family: inherit;
        }
        .input-group button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(255,255,255,0.15);
        }
        .input-group button:active { transform: scale(0.96); }
        .input-group button:disabled { opacity: 0.5; cursor: not-allowed; }
        .error {
            background: rgba(255,68,68,0.08);
            border: 1px solid rgba(255,68,68,0.12);
            border-radius: 16px;
            padding: 14px 20px;
            color: #ff6666;
            font-size: 0.85rem;
            margin-bottom: 16px;
            text-align: center;
        }
        .result-box {
            margin-top: 20px;
            padding: 20px 24px;
            background: rgba(255,255,255,0.03);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.06);
            animation: fadeIn 0.4s ease-out;
        }
        .result-label {
            color: rgba(255,255,255,0.35);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
        }
        .result-url {
            color: #66ff66;
            font-size: 0.85rem;
            word-break: break-all;
            margin-bottom: 16px;
            background: rgba(0,0,0,0.4);
            padding: 14px 18px;
            border-radius: 12px;
            font-family: 'Courier New', monospace;
            border: 1px solid rgba(102,255,102,0.06);
            line-height: 1.6;
        }
        .button-group { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn {
            flex: 1;
            background: rgba(255,255,255,0.06);
            color: #ffffff;
            padding: 11px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
            font-family: inherit;
            min-width: 100px;
        }
        .btn:hover { background: rgba(255,255,255,0.12); transform: translateY(-2px); }
        .btn-success { background: rgba(102,255,102,0.06); border-color: rgba(102,255,102,0.1); }
        .btn-success:hover { background: rgba(102,255,102,0.12); }
        .btn-primary { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08); }
        .btn-primary:hover { background: rgba(255,255,255,0.12); }
        .loading {
            display: <?php echo $loading ? 'block' : 'none'; ?>;
            margin: 16px 0;
            text-align: center;
        }
        .spinner {
            width: 30px;
            height: 30px;
            border: 2px solid rgba(255,255,255,0.06);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { color: rgba(255,255,255,0.3); font-size: 0.8rem; }
        .feature-list {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 24px;
            justify-content: center;
        }
        .feature-item {
            background: rgba(255,255,255,0.02);
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 0.65rem;
            color: rgba(255,255,255,0.3);
            border: 1px solid rgba(255,255,255,0.04);
            letter-spacing: 0.5px;
        }
        .footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.04);
            font-size: 0.65rem;
            color: rgba(255,255,255,0.15);
            letter-spacing: 0.5px;
        }
        .footer a { color: rgba(255,255,255,0.2); text-decoration: none; transition: color 0.3s; }
        .footer a:hover { color: rgba(255,255,255,0.4); }
        .back-link {
            display: inline-block;
            margin-top: 12px;
            color: rgba(255,255,255,0.15);
            text-decoration: none;
            font-size: 0.75rem;
            transition: color 0.3s;
        }
        .back-link:hover { color: rgba(255,255,255,0.35); }

        @media (max-width: 600px) {
            .container { padding: 28px 18px; }
            .input-group { flex-direction: column; }
            .input-group button { width: 100%; justify-content: center; }
            .logo h1 { font-size: 1.5rem; }
            .logo img { width: 60px; height: 60px; }
            .feature-list { gap: 6px; }
            .feature-item { font-size: 0.6rem; padding: 4px 12px; }
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="logo">
            <img src="/images/logo-web.png" alt="Malz Officially" onerror="this.style.display='none'">
            <h1>Malz Bypass URL</h1>
            <span>Bypass Semua Short Link</span>
        </div>

        <div class="subtitle">
            Bypass <strong>SFL.gl</strong> &middot; <strong>Linkvertise</strong> &middot; <strong>LootLabs</strong> &middot; <strong>Work.ink</strong> &middot; <strong>YorURL</strong> &middot; <strong>Dan lain-lain</strong>
        </div>

        <?php if ($error_message): ?>
        <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" id="bypassForm">
            <div class="input-group">
                <input type="url" name="url" id="urlInput" placeholder="https://sfl.gl/xxxxx" value="<?php echo htmlspecialchars($original_url); ?>" required autofocus>
                <button type="submit" id="bypassBtn">Bypass</button>
            </div>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <div class="loading-text">Memproses bypass link...</div>
        </div>

        <?php if ($bypass_result): ?>
        <div class="result-box" id="resultBox">
            <div class="result-label">Link Asli</div>
            <div class="result-url" id="resultUrl"><?php echo htmlspecialchars($bypass_result); ?></div>
            <div class="button-group">
                <button class="btn btn-success" onclick="copyResult()">Salin URL</button>
                <a href="<?php echo htmlspecialchars($bypass_result); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">Buka Link</a>
            </div>
        </div>
        <?php endif; ?>

        <div class="feature-list">
            <span class="feature-item">Bypass Iklan</span>
            <span class="feature-item">Bebas Shortlink</span>
            <span class="feature-item">Cepat & Mudah</span>
            <span class="feature-item">Support All Device</span>
        </div>

        <a href="/" class="back-link">← Kembali ke Beranda</a>

        <div class="footer">
            &copy; 2026 <a href="https://malz-official.biz.id">Malz Officially</a> &middot; Xemoz API
        </div>

    </div>

    <script>
        document.getElementById('bypassForm').addEventListener('submit', function() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('bypassBtn').disabled = true;
        });

        function copyResult() {
            const url = document.getElementById('resultUrl').textContent;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    showToast('URL disalin!');
                }).catch(() => {
                    fallbackCopy(url);
                });
            } else {
                fallbackCopy(url);
            }
        }

        function fallbackCopy(text) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy');
                showToast('URL disalin!');
            } catch (e) {
                showToast('Gagal salin. Salin manual.');
            }
            document.body.removeChild(ta);
        }

        function showToast(msg) {
            const existing = document.querySelector('.toast-message');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = 'toast-message';
            toast.textContent = msg;
            Object.assign(toast.style, {
                position: 'fixed',
                bottom: '30px',
                left: '50%',
                transform: 'translateX(-50%)',
                background: 'rgba(0,0,0,0.9)',
                backdropFilter: 'blur(12px)',
                color: '#66ff66',
                padding: '12px 28px',
                borderRadius: '60px',
                fontSize: '0.85rem',
                border: '1px solid rgba(102,255,102,0.15)',
                zIndex: '999',
                animation: 'fadeIn 0.3s ease-out',
                fontFamily: '-apple-system, BlinkMacSystemFont, sans-serif'
            });
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.5s ease';
                setTimeout(() => toast.remove(), 500);
            }, 2500);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const urlParam = urlParams.get('url');
            if (urlParam) {
                document.getElementById('urlInput').value = urlParam;
                document.getElementById('bypassForm').submit();
            }
        });
    </script>
</body>
</html>
            margin-bottom: 12px;
        }
        .logo img {
            width: 80px;
            height: 80px;
            border-radius: 24px;
            object-fit: cover;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 8px;
        }
        .logo h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff 0%, #888888 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .logo span {
            color: rgba(255,255,255,0.3);
            font-size: 0.75rem;
            display: block;
            margin-top: 4px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            color: rgba(255,255,255,0.4);
            font-size: 0.85rem;
            margin-bottom: 28px;
            line-height: 1.7;
            font-weight: 300;
        }
        .subtitle strong {
            color: rgba(255,255,255,0.6);
            font-weight: 600;
        }
        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .input-group input {
            flex: 1;
            padding: 16px 22px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 60px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            min-width: 200px;
            transition: all 0.3s;
            font-family: inherit;
        }
        .input-group input::placeholder {
            color: rgba(255,255,255,0.25);
        }
        .input-group input:focus {
            border-color: rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.06);
            box-shadow: 0 0 30px rgba(255,255,255,0.03);
        }
        .input-group button {
            padding: 16px 36px;
            background: linear-gradient(135deg, #ffffff 0%, #999999 100%);
            border: none;
            border-radius: 60px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            color: #000000;
            white-space: nowrap;
            transition: all 0.3s;
            font-family: inherit;
        }
        .input-group button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(255,255,255,0.15);
        }
        .input-group button:active {
            transform: scale(0.96);
        }
        .input-group button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .error {
            background: rgba(255,68,68,0.08);
            border: 1px solid rgba(255,68,68,0.12);
            border-radius: 16px;
            padding: 14px 20px;
            color: #ff6666;
            font-size: 0.85rem;
            margin-bottom: 16px;
            text-align: center;
        }
        .result-box {
            margin-top: 20px;
            padding: 20px 24px;
            background: rgba(255,255,255,0.03);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.06);
            animation: fadeIn 0.4s ease-out;
        }
        .result-label {
            color: rgba(255,255,255,0.35);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
        }
        .result-url {
            color: #66ff66;
            font-size: 0.85rem;
            word-break: break-all;
            margin-bottom: 16px;
            background: rgba(0,0,0,0.4);
            padding: 14px 18px;
            border-radius: 12px;
            font-family: 'Courier New', monospace;
            border: 1px solid rgba(102,255,102,0.06);
            line-height: 1.6;
        }
        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn {
            flex: 1;
            background: rgba(255,255,255,0.06);
            color: #ffffff;
            padding: 11px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
            font-family: inherit;
            min-width: 100px;
        }
        .btn:hover {
            background: rgba(255,255,255,0.12);
            transform: translateY(-2px);
        }
        .btn-success {
            background: rgba(102,255,102,0.06);
            border-color: rgba(102,255,102,0.1);
        }
        .btn-success:hover {
            background: rgba(102,255,102,0.12);
        }
        .btn-primary {
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.08);
        }
        .btn-primary:hover {
            background: rgba(255,255,255,0.12);
        }
        .loading {
            display: <?php echo $loading ? 'block' : 'none'; ?>;
            margin: 16px 0;
            text-align: center;
        }
        .spinner {
            width: 30px;
            height: 30px;
            border: 2px solid rgba(255,255,255,0.06);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading-text {
            color: rgba(255,255,255,0.3);
            font-size: 0.8rem;
        }
        .feature-list {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 24px;
            justify-content: center;
        }
        .feature-item {
            background: rgba(255,255,255,0.02);
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 0.65rem;
            color: rgba(255,255,255,0.3);
            border: 1px solid rgba(255,255,255,0.04);
            letter-spacing: 0.5px;
        }
        .footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.04);
            font-size: 0.65rem;
            color: rgba(255,255,255,0.15);
            letter-spacing: 0.5px;
        }
        .footer a {
            color: rgba(255,255,255,0.2);
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: rgba(255,255,255,0.4);
        }
        .back-link {
            display: inline-block;
            margin-top: 12px;
            color: rgba(255,255,255,0.15);
            text-decoration: none;
            font-size: 0.75rem;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: rgba(255,255,255,0.35);
        }

        @media (max-width: 600px) {
            .container { padding: 28px 18px; }
            .input-group { flex-direction: column; }
            .input-group button { width: 100%; justify-content: center; }
            .logo h1 { font-size: 1.5rem; }
            .logo img { width: 60px; height: 60px; }
            .feature-list { gap: 6px; }
            .feature-item { font-size: 0.6rem; padding: 4px 12px; }
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="logo">
            <img src="/images/logo-web.png" alt="Malz Officially" onerror="this.style.display='none'">
            <h1>Malz Bypass URL</h1>
            <span>Bypass Semua Short Link</span>
        </div>

        <div class="subtitle">
            Bypass <strong>SFL.gl</strong> &middot; <strong>Linkvertise</strong> &middot; <strong>LootLabs</strong> &middot; <strong>Work.ink</strong> &middot; <strong>YorURL</strong> &middot; <strong>Dan lain-lain</strong>
        </div>

        <?php if ($error_message): ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" id="bypassForm">
            <div class="input-group">
                <input type="url" name="url" id="urlInput" placeholder="https://sfl.gl/xxxxx" value="<?php echo htmlspecialchars($original_url); ?>" required autofocus>
                <button type="submit" id="bypassBtn">Bypass</button>
            </div>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <div class="loading-text">Memproses bypass link...</div>
        </div>

        <?php if ($bypass_result): ?>
        <div class="result-box" id="resultBox">
            <div class="result-label">Link Asli</div>
            <div class="result-url" id="resultUrl"><?php echo htmlspecialchars($bypass_result); ?></div>
            <div class="button-group">
                <button class="btn btn-success" onclick="copyResult()">Salin URL</button>
                <a href="<?php echo htmlspecialchars($bypass_result); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">Buka Link</a>
            </div>
        </div>
        <?php endif; ?>

        <div class="feature-list">
            <span class="feature-item">Bypass Iklan</span>
            <span class="feature-item">Bebas Shortlink</span>
            <span class="feature-item">Cepat & Mudah</span>
            <span class="feature-item">Support All Device</span>
        </div>

        <a href="/" class="back-link">← Kembali ke Beranda</a>

        <div class="footer">
            &copy; 2026 <a href="https://malz-official.biz.id">Malz Officially</a> &middot; Xemoz API
        </div>

    </div>

    <script>
        document.getElementById('bypassForm').addEventListener('submit', function() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('bypassBtn').disabled = true;
        });

        function copyResult() {
            const url = document.getElementById('resultUrl').textContent;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    showToast('URL disalin!');
                }).catch(() => {
                    fallbackCopy(url);
                });
            } else {
                fallbackCopy(url);
            }
        }

        function fallbackCopy(text) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy');
                showToast('URL disalin!');
            } catch (e) {
                showToast('Gagal salin. Salin manual.');
            }
            document.body.removeChild(ta);
        }

        function showToast(msg) {
            const existing = document.querySelector('.toast-message');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = 'toast-message';
            toast.textContent = msg;
            Object.assign(toast.style, {
                position: 'fixed',
                bottom: '30px',
                left: '50%',
                transform: 'translateX(-50%)',
                background: 'rgba(0,0,0,0.9)',
                backdropFilter: 'blur(12px)',
                color: '#66ff66',
                padding: '12px 28px',
                borderRadius: '60px',
                fontSize: '0.85rem',
                border: '1px solid rgba(102,255,102,0.15)',
                zIndex: '999',
                animation: 'fadeIn 0.3s ease-out',
                fontFamily: '-apple-system, BlinkMacSystemFont, sans-serif'
            });
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.5s ease';
                setTimeout(() => toast.remove(), 500);
            }, 2500);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const urlParam = urlParams.get('url');
            if (urlParam) {
                document.getElementById('urlInput').value = urlParam;
                document.getElementById('bypassForm').submit();
            }
        });

        console.log('✅ Malz Bypass URL - Xemoz API (Fixed)');
        console.log('🔗 API: bypasslink_izenlol.php');
    </script>

</body>
</html>
