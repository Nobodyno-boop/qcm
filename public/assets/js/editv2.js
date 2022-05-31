class Ob {
    constructor(value) {
        this._listeners = [];
        this._value = value;
    }

    sub(callback) {
        this._listeners.push(callback)
    }

    notify() {
        this._listeners.forEach(listener => listener(this._value))
    }

    set value(val) {
        if (val !== this._value) {
            this._value = val;
            this.notify()
        }
    }

    get value() {
        return this._value;
    }
}

let a = new Ob("");

a.sub((value) => console.log("je suis la nouvelle valeur %s", value))

setTimeout(() => {
    a.value = "Yuna"
}, 1000)

class QcmEdit extends HTMLElement {
    constructor() {
        super();
        this.shadow = this.attachShadow({mode: 'open'})

        this.wrapper = document.createElement("div")
        this.wrapper.classList.add("qcm-edit-wrapper")
        let style = document.createElement("style")
        style.innerHTML = "@import url('/assets/css/styles.css'); @import url('https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css')"
        this.shadow.append(style)
        this.shadow.appendChild(this.wrapper)
        this.max = 3;
    }

    connectedCallback() {
        this.questions = new Ob([]);
        this.qcmtitle = "";
        this.qcmid = -1;
        this.type = this.getAttribute("data-type") ?? "new";
        this.token = this.getAttribute("data-token")
        this.url = this.getAttribute("data-url")
        if (this.type === "edit") {
            this.fromJson(this.getAttribute("data"))
            this.qcmid = this.getAttribute("data-id")
            this.qcmtitle = this.getAttribute("data-title")
        }
        this.questions.sub((value) => {
            this.render();
        })
        this.render();

    }

    setEdit(ob, element) {
        ob.sub((value) => {
            element.value = value;
            element.focus()
        })
        element.onkeyup = (e) => {
            ob.value = e.target.value;
        }
        element.value = ob.value
    }

    setIcons(answer, mainOb) {
        let isCorrect = document.createElement("button")
        isCorrect.classList.add("button-cool", "cool-black")
        let isCorrectIcon = document.createElement("i")

        isCorrectIcon.classList.add("las", "la-check-circle")
        if (answer.correct === 1) {
            isCorrectIcon.style.color = "var(--green)"
        }
        isCorrect.append(isCorrectIcon)
        isCorrect.addEventListener("click", (e) => {
            let o = mainOb.value.find(x => x.id === answer.id);
            if (o) {
                o.correct = 1;
                isCorrectIcon.style.color = "var(--green)"
                mainOb._value.forEach(x => {
                    if (x.id !== answer.id) {
                        let el = this.shadow.querySelector(`[data-answer-id='${x.id}']`)
                        if (el) {
                            let i = el.querySelector(".la-check-circle")
                            x.correct = 0
                            i.style.color = "white"
                        }
                    }
                })
            }
        })

        let remove = document.createElement("button")
        remove.classList.add("button-cool")
        let removeIcon = document.createElement("i")
        remove.append(removeIcon)

        removeIcon.classList.add("las", "la-minus-circle")
        remove.addEventListener("click", (e) => {
            mainOb.value = mainOb.value.map(x => {
                if (x.id === answer.id) {
                    return null
                } else return x;
            }).filter(x => !!x)

        })

        return [remove, isCorrect]
    }

    createAnwser(an, mainOb) {
        let question = document.createElement("li")
        question.setAttribute("data-answer-id", an.id)
        question.append(...this.setIcons(an, mainOb))
        let str = document.createElement("input")
        str.placeholder = "Votre réponse.."
        question.appendChild(str)
        this.setEdit(an.message, str)
        return question;
    }

    ObToJson(value) {
        if (value instanceof Ob) {
            return this.ObToJson(value._value)
        }
        if (value.constructor === Array) {
            return value.map(v => this.ObToJson(v))
        }

        if (value.constructor === Object) {
            for (let key in value) {
                let kvalue = value[key];
                if (kvalue instanceof Ob) {
                    value[key] = this.ObToJson(kvalue)
                }
            }
            return value;
        }

        return value;
    }

    toJson() {
        let json = this.questions._value.map(qcm => {
            return this.ObToJson(qcm)
        }).map(question => {
            let correctIndex = -1;
            question.answers = question.answers.map((x, i) => {
                if (x.correct === 1) {
                    correctIndex = i;
                }
                return x.message;
            })
            return {...question, correct: correctIndex};
        })
        return json;
    }

