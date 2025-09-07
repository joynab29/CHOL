
<?php
include("dbconnect.php");
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit;
}

$username = $_SESSION['username'];
$userId = null;
try {
    if (isset($conn)) {
        $stmt = $conn->prepare("SELECT id FROM user WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($uid);
        if ($stmt->fetch()) $userId = $uid;
        $stmt->close();
    }
} catch (Throwable $e) {}

function parse_activities($text) {
    $raw = preg_split("/[\r\n,]+/", $text);
    $out = [];
    foreach ($raw as $r) {
        $t = trim($r);
        if ($t !== '') $out[] = $t;
    }
    if (count($out) > 24) $out = array_slice($out, 0, 24);
    return $out;
}

if (!isset($_SESSION['wheel_activities'])) {
    $_SESSION['wheel_activities'] = ['BYE','jai','ghumai','takai','pori'];
}
if (!isset($_SESSION['wheel_results'])) {
    $_SESSION['wheel_results'] = [];
}

$activities = $_SESSION['wheel_activities'];
$chosenIndex = null;
$chosenLabel = null;
$finalRotation = 0;
$eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['set_options'])) {
        $acts = parse_activities($_POST['activities_text'] ?? '');
        if (!empty($acts)) {
            $_SESSION['wheel_activities'] = $acts;
            $activities = $acts;
            unset($_SESSION['last_spin']);
        }
    } elseif (isset($_POST['spin'])) {
        if (!empty($activities)) {
            $n = count($activities);
            $slice = 360 / $n;

            $chosenIndex = random_int(0, $n - 1);
            $chosenLabel = $activities[$chosenIndex];

            // SVG 0° = RIGHT; pointer at TOP => -90°
            $midAngle = ($chosenIndex * $slice) + ($slice / 2);
            $pointerAngle = -90;
            $delta = fmod(($pointerAngle - $midAngle), 360.0);
            if ($delta < 0) $delta += 360.0;

            $fullSpins = random_int(2, 5);
            $finalRotation = ($fullSpins * 360) + $delta;

            $_SESSION['last_spin'] = [
                'index' => $chosenIndex,
                'label' => $chosenLabel,
                'rotation' => $finalRotation
            ];
        }
    } elseif (isset($_POST['accept'])) {
        // take label from the hidden input that mirrors the UI
        $label = trim($_POST['accepted_label'] ?? '');
        if ($label !== '') {
            $_SESSION['wheel_results'][] = $label;

            if ($eventId && isset($conn) && $userId) {
                try {
                    // ensure activity row
                    $aid = null;
                    $stmt = $conn->prepare("SELECT id FROM eventactivities WHERE event_id = ? AND label = ?");
                    $stmt->bind_param("is", $eventId, $label);
                    $stmt->execute();
                    $stmt->bind_result($aidFound);
                    if ($stmt->fetch()) $aid = $aidFound;
                    $stmt->close();

                    if ($aid === null) {
                        $stmt = $conn->prepare("INSERT INTO eventactivities (event_id, label, is_winner) VALUES (?, ?, 1)");
                        $stmt->bind_param("is", $eventId, $label);
                        $stmt->execute();
                        $aid = $stmt->insert_id;
                        $stmt->close();
                    } else {
                        $stmt = $conn->prepare("UPDATE eventactivities SET is_winner = 1 WHERE id = ?");
                        $stmt->bind_param("i", $aid);
                        $stmt->execute();
                        $stmt->close();
                    }

                    if ($aid) {
                        $stmt = $conn->prepare("INSERT INTO eventwinners (event_id, activity_id, chosen_by) VALUES (?, ?, ?)");
                        $stmt->bind_param("iii", $eventId, $aid, $userId);
                        $stmt->execute();
                        $stmt->close();
                    }
                } catch (Throwable $e) {}
            }
        }
    } elseif (isset($_POST['clear_results'])) {
        $_SESSION['wheel_results'] = [];
    }

    if (isset($_SESSION['last_spin'])) {
        $chosenIndex = $_SESSION['last_spin']['index'];
        $chosenLabel = $_SESSION['last_spin']['label'];
        $finalRotation = $_SESSION['last_spin']['rotation'];
    }
}

$palette = ["#22d3ee","#34d399","#f59e0b","#8b5cf6","#f472b6","#60a5fa","#f87171","#10b981","#fbbf24","#a78bfa"];

