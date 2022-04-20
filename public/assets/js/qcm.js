

export default class Qcm extends HTMLElement {

    constructor() {
        super();
        this.questions = [];
        this.dataQuestion = [];
        this.version = "";
        this.wrapper = document.createElement("div");

        this.shadow = this.attachShadow({mode: 'open'});
        this.shadow.append(this.wrapper)

    }

    connectedCallback() {
        this.dataQuestion = JSON.parse(this.getAttribute("data-questions"));
        this.version = this.getAttribute("data-version")
        this.render()
    }

    disconnectedCallback() {

    }

    createQuestion(json) {
        let element = document.createElement("qcm-question-view");

        element.setAttribute("data-question", JSON.stringify(json['question']))
        element.setAttribute("data-id", json['id'])
        element.setAttribute("data-answers", JSON.stringify(json['answers']))

        this.questions.push(element)
        return element;
    }

    render(){
        if(this.dataQuestion.length >=1){
            this.dataQuestion.map(x => this.createQuestion(x))
                .forEach(x => this.wrapper.appendChild(x))


            let btn = document.createElement("button")
            btn.innerText = "Send !"
            btn.addEventListener("click", (e) => {
                let qcm = {"version": this.version, questions: []}
                let missedQuestion = [];
                this.questions.forEach(x => {
                        let choice = x.getAttribute("data-choice") ?? null;
                        let id = x.getAttribute("data-id")
                        if(choice) {
                            qcm.questions.push({id: id, anwser: choice})
                        } else missedQuestion.push(x)
                    })
                console.log("miss:" + missedQuestion.length )
                if(qcm.questions.length === this.dataQuestion.length){
                    let h = new Headers()
                        h.append("Content-Type", "application/json")
                    fetch("/qcm/result/2", {
                        body: JSON.stringify(qcm),
                        method: "POST",
                        headers: new Headers({'Accept': "application/json"})
                    }).catch(e => console.log(e)).then(x => {
                        x.text().then(x => console.log(x))
                        // if(x.ok){
                        //     x.json().then(x => console.log(x))
                        // } else {
                        //     x.text().then(x => console.log(x))
                        // }

                    })
                }
            })

            this.wrapper.append(btn)
        }
    }
}


