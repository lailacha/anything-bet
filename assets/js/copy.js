
window.addEventListener('load', function () {
    const showText = document.querySelector(".js-show-text")


    // add event listener on all button with class js-copy-to-clip
    document.querySelectorAll(".js-copy-to-clip").forEach((item) => {
        item.addEventListener("click", function () {
            copyMeOnClipboard(item.dataset.target)
        });
    });

    const copyMeOnClipboard = (elem) => {

        // get the element
        const copyText = document.querySelector(elem);

        console.log(copyText)
        // select the text
        copyText.select();

        // copy the text
        document.execCommand("copy");

        // hide the text
        setTimeout(function () {

        }, 2000);

    }
});