function polarToCartesian($cx,$cy,$r,$a){$rad=deg2rad($a);return[$cx+$r*cos($rad),$cy+$r*sin($rad)];}
function svgSlicePath($cx,$cy,$r,$sa,$ea){[$x1,$y1]=polarToCartesian($cx,$cy,$r,$sa);[$x2,$y2]=polarToCartesian($cx,$cy,$r,$ea);$la=($ea-$sa)>180?1:0;return "M $cx $cy L $x1 $y1 A $r $r 0 $la 1 $x2 $y2 Z";}

$size=380;$R=170;$labelR=100;$center=$size/2;
$n=max(1,count($activities));$sliceDeg=360/$n;

$activitiesSafe = array_map(fn($t)=>htmlspecialchars($t,ENT_QUOTES,'UTF-8'), $activities);
$resultsList = implode(', ', array_map(fn($t)=>htmlspecialchars($t,ENT_QUOTES,'UTF-8'), $_SESSION['wheel_results']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chol | Activity Wheel</title>
<link rel="stylesheet" href="activity_wheel.css">
</head>
<body>

<!-- Navbar -->
<nav>
  <div class="logo">CHOL</div>
  <ul>
    <li><a href="home.php">HOME</a></li>
    <li><a href="#">ABOUT US</a></li>
    <li><a href="logout.php">LOG OUT</a></li>
  </ul>
</nav>

<main class="container">
  <section class="left">
    <h1 class="title">Activity Wheel</h1>

    <!-- Update + Spin -->
    <form method="post" class="options">
      <label for="activities_text">Add activities (comma or newline separated):</label>
      <textarea id="activities_text" name="activities_text" rows="5" placeholder="e.g. BYE, jai, ghumai, takai, pori"><?php
        echo htmlspecialchars(implode(", ", $activities), ENT_QUOTES, 'UTF-8'); ?></textarea>
      <div class="row">
        <button type="submit" name="set_options" class="btn">Update Wheel</button>
        <button type="submit" name="spin" class="btn primary">Spin</button>
      </div>
    </form>

    <!-- Accept & Clear -->
    <form method="post" class="row" style="margin-top:10px;">
      <input type="hidden" name="accepted_label" value="<?php echo $chosenLabel ? htmlspecialchars($chosenLabel,ENT_QUOTES,'UTF-8') : ''; ?>">
      <button type="submit" name="accept" class="btn success">Accept Result</button>
      <button type="submit" name="clear_results" class="btn ghost">Clear Accepted</button>
    </form>

    <div class="chosen">
      <div class="label">Chosen activity:</div>
      <div class="value"><?php echo $chosenLabel ? htmlspecialchars($chosenLabel, ENT_QUOTES, 'UTF-8') : '—'; ?></div>
    </div>

    <div class="accepted">
      <div class="label">Accepted result<?php echo count($_SESSION['wheel_results'])!==1?'s':''; ?>:</div>
      <div class="value"><?php echo $resultsList!=='' ? $resultsList : '—'; ?></div>
    </div>
  </section>

  <section class="right">
    <div class="wheel-wrap">
      <!-- pointer stays on top -->
      <div class="pointer" aria-hidden="true"></div>

      <?php
        $slicesSVG=''; $labelsSVG='';
        for($i=0;$i<$n;$i++){
          $start=$i*$sliceDeg; $end=($i+1)*$sliceDeg; $color=$palette[$i%count($palette)];
          $path=svgSlicePath($center,$center,$R,$start,$end);
          $slicesSVG.='<path d="'.$path.'" fill="'.$color.'" stroke="#111827" stroke-width="1"></path>';
          $mid=$start+$sliceDeg/2; [$lx,$ly]=polarToCartesian($center,$center,$labelR,$mid);
          $text=$activitiesSafe[$i];
          $labelsSVG.='<text x="'.$lx.'" y="'.$ly.'" text-anchor="middle" dominant-baseline="middle" transform="rotate('.(-$mid).' '.$lx.' '.$ly.')" class="slice-label">'.$text.'</text>';
        }
        $rotationDeg=$finalRotation;
      ?>
      <svg class="wheel <?php echo $rotationDeg ? 'spin' : ''; ?>"
           viewBox="0 0 <?php echo $size; ?> <?php echo $size; ?>"
           style="--spin-deg: <?php echo $rotationDeg; ?>deg;">
        <g>
          <?php echo $slicesSVG; ?>
          <?php echo $labelsSVG; ?>
          <circle cx="<?php echo $center; ?>" cy="<?php echo $center; ?>" r="18" fill="#111827"></circle>
        </g>
      </svg>
    </div>

    <div class="legend">
      <div class="legend-title">Included slices:</div>
      <ul>
        <?php foreach ($activitiesSafe as $t): ?>
          <li><?php echo $t; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
</main>
</body>
</html>
