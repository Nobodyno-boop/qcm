(function (){
   let buttons = document.querySelectorAll("[data-collapse-toggle]");
   buttons.forEach(element => {
      let id = element.getAttribute("data-collapse-toggle");
      element.addEventListener("click", (click) => {
         let elementData = document.getElementById(id);
         elementData.classList.toggle("hidden");
         element.setAttribute("aria-expanded", !elementData.classList.contains("hidden"))
      })
   });
}());