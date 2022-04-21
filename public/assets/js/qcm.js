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

    markErrors(errors) {
        for (let error of errors) {

            let id = error['id'];
            let correct = error['correct'];
            //filter the question by id
            let questions = this.questions.filter(x => x.getAttribute("data-id") === id);
            if (questions.length >= 1) {
                questions.forEach(element => {
                    let wrongs = element.shadowRoot.querySelectorAll(".question-selected");
                    wrongs.forEach(x => x.classList.add("question-wrong"))
                    if (Array.isArray(correct)) {
                        correct.forEach(x => {
                            let el = element.shadowRoot.querySelector(`[data-choice='${x}']`);
                            if (el) {
                                el.classList.add("question-correct")
                            }
                        })
                    } else {
                        let el = element.shadowRoot.querySelector(`[data-choice='${correct}']`)
                        if (el) {
                            el.classList.add("question-correct")
                        }
                    }
                })
            }
        }

    }

    render() {
        if (this.dataQuestion.length >= 1) {
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
                            qcm.questions.push({id: id, answer: choice})
                        } else missedQuestion.push(x)
                    })

                if(qcm.questions.length === this.dataQuestion.length){
                    let h = new Headers()
                    h.append("Content-Type", "application/json")
                    fetch("/qcm/result/2", {
                        body: JSON.stringify(qcm),
                        method: "POST",
                        headers: new Headers({'Accept': "application/json"})
                    }).catch(e => console.log(e)).then(x => {
                        if (x.ok) {
                            return x.json()
                        }

                    }).then(x => {
                        if (x.errors !== []) {
                            this.markErrors(x.errors)
                        }
                    })
                }
            })

            this.wrapper.append(btn)
        }
    }
}

