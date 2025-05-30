const chatBox = document.getElementById("chat-box");
const userInput = document.getElementById("user-input");

// Load existing chat messages from backend
function loadMessages() {
  fetch("get_admin_messages.php")
    .then(response => response.json())
    .then(messages => {
      chatBox.innerHTML = "";
      messages.forEach(msg => {
        appendMessage(msg.message, msg.sender, msg.timestamp);
      });
      scrollToBottom();
    })
    .catch(error => {
      console.error("Error loading messages:", error);
    });
}

// Append a message to the chat box
function appendMessage(message, sender, timestamp = "") {
  const msgDiv = document.createElement("div");
  const time = timestamp ? ` (${timestamp})` : "";

  msgDiv.textContent = `${sender === "admin" ? "🛡️ Admin" : "👤 User"}: ${message}${time}`;
  msgDiv.style.marginBottom = "10px";
  msgDiv.style.padding = "5px 10px";
  msgDiv.style.borderRadius = "8px";
  msgDiv.style.maxWidth = "90%";
  msgDiv.style.wordWrap = "break-word";
  msgDiv.style.backgroundColor = sender === "admin" ? "#003f5c" : "#005d7a";
  msgDiv.style.alignSelf = sender === "admin" ? "flex-end" : "flex-start";
  msgDiv.style.color = "#fff";

  chatBox.appendChild(msgDiv);
}

// Send admin message
function sendMessage() {
  const message = userInput.value.trim();
  if (message === "") return;

  appendMessage(message, "admin");

  fetch("send_admin_message.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ message: message })
  })
    .then(response => response.json())
    .then(data => {
      if (!data.success) {
        alert("Message not sent. Please try again.");
      }
    })
    .catch(error => {
      console.error("Error sending message:", error);
    });

  userInput.value = "";
  scrollToBottom();
}

// Voice recognition
function startVoice() {
  if (!("webkitSpeechRecognition" in window)) {
    alert("Speech recognition is not supported in this browser.");
    return;
  }

  const recognition = new webkitSpeechRecognition();
  recognition.lang = "en-IN";
  recognition.continuous = false;
  recognition.interimResults = false;

  recognition.start();

  recognition.onresult = (event) => {
    const transcript = event.results[0][0].transcript;
    userInput.value = transcript;
  };

  recognition.onerror = (event) => {
    console.error("Speech recognition error:", event.error);
  };
}

// Auto-scroll to bottom
function scrollToBottom() {
  chatBox.scrollTop = chatBox.scrollHeight;
}

// Load messages on page load
document.addEventListener("DOMContentLoaded", loadMessages);
