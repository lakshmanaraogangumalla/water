const socket = io("http://localhost:3000"); // Initialize socket connection
const callBtn = document.getElementById("callBtn");
const answerBtn = document.getElementById("answerBtn");
const endBtn = document.getElementById("endBtn");
const remoteAudio = document.getElementById("remoteAudio");

let peerConnection;
let localStream, recorder, chunks = [], callStartTime, timerInterval, isMuted = false;

const config = { iceServers: [{ urls: "stun:stun.l.google.com:19302" }] };

// Initiate call
callBtn.onclick = async () => {
  localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
  peerConnection = createPeerConnection(localStream);
  localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

  const offer = await peerConnection.createOffer();
  await peerConnection.setLocalDescription(offer);
  socket.emit("call-admin", { offer });
};

// Receive incoming call
socket.on("incoming-call", async ({ offer }) => {
  answerBtn.style.display = "inline-block";
  callBtn.style.display = "none";

  answerBtn.onclick = async () => {
    localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
    peerConnection = createPeerConnection(localStream);
    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
    const answer = await peerConnection.createAnswer();
    await peerConnection.setLocalDescription(answer);
    socket.emit("answer-call", { answer });

    answerBtn.style.display = "none";
    endBtn.style.display = "inline-block";
  };
});

// Handle answer from receiver
socket.on("call-answered", async ({ answer }) => {
  await peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
  endBtn.style.display = "inline-block";
});

// Setup peer connection
function createPeerConnection(stream) {
  const pc = new RTCPeerConnection(config);

  pc.ontrack = (event) => {
    remoteAudio.srcObject = event.streams[0];
  };

  pc.onicecandidate = (event) => {
    if (event.candidate) {
      socket.emit("ice-candidate", event.candidate);
    }
  };

  return pc;
}

// ICE Candidate from remote peer
socket.on("ice-candidate", async (candidate) => {
  try {
    await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
  } catch (e) {
    console.error("Error adding ICE candidate", e);
  }
});

// End call
endBtn.onclick = () => {
  peerConnection.close();
  peerConnection = null;
  endBtn.style.display = "none";
  callBtn.style.display = "inline-block";
};

// Play user-initiated sound (for autoplay restrictions)
document.body.addEventListener("click", () => {
  document.getElementById("initSound").click();
}, { once: true });

// Register socket and fetch user list (for admin)
socket.emit("register", "admin");

// Fetch users from PHP
function fetchUsersFromPHP() {
  fetch('get_users.php')
    .then(res => res.json())
    .then(users => {
      const select = document.getElementById("userList");
      select.innerHTML = '<option disabled selected>Select a user to call</option>';
      users.forEach(user => {
        if (user.username !== 'admin') {
          const option = document.createElement("option");
          option.value = user.id;
          option.textContent = `${user.username} (${user.id})`;
          select.appendChild(option);
        }
      });
    })
    .catch(err => console.error('Error fetching users:', err));
}

document.addEventListener("DOMContentLoaded", fetchUsersFromPHP);
