/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
<<<<<<< HEAD
import './js/profile.js';
=======
import './login.js';


alert("test");
>>>>>>> 3bdcf11 (js form)

// console.log("Hello Webpack Encore! Edit me in assets/app.js");
// let icon1 = document.getElementById("icon1");
//     let menu1 = document.getElementById("menu1");


//     document.querySelectorAll(".showMenu").forEach((item) => {
//         item.addEventListener("click", function () {
//             console.log("click");
//             showMenu1(true);
//             showMenu2(true);
//             showMenu3(true);
//             showNav(true);
//         });
//     });

//     const showMenu1 = (flag) => {
//         if (flag) {
//         icon1.classList.toggle("rotate-180");
//         menu1.classList.toggle("hidden");
//     }
//     };
//     let icon2 = document.getElementById("icon2");

// console.log(showMenu1(true));
//     const showMenu2 = (flag) => {
//         if (flag) {
//         icon2.classList.toggle("rotate-180");
//     }
//     };
//     let icon3 = document.getElementById("icon3");

//     const showMenu3 = (flag) => {
//         if (flag) {
//         icon3.classList.toggle("rotate-180");
//     }
//     };

//     let Main = document.getElementById("Main");
//     let open = document.getElementById("open");
//     let close = document.getElementById("close");

//     const showNav = (flag) => {
//         if (flag) {
//         Main.classList.toggle("-translate-x-full");
//         Main.classList.toggle("translate-x-0");
//         open.classList.toggle("hidden");
//         close.classList.toggle("hidden");
//     }
//     };

// function verifForm(){
//     let password = document.getElementById("plainPassword");
//     let passwordConfirm = document.getElementById("confirmPassword");

//     if (password.value != passwordConfirm.value){
//         password.style.border = "1px solid red";
//         passwordConfirm.style.border = "1px solid red";
//         console.log("test");
//         return false;
//     }
//     else{
//         password.style.border = "1px solid green";
//         passwordConfirm.style.border = "1px solid green";
//         return true;
//     }
// }


    // $("#password").blur(function passverif() {
    // var password = $(this).val();
    // if (password == "") {
    //     $(this).addClass("error");
    //     document.getElementById('errorpassword').innerHTML = "Veuillez remplir ce champ";
    // } else {
    //     $(this).removeClass("error");
    //     document.getElementById('errorpassword').innerHTML = "";
    // }
    // });

  $("#lastname").blur(function lnverif() {
    var lastname = $(this).val();
    if (lastname == "") {
      $(this).addClass("error");
      document.getElementById('errorlastname').innerHTML = "Veuillez remplir ce champ";
    } else {
      $(this).removeClass("error");
      document.getElementById('errorlastname').innerHTML = "";
    }
  });

  $("#firstname").blur(function fnverif() {
    var firstname = $(this).val();
    if (firstname == "") {
      $(this).addClass("error");
      document.getElementById('errorfirstname').innerHTML = "Veuillez remplir ce champ";
    } else {
      $(this).removeClass("error");
      document.getElementById('errorfirstname').innerHTML = "";
    }
  });

    document.addEventListener("DOMContentLoaded", function() {
        
        // if input is empty
        let password = document.getElementById("plainPassword");
        let passwordConfirm = document.getElementById("confirmPassword");

        if (password.value != passwordConfirm.value){
            password.style.border = "1px solid red";
            passwordConfirm.style.border = "1px solid red";
            console.log("test");
            return false;
        }


    });

