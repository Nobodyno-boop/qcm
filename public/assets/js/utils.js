(function () {
    // recupère tout les elements qui ont un "data-toggle-element"
    let elements = document.querySelectorAll("[data-toggle-element]")
    elements.forEach(x => {
        // Ajoute un event on click
        x.addEventListener("click", (e) => {
            // recupere le data-toggle-element
            let query = e.target.getAttribute("data-toggle-element");
            // recupere le data-toggle-class, SI il est null alors par défault il prend la class CSS "hidden"
            let style = e.target.getAttribute("data-toggle-class") || "hidden";

            let afterDelay = e.target.getAttribute("data-toggle-after-delay") || null;

            let fun;
            if(afterDelay){
                afterDelay = afterDelay.split(",")
                let css = afterDelay[0].split("|");
                let time = afterDelay[1];

                if(time.endsWith("s")){
                    let su = time.substring(0, time.length-1);
                    if(!isNaN(Number(su))) { //Todo; fix
                        var delay = null;
                        if (su % 1 === 0) {
                            delay = 1000 * Number(su);
                        } else {
                            //1.0
                            // 0.5
                            let number = su.split(".");
                            delay = 1000 * Number(number[0]);
                            delay += 100 * Number(number[1])
                        }
                        fun = (el) => {
                            css.forEach(x => el.classList.toggle(x))
                        }
                    }
                }
            }

            style = style.split("|")
            // récupère l'élement html via query
            let element = document.querySelector(query)
            if (!element) { // met une erreur dans le cas ou l'element est null
                throw new Error("Could not load the element with query :" + query)
            }
            // toggle la class pour l'element
            style.forEach(css => element.classList.toggle(css))
            if (fun) {
                setTimeout(() => {
                    fun(element)
                }, delay)
            }
        })
    })
}());