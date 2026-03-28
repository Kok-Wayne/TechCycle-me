// 🔹 Send Location (Function 2)
document.getElementById("locationForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("ewaste_management.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById("locationMsg").innerHTML = data;
    })
    .catch(() => {
        document.getElementById("locationMsg").innerHTML = "Error occurred.";
    });
});


// 🔹 Update Status (Function 5)
document.getElementById("statusForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("ewaste_management.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById("statusMsg").innerHTML = data;
    })
    .catch(() => {
        document.getElementById("statusMsg").innerHTML = "Error occurred.";
    });
});