<!DOCTYPE html>
<html>
<head>
  <title>Audio Call</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Audio Call Interface</h2>
  <button id="callBtn">📞 Call Admin</button>
  <button id="answerBtn" style="display:none;">✅ Answer</button>
  <button id="endBtn" style="display:none;">❌ End Call</button>

  <audio id="remoteAudio" autoplay></audio>

  <script src="/socket.io/socket.io.js"></script>
  <script src="call.js"></script>
</body>
</html>
