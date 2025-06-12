emailjs.init("jPyobImBX-gSDmoPH");

// // Add event listener to the send button
// sendBtn.addEventListener("click", (e) => {
//   e.preventDefault();

  // Get the form values
  const name = "JK";//document.getElementById("name").value;
  const email = "josephkuttor730@gmail.com";//document.getElementById("email").value;
  const message = "Hello, Joseph.";//document.getElementById("message").value;

  // Send the email using EmailJS
  emailjs.send("service_ezr6swe", "template_i8b70wl", {
    from_name: name,
    from_email: email,
    message: message,
    to_email: "wlord820@mail.com",
    title: "UMAT SRC"
  })
  .then((response) => {
    console.log("Email sent successfully!", response.status, response.text);
    alert("Email sent successfully!");
  })
  .catch((error) => {
    console.log("Error sending email:", error);
    alert("Error sending email. Please try again.");
  });
// });