
function login_log(event) {

    event.preventDefault(); 
    
    elements = event.target.elements;
    btn = elements.submitBtn;
    
    data = {
        p_user: elements.user.value,
        p_pass: elements.pass.value,
    };

      btn.disabled = true;
      $.post("/routes/requests.php?login",
        data,
        function (response) {

            response = JSON.parse(response);

            if (response.status == 'success') {
                window.location.href = "/?";
            } else {
                alert(response.message);
                btn.disabled = false;
            }
        }
      );
   
  }