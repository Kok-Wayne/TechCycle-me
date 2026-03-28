// ================= SEND LOCATION =================
document.getElementById("locationForm")?.addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("send_ewaste_location.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("locationMsg").innerHTML = data.message;
    })
    .catch(() => {
        document.getElementById("locationMsg").innerHTML = "Error occurred.";
    });
});


// ================= UPDATE STATUS =================
document.getElementById("statusForm")?.addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("update_ewaste_status.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("statusMsg").innerHTML = data.message;
    })
    .catch(() => {
        document.getElementById("statusMsg").innerHTML = "Error occurred.";
    });
});