    fromJson(json) {
        json = JSON.parse(json);
        json.map(x => {
            if (x['answers']) {
                x['answers'] = x['answers'].map((answer, i) => {
                    let json = {}

                    json['message'] = new Ob(answer)
                    json['correct'] = i === x['correct'] ? 1 : 0;
                    // fake id
                    json['id'] = Math.random().toString(36).slice(2, 7)
                    return json;
                })
                let n = new Ob(x['answers']);
                n.sub(() => this.render())
                x['answers'] = n;

            }
            x['question'] = new Ob(x['question'])
            return x;
        })
        this.questions = new Ob(json)
    }


    render() {
        this.wrapper.innerHTML = /*HTML*/"<input id='title' placeholder='Le titre'><div class='edit-buttons'><button id='add' class='button-cool'> <span>ajout d'une question</span> </button><button id='save' class='button-cool'> <span>sauvegarder</span></button></div> <div id='qcm-edit'></div>";
        let title = this.shadow.getElementById("title")
        title.value = this.qcmtitle;

        title.addEventListener("input", (e) => {
            this.qcmtitle = title.value
        })
        let addBtn = this.shadow.getElementById("add")
        let saveBtn = this.shadow.getElementById("save")
        addBtn.addEventListener("click", (e) => {
            if (this.max >= this.questions.value.length + 1) {
                let answer = new Ob([{id: Math.random().toString(36).slice(2, 7), message: new Ob(""), correct: 0}]);
                answer.sub(x => this.render());
                this.questions.value = [...this.questions.value, {
                    id: Math.random().toString(36).slice(2, 7),
                    question: new Ob(""),
                    answers: answer
                }]
            }
        })
        saveBtn.addEventListener("click", (e) => {
            let save = this.toJson();
            let url = this.type === 'edit' ? this.url + "qcm/edit" : this.url + "qcm/save";
            if (this.qcmtitle === "") {
                this.qcmtitle = "Votre titre";
            }
            fetch(url, {
                method: "POST",
                body: JSON.stringify({
                    title: this.qcmtitle,
                    qcm: save,
                    id: this.qcmid,
                    token: this.token
                })
            }).then(x => {
                return x.json()
            }).then(x => {
                if (x === []) {
                    location.replace(this.url);
                } else if (x?.message) {
                    if (x.message === "ok") {
                        if (this.type === "edit") {
                            window.location.reload();
                        } else {
                            if (x?.id) {
                                window.location.replace(this.url + "qcm/view/" + x?.id);
                            }
                        }
                    }
                }
            }).catch(e => console.error(e))
        })

        let edit = this.shadow.getElementById("qcm-edit")
        this.questions.value.forEach(question => {
            let wrapper = document.createElement("div")
            wrapper.classList.add("edit-question")
            let add = document.createElement("button")
            add.classList.add("button-cool")
            let iconAdd = document.createElement("i")
            iconAdd.classList.add("las", "la-plus-circle")
            add.addEventListener("click", (e) => {
                let el = this.questions.value.find(x => x.id === question.id)
                if (typeof el !== "undefined") {
                    el.answers.value = [...el.answers.value, {
                        id: Math.random().toString(36).slice(2, 7),
                        message: new Ob(""),
                        correct: 0
                    }]
                }
            })

            add.append(iconAdd)


            let deleteIcon = document.createElement("i")
            deleteIcon.classList.add("las", "la-trash")
            let deleteBtn = document.createElement("button")
            deleteBtn.classList.add("button-cool")
            deleteBtn.append(deleteIcon)

            deleteBtn.addEventListener("click", (e) => {
                this.questions.value = this.questions.value.map(q => {
                    if (q.id !== question.id) {
                        return q;
                    }
                }).filter(x => !!x)
            })

            let title = document.createElement("input")
            title.placeholder = "Votre question.."
            let div = document.createElement("div")
            div.classList.add("edit-input-question")
            div.append(title, deleteBtn)
            this.setEdit(question.question, title)

            let list = document.createElement("ul")

            question.answers.value.forEach(an => {
                let answer = this.createAnwser(an, question.answers)
                list.appendChild(answer)
            })

            wrapper.replaceChildren(div, add, list)
            edit.appendChild(wrapper)
        })
    }
}


export default QcmEdit