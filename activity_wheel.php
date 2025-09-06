<?php
// activity_wheel.php
include("dbconnect.php");
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit;
}

// ----- Helpers -----
function sanitize_options($raw) {
    // split by newlines or commas
    $parts = preg_split('/[\r\n,]+/', $raw);
    $opts = [];
    foreach ($parts as $p) {
        $t = trim($p);
        if ($t !== '') { $opts[] = $t; }
    }
    // unique but keep order
    $opts = array_values(array_unique($opts));
    return $opts;
}

function palette_color($i) {
    // pleasant distinct colors (no JS libraries, static list)
    $colors = [
        "#22c55e", "#06b6d4", "#8b5cf6", "#f97316", "#ef4444",
        "#0ea5e9", "#a3e635", "#eab308", "#ec4899", "#14b8a6",
        "#f59e0b", "#84cc16"
    ];
    return $colors[$i % count($colors)];
}

// ----- Initialize / restore session state -----
if (!isset($_SESSION['wheel'])) {
    $_SESSION['wheel'] = [
        'options'  => ['BYE','jai','ghumai','takai','pori'], // starter demo
        'accepted' => [],
        'last_index' => null,
        'last_rotation' => 0
    ];
}
$wheel = &$_SESSION['wheel'];

// ----- Handle actions -----
$notice = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'set_options') {
        $opts = sanitize_options($_POST['options'] ?? '');
        if (count($opts) < 2) {
            $notice = "At least 2 activities lagbe.";
        } else {
            $wheel['options'] = $opts;
            $wheel['accepted'] = [];  // reset results when options change
            $wheel['last_index'] = null;
            $wheel['last_rotation'] = 0;
        }
    } elseif ($action === 'spin') {
        // spin server-side: choose a random slice and compute rotation
        $n = max(2, count($wheel['options']));
        $anglePer = 360 / $n;

        $idx = random_int(0, $n - 1);
        $mid = $idx * $anglePer + $anglePer / 2; // degrees from 0° (pointing right)

        // pointer is at TOP (90°). We need the chosen mid to land at 90°.
        $turns = random_int(3, 6);
        $finalRot = $turns * 360 + (90 - $mid); // positive clockwise

        $wheel['last_index'] = $idx;
        $wheel['last_rotation'] = $finalRot;
    } elseif ($action === 'accept' && $wheel['last_index'] !== null) {
        $choice = $wheel['options'][$wheel['last_index']];
        if (!in_array($choice, $wheel['accepted'], true)) {
            $wheel['accepted'][] = $choice;
        }
    } elseif ($action === 'clear_results') {
        $wheel['accepted'] = [];
    }
}

// ----- Build wheel rendering data -----
$options = $wheel['options'];
$n = max(2, count($options));
$anglePer = 360 / $n;

// conic-gradient string
$parts = [];
$labels = [];
$start = 0.0;
for ($i = 0; $i < $n; $i++) {
    $end = $start + $anglePer;
    $color = palette_color($i);
    $parts[] = $color . " " . $start . "deg " . $end . "deg";
    $mid = $start + ($anglePer / 2);
    $labels[] = [
        'text' => htmlspecialchars($options[$i], ENT_QUOTES, 'UTF-8'),
        'angle' => $mid,
        'color' => $color
    ];
    $start = $end;
}
$gradient = implode(", ", $parts);

