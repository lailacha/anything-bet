const lastname = document.querySelector("input[name='registration_form[lastname]']");
console.log(lastname);
const firstname = document.querySelector("input[name='registration_form[firstname]']");
console.log(firstname);
const email = document.querySelector("input[name='registration_form[email]']");
console.log(email);
const username = document.querySelector("input[name='registration_form[username]']");
console.log(username);
const password = document.querySelector("input[name='registration_form[plainPassword]']");
console.log(password);
const passwordConfirm = document.querySelector("input[name='registration_form[confirmPassword]']");
console.log(passwordConfirm);

lastname.addEventListener("keyup", function lnverif() {
    let lastnameval = lastname.value;
    if (lastnameval == "") {
        lastname.classList.add("error");
        document.getElementById('errorlastname').innerHTML = "Veuillez remplir ce champ";
    } else {
        lastname.classList.remove("error");
        document.getElementById('errorlastname').innerHTML = "";
    }
});

firstname.addEventListener("keyup", function fnverif() {
    let firstnameval = firstname.value;
    if (firstnameval == "") {
        firstname.classList.add("error");
        document.getElementById('errorfirstname').innerHTML = "Veuillez remplir ce champ";
    } else {
        firstname.classList.remove("error");
        document.getElementById('errorfirstname').innerHTML = "";
    }
});

email.addEventListener("keyup", function emverif() {
    let emailval = email.value;
    const regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(emailval)) {
        email.classList.add("error");
        document.getElementById('erroremail').innerHTML = "Veuillez remplir ce champ";
    } else {
        email.classList.remove("error");
        document.getElementById('erroremail').innerHTML = "";
    }
});

username.addEventListener("keyup", function unverif() {
    let usernameval = username.value;
    if (usernameval == "") {
        username.classList.add("error");
        document.getElementById('errorusername').innerHTML = "Veuillez remplir ce champ";
    } else {
        username.classList.remove("error");
        document.getElementById('errorusername').innerHTML = "";
    }
});

let passwordval = password.value;
password.addEventListener("keyup", function passverif() {
    if (passwordval == "") {
        password.classList.add("error");
        document.getElementById('errorpassword').innerHTML = "Veuillez remplir ce champ";
    } else {
        password.classList.remove("error");
        document.getElementById('errorpassword').innerHTML = "";
    }
});

passwordConfirm.addEventListener("keyup", function passconfverif() {
    let passwordconfval = passwordConfirm.value;
    if (passwordconfval == "") {
        passwordConfirm.classList.add("error");
        document.getElementById('errorpasswordconf').innerHTML = "Veuillez remplir ce champ";
    } else {
        passwordConfirm.classList.remove("error");
        document.getElementById('errorpasswordconf').innerHTML = "";
    }
    if (passwordval != passwordconfval) {
        passwordConfirm.classList.add("error");
        document.getElementById('errorpasswordconf').innerHTML = "Les mots de passe ne correspondent pas";
    } else {
        passwordConfirm.classList.remove("error");
        document.getElementById('errorpasswordconf').innerHTML = "";
    }
});
