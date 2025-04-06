//Ẩn hiện form đặt vé
document.getElementById("showBookingForm").addEventListener("click", function() {
    document.getElementById("bookingOverlay").style.display = "flex";
});
document.getElementById("closeBookingForm").addEventListener("click", function() {
    document.getElementById("bookingOverlay").style.display = "none";
});

//Xử lý đặt vé trong form
document.querySelector("form").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = new FormData(this);

    fetch("../Home/process_booking.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            location.reload();
        } else {
            alert(data.error);
        }
    })
    .catch(error => console.error("Lỗi:", error));
});

