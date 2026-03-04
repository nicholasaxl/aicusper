<!DOCTYPE html>
<html>
<head>
    <title>Remote Trigger</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body style="display:flex;justify-content:center;align-items:center;height:100vh;">

<button id="remoteBtn" style="padding:20px;font-size:20px;">
    Trigger Capture
</button>

<script>
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.getElementById("remoteBtn").addEventListener("click", async () => {

    await fetch("/trigger-capture", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/json"
        }
    });

    alert("Triggered!");
});
</script>

</body>
</html>