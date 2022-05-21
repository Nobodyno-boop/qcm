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
        style.innerHTML = "@import url('/assets/css/styles.css')"
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
            let choice = e.target.getAttribute("data-choice");
            if (this.type === "single") {
                this.setAttribute("data-choice", choice)
                this.reponse.filter(x => x.classList.contains("question-selected")).forEach(x => x.classList.remove("question-selected"))
                e.target.classList.add("question-selected")
            } else if (this.type === "multi") {
                let choices = this.getAttribute("data-choice").split("-");
                if (choices.includes(choice)) {
                    e.target.classList.remove("question-selected")
                    choices = choices.filter(x => x !== choice)
                } else {
                    e.target.classList.add("question-selected")
                    choices.push(choice)
                }
                // avoid empty string get join like that: "-0-1"
                choices = choices.filter(x => x !== "")
                this.setAttribute("data-choice", choices.join("-"));
            }
        })
        element.innerText = str;
        this.reponse.push(element)
        return element;
    }

    render(){
        if(this.answers){
            // console.log(this.question)
            this.wrapper.innerHTML = `<h4>${this.question}</h4>`;
            this.answers.forEach((x, i) => {
                this.wrapper.appendChild(this.createResponse(x, i))
            })
        }
    }




}