<input type="hidden" name="sent_status" id="sent_status">
<!-- You must include the EmailJS SDK -->
<script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>

<script>
// Initialize EmailJS with your user/public key
emailjs.init("jPyobImBX-gSDmoPH"); // e.g., "service_xxxxxxx"

  // Get form values
  var name = <?php echo json_encode($From); ?>; // Assuming $From is defined in PHP
  var title = <?php echo json_encode($Subject); ?>; // Assuming $Subject is defined in PHP
  var message = <?php echo json_encode($Body); ?>;
  var to_email = <?php echo json_encode($To); ?>;

  // Send the email using EmailJS
  
  emailjs.send("service_ezr6swe", "template_i8b70wl", {
    name: name,
    message_html: message,
    to_email: to_email,
    title: title
    })
    .then(function(response) {
      var sent = JSON.stringify(1);
      console.log(' sent successfully!', response.status, response.text);
    })
    .catch(function(error) {
      var sent = JSON.stringify(0);
      console.error(' failed to send.', error);
    });
</script>