// rotation for the CSS animation
$finalRotation = isset($wheel['last_rotation']) ? (float)$wheel['last_rotation'] : 0.0;
$chosenText = $wheel['last_index'] !== null ? $options[$wheel['last_index']] : null;
$acceptedDisplay = implode(", ", array_map(fn($t)=>htmlspecialchars($t, ENT_QUOTES, 'UTF-8'), $wheel['accepted']));
$username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="activity_wheel.css">
<title>Chol | Activity Wheel</title>
<style>
    :root {
        --bg: #0b0b0f;
        --panel: #14141b;
        --panel-2: #1b1b25;
        --text: #e5e7eb;
        --muted: #9ca3af;
        --accent: #a855f7;
    }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial;
        background: radial-gradient(80% 60% at 50% 0%, #1b1b25, #0b0b0f 70%);
        color: var(--text);
        min-height: 100vh;
    }

    /* Navbar (reusing your style vibes) */
    nav { display:flex; align-items:center; justify-content:space-between; padding:16px 24px; background:#0f0f14; border-bottom:1px solid #23232f; position:sticky; top:0; z-index:10; }
    nav .logo { font-weight:800; letter-spacing:2px; color:#fff; }
    nav ul { display:flex; gap:18px; list-style:none; margin:0; padding:0; }
    nav a { color:#cbd5e1; text-decoration:none; font-weight:600; }
    nav a:hover { color:#fff; }

    .wrap { max-width:1200px; margin:40px auto; padding:0 16px; display:grid; grid-template-columns: 1.1fr 0.9fr; gap:28px; }

    .card {
        background: linear-gradient(180deg, var(--panel), var(--panel-2));
        border:1px solid #23232f;
        border-radius:16px;
        padding:20px;
        box-shadow: 0 15px 40px rgba(0,0,0,.35);
    }

    .title { margin:0 0 6px; font-size:28px; font-weight:800; }
    .sub { color:var(--muted); margin:0 0 16px; }

    /* Wheel */
    .wheel-area { display:grid; place-items:center; padding:24px; }
    .wheel {
        width: min(520px, 72vw);
        height: min(520px, 72vw);
        border-radius:50%;
        border:10px solid #0f0f14;
        position:relative;
        box-shadow: inset 0 0 0 4px rgba(255,255,255,0.06), 0 20px 50px rgba(0,0,0,.5);
        /* dynamic fill from PHP */
        background: conic-gradient(<?php echo $gradient; ?>);
        /* animation target using CSS var */
        transform: rotate(0deg);
        animation: spin 1.6s cubic-bezier(.2,.8,.2,1) forwards;
        --final-rot: <?php echo $finalRotation; ?>deg;
    }
    @keyframes spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(var(--final-rot)); }
    }
    /* pointer (top) */
    .pointer {
        position:absolute; top:-18px; left:50%; transform:translateX(-50%);
        width:0; height:0;
        border-left:14px solid transparent;
        border-right:14px solid transparent;
        border-bottom:22px solid #ffffff;
        filter: drop-shadow(0 6px 8px rgba(0,0,0,.45));
    }

    /* labels around the wheel */
    .labels { position:absolute; inset:0; pointer-events:none; }
    .label {
        position:absolute; top:50%; left:50%;
        transform-origin: 0 0; /* rotate from center */
        text-align:center;
        font-weight:800; font-size: clamp(12px, 1.6vw, 16px);
        text-shadow: 0 1px 2px rgba(0,0,0,.55);
        width: 42%;
    }
    .label span {
        display:inline-block; background:rgba(0,0,0,.25);
        padding:.28rem .5rem; border-radius:999px; backdrop-filter: blur(2px);
        border:1px solid rgba(255,255,255,.15);
    }

    /* Right side panel */
    .panel h3 { margin:0 0 10px; font-size:20px; }
    .panel .group { margin-bottom:18px; }
    .chip { display:inline-block; padding:.4rem .7rem; border-radius:999px; border:1px solid #2a2a39; margin:4px 6px 0 0; background:#12121a; }

    textarea, input[type="text"] {
        width:100%; background:#0f0f14; color:#e5e7eb; border:1px solid #272734;
        border-radius:10px; padding:10px 12px; resize:vertical; min-height: 88px;
    }
    .btnrow { display:flex; gap:10px; flex-wrap:wrap; margin-top:12px; }
    .btn {
        appearance:none; border:none; cursor:pointer; font-weight:800;
        padding:10px 14px; border-radius:12px; color:#0b0b0f; background:#a855f7;
        box-shadow: 0 8px 22px rgba(168,85,247,.35); transition: transform .07s ease;
    }
    .btn:hover { transform: translateY(-1px); }
    .btn.secondary { background:#0f0f14; color:#e5e7eb; border:1px solid #2b2b39; box-shadow:none; }
    .btn.ghost { background:transparent; color:#cbd5e1; border:1px dashed #2b2b39; }
    .notice { color:#fbbf24; font-weight:700; margin-top:8px; }
    .success { color:#34d399; font-weight:700; }

    /* footer info */
    .footer { text-align:center; color:#6b7280; margin:18px 0 6px; font-size:12px; }
    @media (max-width: 980px) {
        .wrap { grid-template-columns: 1fr; }
    }
    
</style>
</head>
<body>
    <nav>
        <div class="logo">CHOL</div>
        <ul>
            <li><a href="home.php">HOME</a></li>
            <li><a href="#">ABOUT US</a></li>
            <li><a href="logout.php">LOG OUT</a></li>
        </ul>
    </nav>

    <div class="wrap">
        <!-- Left: Wheel -->
        <section class="card wheel-area">
            <div class="wheel">
                <div class="pointer"></div>
                <div class="labels">
                    <?php
                    // place labels at ring ~ 78% radius from center
                    // math: rotate(mid) then translate to edge, then unrotate for readable text
                    foreach ($labels as $i => $L):
                        $ang = $L['angle'];
                        $tx = "transform: rotate(".$ang."deg) translate(calc(min(520px, 72vw)/2 - 52px)) rotate(-".$ang."deg);";
                    ?>
                        <div class="label" style="<?php echo $tx; ?>">
                            <span><?php echo $L['text']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="footer">
                Server chose a random slice; the wheel animates to it. Accept result to add it as a winner.
            </div>
        </section>

        <!-- Right: Controls & Result -->
        <aside class="card panel">
            <h2 class="title">Activity Wheel — DEKHI (Besties)</h2>
            <p class="sub">Logged in as <strong><?php echo $username; ?></strong></p>

            <div class="group">
                <h3>Chosen activity:</h3>
                <p style="font-size:20px; font-weight:800;"><?php echo $chosenText ? htmlspecialchars($chosenText, ENT_QUOTES, 'UTF-8') : '—'; ?></p>
                <form method="post" class="btnrow">
                    <input type="hidden" name="action" value="accept">
                    <button class="btn" type="submit" <?php echo $chosenText ? '' : 'disabled'; ?>>Accept Result</button>
                    <a class="btn secondary" href="#" onclick="this.closest('form').insertAdjacentHTML('afterend',''); return false;" style="pointer-events:none; opacity:.6;">Back</a>
                </form>
            </div>

            <div class="group">
                <h3>Included slices:</h3>
                <div>
                    <?php foreach ($options as $o): ?>
                        <span class="chip"><?php echo htmlspecialchars($o, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="group">
                <h3>Winners (accepted):</h3>
                <p class="<?php echo $acceptedDisplay ? 'success' : 'sub'; ?>">
                    <?php echo $acceptedDisplay !== '' ? $acceptedDisplay : 'Nothing accepted yet.'; ?>
                </p>
                <form method="post" class="btnrow">
                    <input type="hidden" name="action" value="clear_results">
                    <button class="btn ghost" type="submit">Clear Results</button>
                </form>
            </div>

            <hr style="border-color:#23232f; opacity:.6; margin:18px 0;">

            <form method="post">
                <h3>Enter / edit activities</h3>
                <p class="sub">Write each option on a new line (or comma separated). Minimum 2.</p>
                <textarea name="options" placeholder="e.g. BYE&#10;jai&#10;ghumai&#10;takai&#10;pori"><?php
                    echo htmlspecialchars(implode("\n", $options), ENT_QUOTES, 'UTF-8');
                ?></textarea>
                <div class="btnrow">
                    <button class="btn secondary" type="submit" name="action" value="set_options">Save Options</button>
                    <button class="btn" type="submit" name="action" value="spin">Spin Again</button>
                </div>
                <?php if ($notice): ?>
                    <div class="notice"><?php echo htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </form>
        </aside>
    </div>
</body>
</html>
