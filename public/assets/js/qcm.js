export default class Qcm extends HTMLElement {

    constructor() {
        super();
        this.questions = [];
        this.dataQuestion = [];
        this.version = "";
        this.wrapper = document.createElement("div");
        this.wrapper.classList.add("questions")
        this.shadow = this.attachShadow({mode: 'open'});
        let style = document.createElement("style")
        style.innerHTML = "@import url('/assets/css/styles.css')"
        this.shadow.append(style)
        this.shadow.append(this.wrapper)

    }

    connectedCallback() {
        this.dataQuestion = JSON.parse(this.getAttribute("data-questions"));
        this.version = this.getAttribute("data-version")
        this.qcm_id = this.getAttribute("data-id")
        this.errorMode = this.getAttribute("data-error") ?? false;
        this.errors = JSON.parse(this.getAttribute("data-errors")) ?? []
        this.answers = JSON.parse(this.getAttribute("data-answers")) ?? []
        this.url = this.getAttribute("data-url")
        this.render()
    }

    disconnectedCallback() {

    }

    createQuestion(json) {
        let element = document.createElement("qcm-question-view");
        element.setAttribute("data-question", JSON.stringify(json['question']))
        element.setAttribute("data-id", json['id'])
        element.setAttribute("data-answers", JSON.stringify(json['answers']))
        if (this.errorMode) {
            element.setAttribute("locked", "")
        }
        this.questions.push(element)
        return element;
    }

    markErrors(errors) {
        for (let answer of this.answers) {
            let error = this.errors.filter(error => error.id === answer.id);
            let el = this.questions.find(x => (x.getAttribute("data-id") === answer.id))
            let answers = [...el.reponse];
            if (error.length === 1) {
                let correct = error[0]?.correct;
                let question = answers.find(x => parseInt(x.getAttribute("data-choice")) === correct);
                question.classList.add("question-wrong")
            }

            let question = answers.find(x => x.getAttribute("data-choice") === answer.answer)
            if (error.length === 1) {
                question.classList.add("question-selected")
            } else {
                question.classList.add("question-correct")
            }

        }

    }

    render() {
        if (this.dataQuestion.length >= 1) {
            this.dataQuestion.map(x => this.createQuestion(x))
                .forEach(x => this.wrapper.appendChild(x))
            if (!this.errorMode) {

                let btn = document.createElement("button")
                let span = document.createElement("span")
                span.innerText = "Test !"
                btn.classList.add("button-cool")
                btn.appendChild(span)
                btn.addEventListener("click", (e) => {
                    let qcm = {"version": this.version, questions: []}
                    let missedQuestion = [];
                    this.questions.forEach(x => {
                        let choice = x.getAttribute("data-choice") ?? null;
                        let id = x.getAttribute("data-id")
                        if (choice) {
                            x.setAttribute("locked", true)
                            qcm.questions.push({id: id, answer: choice})
                        } else missedQuestion.push(x)
                    })

                    if (qcm.questions.length === this.dataQuestion.length) {
                        let h = new Headers()
                        h.append("Content-Type", "application/json")
                        fetch("/qcm/result/" + this.qcm_id, {
                            body: JSON.stringify(qcm),
                            method: "POST",
                            headers: new Headers({'Accept': "application/json"})
                        }).catch(e => console.log(e)).then(x => {
                            if (x.ok) {
                                return x.json()
                            }
                        }).then(x => {
                            if (x?.message === 'ok') {
                                window.location.replace(this.url + "qcm/result/" + this.qcm_id)
                            } else {
                                // error
                            }
                        })
                    }

                })
                this.wrapper.append(btn)
            } else {
                this.markErrors(this.errors)
            }
        }
    }
}


