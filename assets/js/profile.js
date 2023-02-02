let inputs = document.querySelectorAll(".form-control");
var initial = "{";
inputs.forEach(input => {
    initial += "\"" + input.name + "\" : \"" + input.value + "\", ";
});
initial = initial.slice(0, -2);
initial += "}";
initial = JSON.parse(initial)

submitProfile = document.getElementById("user_update_submit");

submitProfile.addEventListener("click", (e) => {
    let isUpdate = false;
    let inputs = document.querySelectorAll(".form-control");
    inputs.forEach(input => {
        if(initial[input.name] != input.value){
            isUpdate = true;
        }
    });

    if(!isUpdate){
        e.preventDefault();
        document.getElementById("alert").style.display = "block";
    }
});