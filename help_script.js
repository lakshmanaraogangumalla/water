let conversationHistory = [];
let userName = '';
let context = {};
let userLocation = {};
let orderDetails = [];  // To store order-related details

// Utility to display messages in the chat
function displayMessage(message, type) {
  const chatBox = document.getElementById("chat-box");
  const div = document.createElement("div");
  div.className = type;
  div.innerText = message;
  chatBox.appendChild(div);
  chatBox.scrollTop = chatBox.scrollHeight;
}

// Handle different intents based on user input
function getBotResponse(input) {
  input = input.toLowerCase();

  // Add to conversation history
  conversationHistory.push({ role: "user", message: input });

  // Handle greeting and user name
  if (input.includes("my name is") && !userName) {
    userName = input.split("my name is")[1].trim();
    return `Nice to meet you, ${userName}! How can I assist you today?`;
  }

  // Greet the user based on the time of day
  const currentTime = new Date();
  const hours = currentTime.getHours();
  if (input.includes("hello") || input.includes("hi")) {
    if (hours < 12) {
      return "Good Morning! How can I help you today?";
    } else if (hours < 18) {
      return "Good Afternoon! How can I assist you today?";
    } else {
      return "Good Evening! How can I assist you today?";
    }
  }

  // Display current date and time
  if (input.includes("date") || input.includes("time")) {
    const currentDate = currentTime.toLocaleDateString();
    const currentTimeStr = `${currentTime.getHours()}:${currentTime.getMinutes() < 10 ? '0' + currentTime.getMinutes() : currentTime.getMinutes()}`;
    return `Current date is ${currentDate}, and the time is ${currentTimeStr}.`;
  }

  // Fetch order-related details
  if (input.includes("order") || input.includes("billing")) {
    if (orderDetails.length > 0) {
      return `You have ${orderDetails.length} orders. Your most recent order was placed on ${orderDetails[0].order_date} for ${orderDetails[0].amount}.`;
    } else {
      return "You have no orders at the moment.";
    }
  }

  // Location-based assistance
  if (input.includes("location") || input.includes("where am i")) {
    if (userLocation.city) {
      return `You are in ${userLocation.city}, ${userLocation.country}.`;
    } else {
      return "Sorry, I am unable to detect your location. Can you tell me where you are?";
    }
  }

  // Fallback response
  return "Sorry, I didn't understand that. Can you please rephrase it?";
}

// Handle message sending
function sendMessage() {
  const inputField = document.getElementById("user-input");
  const userText = inputField.value.trim();
  if (userText === "") return;

  // Display user message
  displayMessage("You: " + userText, "user");

  // Get bot response and display it
  const botResponse = getBotResponse(userText);
  setTimeout(() => {
    displayMessage("Bot: " + botResponse, "bot");
    conversationHistory.push({ role: "bot", message: botResponse });
  }, 500);

  inputField.value = "";
}

// Voice input using Web Speech API
function startVoice() {
  const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
  recognition.lang = 'en-US';
  recognition.interimResults = false;
  recognition.maxAlternatives = 1;

  recognition.start();
  recognition.onresult = function(event) {
    const transcript = event.results[0][0].transcript;
    document.getElementById("user-input").value = transcript;
    sendMessage();
  };

  recognition.onerror = function(event) {
    alert('Voice recognition error: ' + event.error);
  };
}

// Initialize chatbot with a welcome message
document.addEventListener("DOMContentLoaded", function() {
  displayMessage("Bot: Hello! I'm your assistant. What's your name?", "bot");

  // Load order details for the user
  fetchOrderDetails();
});

// Fetch user order details from the backend
function fetchOrderDetails() {
  // Call PHP backend to get order details via AJAX (this can be an API call)
  fetch('get_order_details.php')
    .then(response => response.json())
    .then(data => {
      orderDetails = data.orders;
    });
}
