<?php
// DB connection (adjust with your credentials)
$pdo = new PDO('mysql:host=localhost;dbname=login_system;charset=utf8', 'root', 'R@mu12072004', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Handle Save/Edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['entry_date'] ?? date('Y-m-d');
    $content = $_POST['content'] ?? '';

    // Check if entry exists
    $stmt = $pdo->prepare("SELECT id FROM diary_entries WHERE entry_date = ?");
    $stmt->execute([$date]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        // Update existing entry
        $stmt = $pdo->prepare("UPDATE diary_entries SET content = ? WHERE entry_date = ?");
        $stmt->execute([$content, $date]);
    } else {
        // Insert new entry
        $stmt = $pdo->prepare("INSERT INTO diary_entries (entry_date, content) VALUES (?, ?)");
        $stmt->execute([$date, $content]);
    }

    header("Location: notepad.php?date=$date");
    exit;
}

// Handle Delete action
if (isset($_GET['delete']) && isset($_GET['date'])) {
    $date = $_GET['date'];
    $stmt = $pdo->prepare("DELETE FROM diary_entries WHERE entry_date = ?");
    $stmt->execute([$date]);
    header("Location: notepad.php");
    exit;
}

// Get all diary entry dates (for flipbook pages)
$stmt = $pdo->query("SELECT entry_date FROM diary_entries ORDER BY entry_date ASC");
$dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Selected date to show or default to today
$currentDate = $_GET['date'] ?? date('Y-m-d');

// Get entry content for currentDate
$stmt = $pdo->prepare("SELECT content FROM diary_entries WHERE entry_date = ?");
$stmt->execute([$currentDate]);
$content = $stmt->fetchColumn() ?: '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Daily Diary Flipbook</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #fafafa;
    margin: 0; padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
  }
  h1 {
    margin: 20px 0;
  }

  /* Flipbook container */
  .flipbook {
    perspective: 2000px;
    width: 400px;
    height: 600px;
    position: relative;
  }

  /* Each page */
  .page {
    position: absolute;
    width: 100%;
    height: 100%;
    background: white;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    border-radius: 12px;
    padding: 30px 25px;
    box-sizing: border-box;
    backface-visibility: hidden;
    transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    overflow-y: auto;
  }

  .page textarea {
    width: 100%;
    height: 80%;
    font-size: 1.1em;
    border: none;
    resize: none;
    outline: none;
    font-family: 'Segoe UI', sans-serif;
  }

  .page .page-date {
    font-weight: 700;
    font-size: 1.2em;
    margin-bottom: 10px;
    color: #512da8;
  }

  /* Back page style */
  .page.back {
    background: #e0e0e0;
    transform: rotateY(180deg);
  }

  /* Flip states */
  .flipbook.flipped .page.front {
    transform: rotateY(-180deg);
    z-index: 1;
  }
  .flipbook.flipped .page.back {
    transform: rotateY(0deg);
    z-index: 2;
  }

  /* Controls */
  .controls {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 10px;
  }

  button, select {
    background: #512da8;
    color: white;
    border: none;
    padding: 10px 14px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  button:hover, select:hover {
    background: #311b92;
  }

  /* Delete link */
  .delete-link {
    color: #d32f2f;
    cursor: pointer;
    font-weight: 700;
    margin-left: 15px;
  }
</style>
</head>
<body>

<h1>Daily Diary Flipbook</h1>
<button onclick="window.location.href='admin_home.php'" style="margin-bottom: 15px; padding: 8px 16px; border:none; background:#512da8; color:#fff; border-radius:6px; cursor:pointer;">
  ← Back
</button>

<div class="flipbook" id="flipbook">

  <!-- Front page: View/Edit diary entry -->
  <div class="page front">
    <form method="post" action="notepad.php">
      <div class="page-date">Date: 
        <input type="date" name="entry_date" id="entry_date" value="<?= htmlspecialchars($currentDate) ?>" required />
      </div>
      <textarea name="content" placeholder="Write your diary entry here..."><?= htmlspecialchars($content) ?></textarea>

      <div style="margin-top: 10px;">
        <button type="submit">Save</button>

        <?php if ($content): ?>
          <a href="notepad.php?delete=1&date=<?= htmlspecialchars($currentDate) ?>" class="delete-link" onclick="return confirm('Delete this entry?')">Delete</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Back page: Flipbook pages list (date navigation) -->
  <div class="page back">
    <h3>Entries</h3>
    <select id="pageSelector" size="10" style="width:100%; font-size:1em;">
      <?php foreach ($dates as $d): ?>
        <option value="<?= $d ?>" <?= $d === $currentDate ? 'selected' : '' ?>><?= date('D, M j, Y', strtotime($d)) ?></option>
      <?php endforeach; ?>
    </select>
    <button id="flipBackBtn" style="margin-top: 10px; width: 100%;">Back to Entry</button>
  </div>

</div>

<div class="controls">
  <button id="flipBtn">Flip Page</button>
</div>

<script>
  const flipbook = document.getElementById('flipbook');
  const flipBtn = document.getElementById('flipBtn');
  const flipBackBtn = document.getElementById('flipBackBtn');
  const pageSelector = document.getElementById('pageSelector');
  const entryDateInput = document.getElementById('entry_date');

  flipBtn.addEventListener('click', () => {
    flipbook.classList.toggle('flipped');
  });

  flipBackBtn.addEventListener('click', () => {
    flipbook.classList.remove('flipped');
  });

  // When a date is selected in the entries list, reload the page for that date
  pageSelector.addEventListener('change', (e) => {
    const selectedDate = e.target.value;
    window.location.href = `notepad.php?date=${selectedDate}`;
  });

  // Also change the date input when flipping back
  flipbook.addEventListener('transitionend', () => {
    if (!flipbook.classList.contains('flipped')) {
      entryDateInput.focus();
    }
  });
</script>

</body>
</html>
