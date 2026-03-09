<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Food Recommendation</title>

<style>

body{
    margin:0;
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(
        135deg,
        #fff7e6,
        #f5e2b8,
        #d4a94f
    );
    height:100vh;
    overflow:hidden;
}

/* MAIN LAYOUT */

.container{
    display:flex;
    height:100vh;
    padding:4vw;
    box-sizing:border-box;
    gap:4vw;
}

/* LEFT SIDE */

.left{
    width:45%;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.description{
    font-size:clamp(24px, 3vw, 48px);
    line-height:1.4;
    color:#4b3305;
    font-weight:600;
    max-width:80%;
}

/* RIGHT SIDE */

.right{
    width:55%;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
    position:relative;
}

.price{
    font-size:clamp(20px, 2vw, 36px);
    font-weight:600;
    color:#6a4d08;
    margin-top:0.5vw;
}

/* GOLD GLOW */

.right::before{
    content:"";
    position:absolute;
    width:35vw;
    height:35vw;
    background: radial-gradient(circle, #fff4c4, transparent 70%);
    border-radius:50%;
    z-index:1;
}

/* IMAGE */

.hero-image{
    width:28vw;
    max-width:450px;
    border-radius: 60% 40% 50% 50% / 40% 60% 40% 60%;
    filter: drop-shadow(0px 2vw 3vw rgba(0,0,0,0.25));
    animation: float 3s ease-in-out infinite;
    z-index:2;
    object-fit:cover;
}

/* TITLE */

.headline{
    margin-top:2vw;
    font-size:clamp(28px, 3vw, 56px);
    font-weight:700;
    color:#443309;
}

/* FLOAT ANIMATION */

@keyframes float{
    0%{transform:translateY(0);}
    50%{transform:translateY(-0.6vw);}
    100%{transform:translateY(0);}
}

/* META PANEL */

.meta{
    position:fixed;
    top:16px;
    right:16px;
    background:rgba(255,255,255,0.95);
    border:2px solid #b8860b;
    border-radius:8px;
    padding:12px;
    font-size:0.9rem;
    max-width:200px;
    text-align:left;
    z-index:10;
}

/* SNAPSHOT (hidden camera preview) */

.snapshot{
    opacity:0;
    position:absolute;
    pointer-events:none;
}

.snapshot video{
    width:240px;
}

/* BRANDING */

.branding{
    position:fixed;
    bottom:1.2vw;
    width:100%;
    text-align:center;
    font-size:clamp(10px, 1vw, 14px);
    color:#6a4d08;
    opacity:0.7;
}

/* MOBILE */

@media (max-width:900px){

.container{
    flex-direction:column;
    justify-content:center;
    align-items:center;
}

.left{
    width:90%;
    text-align:center;
}

.right{
    width:100%;
}

.description{
    max-width:100%;
}

}

</style>
</head>

<body>

<div class="meta" style="display:none;">
<h4>Age: <span id="age">—</span></h4>
<h4>Gender: <span id="gender">—</span></h4>
<h4>Race: <span id="race">—</span></h4>
<h4>Weather: <span id="weather">—</span></h4>
<h4>Lat: <span id="lat">—</span></h4>
<h4>Lon: <span id="lon">—</span></h4>
</div>

<!-- hidden camera -->
<div class="snapshot">
<video id="previewVideo" autoplay muted playsinline></video>
</div>

<div class="container">

<div class="left">
<p class="description" id="flavorText">
Waiting for recommendation...
</p>
</div>

<div class="right">

<img id="foodImage"
class="hero-image"
src="https://images.unsplash.com/photo-1550547660-d9450f859349">

<h2 class="headline" id="foodTitle">
Recommended Food
</h2>

<h3 id="foodPrice" class="price">
—
</h3>

</div>

</div>

<div class="branding">
This store is powered by <strong>Hitachi POS</strong>
</div>

<script>
const CURRENCY = "{{ env('CURRENCY', 'Rp') }}";
</script>

@vite('resources/js/app.js')

<script>

let waitingForResult = false;
let captureRunning = false;

const previewVideo = document.getElementById('previewVideo');
const canvas = document.createElement('canvas');
const video = previewVideo;

const API_RECOMMEND = "http://127.0.0.1:5000/recommend";
const OUTLET_ID = 2;

function showError(message){
document.getElementById("flavorText").textContent = "⚠ " + message;
}

function resetState(){
waitingForResult=false;
captureRunning=false;
}

/* CAMERA */

navigator.mediaDevices.getUserMedia({video:true})
.then(stream=>{
previewVideo.srcObject=stream;
video.srcObject=stream;
})
.catch(err=>alert("Camera access denied: "+err));

/* CAPTURE */

async function captureAndSend(){

if(captureRunning) return;
captureRunning=true;

try{

const ctx = canvas.getContext('2d');
canvas.width = video.videoWidth;
canvas.height = video.videoHeight;

ctx.drawImage(video,0,0);

const blob = await new Promise((resolve,reject)=>{
canvas.toBlob(b=>{
if(b) resolve(b);
else reject(new Error("Failed to capture image"));
},"image/jpeg");
});

const position = await new Promise((resolve,reject)=>{
navigator.geolocation.getCurrentPosition(resolve,reject);
});

let formData = new FormData();
formData.append("image",blob,"capture.jpg");
formData.append("outlet_id",OUTLET_ID);
formData.append("latitude",position.coords.latitude);
formData.append("longitude",position.coords.longitude);

console.log("📤 Sending frame to Flask...");

const res = await fetch(API_RECOMMEND,{
method:"POST",
body:formData
});

const data = await res.json();

if(!res.ok){
throw new Error(data.error || "Server error");
}

}catch(err){

console.error("❌ Capture error:",err);
showError(err.message);
resetState();

}finally{
captureRunning=false;
}

}

/* WEBSOCKET TRIGGER */

window.addEventListener('capture-trigger', async ()=>{
console.log("📡 WebSocket trigger received");

waitingForResult=true;
await captureAndSend();

});

/* SSE LISTENER */

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
  
  // 🔹 Food name from DB
  document.getElementById('foodTitle').textContent = d.recommended_food || "Recommended Food";
  document.getElementById('foodPrice').textContent =
    d.price ? CURRENCY + " " + d.price : "";
  document.getElementById('flavorText').textContent = d.flavor_text || '';

  if (waitingForResult) {
    resetState();
  }
};

</script>

</body>
</html>