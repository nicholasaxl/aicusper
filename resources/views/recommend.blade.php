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

    /* snapshot preview top-left */
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
      width: 240px;   /* small window */
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
    .capture-btn {
      margin-top: auto;
      margin-bottom: 40px;
      padding: 14px 28px;
      font-size: 1rem;
      font-weight: bold;
      background: #f44336;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      box-shadow: 0 6px 12px rgba(0,0,0,0.2);
      transition: 0.2s ease;
    }

    .capture-btn:hover {
      background: #d32f2f;
      transform: translateY(-2px);
    }

    .capture-btn:disabled {
      background: #aaa;
      cursor: not-allowed;
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

  <!-- Snapshot preview -->
  <div class="snapshot">
    <video id="previewVideo" autoplay muted playsinline></video>
  </div>

  <div class="card">
    <img id="foodImage" src="" alt="Food Image">
    <div class="flavor" id="flavorText">—</div>
  </div>
  <button id="captureBtn" class="capture-btn">
    Capture Recommendation
  </button>

  <script>
    let waitingForResult = false; // 🔒 lock for capture-result cycle
    const previewVideo = document.getElementById('previewVideo');
    const canvas = document.createElement('canvas'); // hidden canvas for capture
    const video = previewVideo; // use same video for preview + capture

    const API_RECOMMEND = "http://127.0.0.1:5000/recommend";
    // const API_RESULT   = "/api/latest-result";   
    const OUTLET_ID    = 82;
    const DELAY_MS     = 5000;

    let captureRunning = false; // 🔒 lock

    // Start camera (video only for grabbing frames, never displayed)
    navigator.mediaDevices.getUserMedia({ video: true })
      .then(stream => {
        video.srcObject = stream;
        /* video.play(); */
        navigator.mediaDevices.getUserMedia({ video: true })
          .then(stream => {
            previewVideo.srcObject = stream;
          })
          .catch(err => alert("Camera access denied: " + err));
        video.onloadedmetadata = () => {
          console.log("✅ Camera ready, starting loops...");
          // startCaptureLoop();
          // startResultLoop();
        };
      })
      .catch(err => alert("Camera access denied: " + err));

    // Capture and send to Flask
    async function captureAndSend() {
      if (captureRunning) {
        console.log("⏸ Skipping capture, still running...");
        return;
      }
      captureRunning = true; // lock set

      const ctx = canvas.getContext('2d');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      ctx.drawImage(video, 0, 0);

      canvas.toBlob(async (blob) => {
        if (!blob) {
          captureRunning = false;
          return;
        }

        let formData = new FormData();
        formData.append("image", blob, "capture.jpg");
        formData.append("outlet_id", OUTLET_ID);

        navigator.geolocation.getCurrentPosition(async (pos) => {
          formData.append("latitude", pos.coords.latitude);
          formData.append("longitude", pos.coords.longitude);

          try {
            console.log("📤 Sending frame to Flask...");
            await fetch(API_RECOMMEND, { method: "POST", body: formData });
          } catch (e) {
            console.error("❌ Request failed:", e);
          } finally {
            captureRunning = false; // unlock when done
          }
        }, (err) => {
          console.warn("⚠️ Location denied:", err.message);
          captureRunning = false;
        });
      }, "image/jpeg");
    }
    const captureBtn = document.getElementById("captureBtn");

    captureBtn.addEventListener("click", async () => {
      if (waitingForResult) return;

      waitingForResult = true;

      captureBtn.disabled = true;
      captureBtn.textContent = "⏳ Processing...";

      await captureAndSend();
    });

    // Poll latest JSON and update UI
    async function fetchAndRender() {
      try {
        const res = await fetch(API_RESULT);
        if (!res.ok) return;
        const d = await res.json();
        document.getElementById('age').textContent = d.age || 'N/A';
        document.getElementById('gender').textContent = d.gender || 'N/A';
        document.getElementById('race').textContent = d.race || 'N/A';
        document.getElementById('weather').textContent = d.weather || 'N/A';
        document.getElementById('lat').textContent = d.latitude || 'N/A';
        document.getElementById('lon').textContent = d.longitude || 'N/A';
        if (d.food_image) {
          document.getElementById('foodImage').src = 'img/foods/' + d.food_image;
        }
        document.getElementById('flavorText').textContent = d.flavor_text || '';
      } catch (e) {
        console.error("❌ Fetch error:", e);
      }
    }

    /* function startCaptureLoop() {
      setInterval(captureAndSend, DELAY_MS);
    } */

    // 🔥 SSE listener (push from backend)
    const evtSource = new EventSource("http://127.0.0.1:5000/api/latest-result-stream");

    evtSource.onmessage = function(event) {
      const d = JSON.parse(event.data);

      console.log("📡 New result received");

      // Update UI
      document.getElementById('age').textContent = d.age || 'N/A';
      document.getElementById('gender').textContent = d.gender || 'N/A';
      document.getElementById('race').textContent = d.race || 'N/A';
      document.getElementById('weather').textContent = d.weather || 'N/A';
      document.getElementById('lat').textContent = d.latitude || 'N/A';
      document.getElementById('lon').textContent = d.longitude || 'N/A';

      if (d.food_image) {
        document.getElementById('foodImage').src = 'foods/' + d.food_image;
      }

      document.getElementById('flavorText').textContent = d.flavor_text || '';

      // 🔥 Stop loading ONLY when result arrives
      if (waitingForResult) {
        captureBtn.textContent = "📸 Capture Recommendation";
        captureBtn.disabled = false;
        waitingForResult = false;
      }
    };

    function startResultLoop() {
      setInterval(fetchAndRender, DELAY_MS);
      fetchAndRender();
    }
  </script>

</body>
</html>
