export default class Question extends HTMLElement {
    constructor() {
        super();
        this._id = "";
        this.question = null;
        this.wrapper = document.createElement("div")
        this.wrapper.classList.add("question")
        this.reponse = [];
        this.answers = [];
        this.type = "single";
        this.shadow = this.attachShadow({mode: 'open'});
        let style = document.createElement("style")
        style.innerHTML = "@import url('/assets/css/styles.css'); @import url('https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css')"
        this.shadow.append(style)
        this.shadow.append(this.wrapper)
    }

    connectedCallback() {
        this.question = JSON.parse(this.getAttribute("data-question"))
        this.answers = JSON.parse(this.getAttribute("data-answers"))
        this.type = this.getAttribute("data-type") ?? "single"
        this.setAttribute("data-choice", "")
        this.render();
    }


    createResponse(str, i) {
        let element = document.createElement("div")
        element.classList.add("answer")
        element.setAttribute("data-choice", i) // by default is not choice
        element.addEventListener("click", (e) => {
            if (!this.hasAttribute("locked")) {
                let el = e.target.closest("[data-choice]")
                if (typeof el === "undefined") {
                    if (e.target.hasAttribute("data-choice")) {
                        el = e.target;
                    } else throw new Error("Can't work")
                }
                let choice = el.getAttribute("data-choice");
                if (this.type === "single") {
                    this.markSelect(el, choice)
                } else if (this.type === "multi") {
                    let choices = this.getAttribute("data-choice").split("-");
                    if (choices.includes(choice)) {
                        el.classList.remove("question-selected")
                        choices = choices.filter(x => x !== choice)
                    } else {
                        el.classList.add("question-selected")
                        choices.push(choice)
                    }
                    // avoid empty string get join like that: "-0-1"
                    choices = choices.filter(x => x !== "")
                    this.setAttribute("data-choice", choices.join("-"));
                }
            }
        })
        let icon = document.createElement("i")
        icon.classList.add("lar", "la-circle")
        element.replaceChildren(icon, str);
        this.reponse.push(element)
        return element;
    }

    markSelect(element, choice) {
        if (!element.classList.contains("question-selected")) {
            let i = document.createElement("i")
            i.classList.add("lar", "la-dot-circle")
            element.replaceChildren(i, element.innerText)
            this.setAttribute("data-choice", choice)
            this.reponse.filter(x => x.classList.contains("question-selected")).forEach(x => {
                x.classList.remove("question-selected")
                let icon = document.createElement("i")
                icon.classList.add("lar", "la-circle")
                x.replaceChildren(icon, x.innerText)
            })
            element.classList.add("question-selected")
        }
    }

    render() {
        if (this.answers) {
            // console.log(this.question)
            this.wrapper.innerHTML = `<h4>${this.question}</h4>`;
            this.answers.forEach((x, i) => {
                this.wrapper.appendChild(this.createResponse(x, i))
            })
        }
    }




}