<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Food Recommendation</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #fff 50%, #f44336 50%);
      color: #333;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
      padding-top: 60px;
    }

    .meta {
      position: fixed;
      top: 16px;
      right: 16px;
      background: rgba(255,255,255,0.95);
      border: 2px solid #f44336;
      border-radius: 8px;
      padding: 12px;
      font-size: 0.9rem;
      max-width: 200px;
      text-align: left;
      z-index: 10;
    }

    .meta h4 { margin: 6px 0; }

    .snapshot {
      position: fixed;
      top: 16px;
      left: 16px;
      border: 2px solid #f44336;
      border-radius: 8px;
      background: white;
      z-index: 10;
    }

    .snapshot video {
      display: block;
      width: 240px;
      height: 180px;
    }

    .card {
      background: white;
      border: 4px solid #f44336;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
      max-width: 600px;
      width: 90%;
      margin-top: 20px;
    }

    .card img { width: 100%; display: block; }

    .flavor {
      background: #fff5f5;
      border-top: 4px solid #f44336;
      padding: 24px;
      font-size: 1.1rem;
      line-height: 1.6;
      color: #444;
    }
  </style>
</head>
<body>

  <div class="meta">
    <h4>Age: <span id="age">—</span></h4>
    <h4>Gender: <span id="gender">—</span></h4>
    <h4>Race: <span id="race">—</span></h4>
    <h4>Weather: <span id="weather">—</span></h4>
    <h4>Lat: <span id="lat">—</span></h4>
    <h4>Lon: <span id="lon">—</span></h4>
  </div>

  <div class="snapshot" style="opacity: 0; position: absolute; pointer-events: none;">
      <video id="previewVideo" autoplay muted playsinline></video>
  </div>

  <div class="card">
    <img id="foodImage" src="" alt="Food Image">
    <div class="flavor" id="flavorText">—</div>
  </div>

  @vite('resources/js/app.js')

<script>
let waitingForResult = false;
let captureRunning = false;

const previewVideo = document.getElementById('previewVideo');
const canvas = document.createElement('canvas');
const video = previewVideo;

const API_RECOMMEND = "http://127.0.0.1:5000/recommend";
const OUTLET_ID = 2;

function showError(message) {
  document.getElementById("flavorText").textContent = "⚠ " + message;
}

function resetState() {
  waitingForResult = false;
  captureRunning = false;
}

// Camera (hidden preview but stream active)
navigator.mediaDevices.getUserMedia({ video: true })
  .then(stream => {
    previewVideo.srcObject = stream;
    video.srcObject = stream;
  })
  .catch(err => alert("Camera access denied: " + err));

// Capture and send
async function captureAndSend() {
  if (captureRunning) return;
  captureRunning = true;

  try {
    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);

    const blob = await new Promise((resolve, reject) => {
      canvas.toBlob(b => {
        if (b) resolve(b);
        else reject(new Error("Failed to capture image"));
      }, "image/jpeg");
    });

    const position = await new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject);
    });

    let formData = new FormData();
    formData.append("image", blob, "capture.jpg");
    formData.append("outlet_id", OUTLET_ID);
    formData.append("latitude", position.coords.latitude);
    formData.append("longitude", position.coords.longitude);

    console.log("📤 Sending frame to Flask...");

    const res = await fetch(API_RECOMMEND, {
      method: "POST",
      body: formData
    });

    const data = await res.json();

    if (!res.ok) {
      throw new Error(data.error || "Server error");
    }

  } catch (err) {
    console.error("❌ Capture error:", err);
    showError(err.message);
    resetState();
  } finally {
    captureRunning = false;
  }
}

// WebSocket trigger → snap photo
window.addEventListener('capture-trigger', async () => {
  console.log("📡 WebSocket trigger received");

  waitingForResult = true;
  await captureAndSend();
});

// SSE listener (result update)
const evtSource = new EventSource("http://127.0.0.1:5000/api/latest-result-stream");

evtSource.onmessage = function(event) {
  const d = JSON.parse(event.data);

  document.getElementById('age').textContent = d.age || 'N/A';
  document.getElementById('gender').textContent = d.gender || 'N/A';
  document.getElementById('race').textContent = d.race || 'N/A';
  document.getElementById('weather').textContent = d.weather || 'N/A';
  document.getElementById('lat').textContent = d.latitude || 'N/A';
  document.getElementById('lon').textContent = d.longitude || 'N/A';

  if (d.food_image) {
    document.getElementById('foodImage').src = '/storage/' + d.food_image;
  }

  document.getElementById('flavorText').textContent = d.flavor_text || '';

  if (waitingForResult) {
    resetState();
  }
};
</script>

</body>
</html>