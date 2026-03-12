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
            height: 100vh;
            font-family: Arial;
            background: #f5f5f5;
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
    </style>
</head>
<body>

<!-- frame for temp.jpg -->
<img id="frame" src="https://bsulteng-dev-pos.hcsidn.com:5000/temp.jpg" alt="Latest Frame">

<!-- trigger -->
<button id="remoteBtn">
    Trigger Capture
</button>

<script>
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const frame = document.getElementById('frame');

document.getElementById("remoteBtn").addEventListener("click", async () => {

    // 1) trigger capture on Laravel (file-based)
    await fetch("/file-trigger", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/json"
        },
        body: JSON.stringify({})
    });

    // 2) after a short delay, refresh the image once
    setTimeout(() => {
        frame.src = "https://bsulteng-dev-pos.hcsidn.com:5000/temp.jpg?cache=" + new Date().getTime();
    }, 3000); // wait 3 seconds (adjust if needed)
});
</script>

</body>
</html>