/* logic for collapsibles */

window.onload = function () {

    let collapsibles = document.getElementsByClassName("collapsible");
    let i;

    for (i = 0; i < collapsibles.length; i++) {
        collapsibles[i].addEventListener("click", function () {
            this.classList.toggle("active");
            let content = this.nextElementSibling;
            content.classList.toggle("active");
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
                content.style.overflow = "hidden";
                setTimeout(function(){
                    content.style.overflow = "hidden";
                }, 300);
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                setTimeout(function(){
                    content.style.overflow = "visible";
                }, 300);
            }
        });
    }
}