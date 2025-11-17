//  BOOKING PAGE CODE
const seats = document.querySelectorAll(".seat");

seats.forEach(seat => {
    
    seat.addEventListener("click", () => {

        if (seat.classList.contains("reserved")) {
            return;
        }

        seat.classList.toggle("selected");

        updateSelectedSeats();
    });
});

function updateSelectedSeats() {
    const selectedSeats = document.querySelectorAll(".seat.selected");

    const seatIDs = [...selectedSeats].map(seat => seat.dataset.seatId);
    document.getElementById("selectedSeats").value = seatIDs.join(",");
}

function goToPayment() {

    const seats = document.getElementById("selectedSeats").value;
    localStorage.setItem("selectedSeats", seats);
    
    const showtime = document.getElementById("showtime").value;
    localStorage.setItem("showtime", showtime);
    
    const hall = document.getElementById("hall").value;
    localStorage.setItem("hallType", hall);

    localStorage.setItem("ticketsCount", seats.split(",").length);
    window.location.href = "payment_page.html";
}


// PAYMENT PAGE CODE
document.getElementById("paymentForm").onsubmit = function(e){
    e.preventDefault();

    const name = document.getElementById("cardName").value;
    const number = document.getElementById("cardNumber").value;
    const expiry = document.getElementById("expiry").value;
    const cvv = document.getElementById("cvv").value;

    if (!name || !number || !expiry || !cvv) {
        alert("⚠️ Please fill in all payment details.");
        return;
    }


    const seats = localStorage.getItem("selectedSeats") || "";
    const showtime = localStorage.getItem("showtime") || "Not specified";
    const hall = localStorage.getItem("hallType") || "Not specified";

    const bookingId = "BOOK" + Math.floor(Math.random() * 900000 + 100000);

    const bookingData = {
        id: bookingId,
        seats: seats,
        showtime: showtime,
        hall: hall,
        date: new Date().toLocaleString()
    };
    localStorage.setItem("latestBooking", JSON.stringify(bookingData));

   
    document.getElementById("paymentForm").innerHTML = `
        <h2>Payment Successful!</h2>
        <p>Booking ID: <strong>${bookingId}</strong></p>
        <p>Seats: ${seats}</p>
        <p>Showtime: ${showtime}</p>
        <p>Hall: ${hall}</p>
        <h2>Booking Confirmed!</h2>
    `;
};
