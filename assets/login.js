
const email = document.getElementById("email");
const password = document.getElementById("floating_standard password");

email?.addEventListener("keyup", function emailverif() {
    let emailval = email.value;
    const regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(emailval)) {
      email.classList.add("error");
      console.log(this.classList);
      document.getElementById('erroremail').innerHTML = "Veuillez choisir un email valide";
    } else {
      email.classList.remove("error");
      document.getElementById('erroremail').innerHTML = "";
    }
});

password?.addEventListener("keyup", function passverif() {
    let passwordval = password.value;
    if (passwordval == "") {
        password.classList.add("error");
        document.getElementById('errorpassword').innerHTML = "Veuillez remplir ce champ";
    } else {
        password.classList.remove("error");
        document.getElementById('errorpassword').innerHTML = "";
    }
});
