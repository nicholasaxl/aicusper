<!DOCTYPE html>
<html>
<head>
    <title>Remote Trigger</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: Arial;
            background: #f5f5f5;
            padding: 20px;
            box-sizing: border-box;
        }

        #frame {
            border: 4px solid #f44336;
            border-radius: 12px;
            width: 400px;
            height: 300px;
            object-fit: cover;
            background: white;
        }

        #remoteBtn {
            margin-top: 20px;
            padding: 16px 28px;
            font-size: 18px;
            background: #f44336;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        #remoteBtn:hover {
            background: #d32f2f;
        }

        #remoteBtn:disabled {
            background: #999;
            cursor: not-allowed;
        }

        .results-panel {
            margin-top: 24px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            width: 400px;
            box-sizing: border-box;
        }

        .results-panel h3 {
            margin: 0 0 12px;
            color: #333;
            font-size: 16px;
            border-bottom: 2px solid #f44336;
            padding-bottom: 8px;
        }

        .result-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 16px;
        }

        .result-item {
            display: flex;
            flex-direction: column;
        }

        .result-item .label {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .result-item .value {
            font-size: 15px;
            font-weight: 600;
            color: #333;
        }

        .result-food {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #eee;
        }

        .result-food .food-name {
            font-size: 18px;
            font-weight: 700;
            color: #f44336;
        }

        .result-food .food-price {
            font-size: 14px;
            color: #666;
            margin-top: 2px;
        }

        .result-food .flavor {
            font-size: 13px;
            color: #555;
            font-style: italic;
            margin-top: 8px;
            line-height: 1.4;
        }

        .status {
            margin-top: 8px;
            font-size: 13px;
            color: #888;
        }
    </style>
</head>
<body>

<img id="frame" src="{{ env('FLASK_API_URL', 'http://localhost:5000') }}/temp.jpg" alt="Latest Frame">

<button id="remoteBtn">
    Trigger Capture
</button>

<p class="status" id="status">Ready</p>

<div class="results-panel" id="resultsPanel" style="display:none;">
    <h3>AI Analysis Results</h3>
    <div class="result-grid">
        <div class="result-item">
            <span class="label">Age</span>
            <span class="value" id="resAge">—</span>
        </div>
        <div class="result-item">
            <span class="label">Gender</span>
            <span class="value" id="resGender">—</span>
        </div>
        <div class="result-item">
            <span class="label">Race</span>
            <span class="value" id="resRace">—</span>
        </div>
        <div class="result-item">
            <span class="label">Weather</span>
            <span class="value" id="resWeather">—</span>
        </div>
    </div>
    <div class="result-food">
        <div class="food-name" id="resFood">—</div>
        <div class="food-price" id="resPrice"></div>
        <div class="flavor" id="resFlavor"></div>
    </div>
</div>

<script>
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const frame = document.getElementById('frame');
const statusEl = document.getElementById('status');
const CURRENCY = "{{ env('CURRENCY', 'Rp') }}";
const API_BASE = "{{ env('FLASK_API_URL', 'http://localhost:5000') }}";

// Auto-refresh temp image every 2 seconds
setInterval(() => {
    const img = new Image();
    const url = API_BASE + "/temp.jpg?t=" + Date.now();
    img.onload = () => { frame.src = url; };
    img.src = url;
}, 2000);

// Trigger button
document.getElementById("remoteBtn").addEventListener("click", async () => {
    const btn = document.getElementById("remoteBtn");
    btn.disabled = true;
    statusEl.textContent = "Triggering capture...";

    await fetch("/file-trigger", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/json"
        },
        body: JSON.stringify({})
    });

    statusEl.textContent = "Waiting for AI analysis (~30s)...";

    setTimeout(() => {
        btn.disabled = false;
        statusEl.textContent = "Ready";
    }, 35000);
});

// Poll for latest results every 3 seconds
let lastFood = null;

async function pollResults() {
    try {
        const res = await fetch(API_BASE + "/api/latest-result-stream");
        if (!res.ok) return;
        const d = await res.json();

        if (!d.recommended_food) return;

        document.getElementById('resultsPanel').style.display = 'block';
        document.getElementById('resAge').textContent = d.age || '—';
        document.getElementById('resGender').textContent = d.gender || '—';
        document.getElementById('resRace').textContent = d.race || '—';
        document.getElementById('resWeather').textContent = d.weather || '—';
        document.getElementById('resFood').textContent = d.recommended_food || '—';
        document.getElementById('resPrice').textContent = d.price ? CURRENCY + " " + d.price : '';
        document.getElementById('resFlavor').textContent = d.flavor_text || '';

        if (d.recommended_food !== lastFood) {
            lastFood = d.recommended_food;
            statusEl.textContent = "Result received!";
            document.getElementById("remoteBtn").disabled = false;
        }
    } catch (err) {
        // silent
    }
}

setInterval(pollResults, 3000);
pollResults();
</script>

</body>
</html>