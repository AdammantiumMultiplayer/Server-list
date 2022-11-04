setInterval(() => {
    document.getElementById('logs').contentWindow.location.reload();
    document.getElementById('playerinf').contentWindow.location.reload();
    console.log("loaded")
}, 5000);