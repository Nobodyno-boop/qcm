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

    clone() {
        return Object.assign([], this.ObToJson(this._value))
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
}

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

        this.fromJson(this.getAttribute("data"))
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
        let clone = this.questions.clone();
        console.log(clone)
        console.log(this.questions.value)
        // let json = this.questions._value.map(qcm => {
        //     return this.ObToJson(qcm)
        // }).map(question => {
        //     let correctIndex = -1;
        //     question.answers = question.answers.map((x, i) => {
        //         if(x.correct === 1){
        //             correctIndex = i;
        //         }
        //         return x.message;
        //     })
        //     return {...question, correct: correctIndex};
        // })
        // console.log(json)
        return {};
    }

    fromJson(json) {
        json = JSON.parse(json);
        json.map(x => {
            if (x['answers']) {
                x['answers'] = x['answers'].map(answer => {
                    answer['message'] = new Ob(answer['message'])
                    return answer;
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
        this.wrapper.innerHTML = /*HTML*/"<div class='edit-buttons'><button id='add' class='button-cool'> ajout d'une question </button><button id='save' class='button-cool'> sauvegarder</button></div> <div id='qcm-edit'></div>";
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
            fetch("/qcm/save", {
                method: "POST",
                body: JSON.stringify(save)
            }).then(x => {
                return x.json()
            }).then(x => {
                console.log(x)
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

            let title = document.createElement("input")
            title.placeholder = "Votre question.."
            this.setEdit(question.question, title)

            let list = document.createElement("ul")

            question.answers.value.forEach(an => {
                let answer = this.createAnwser(an, question.answers)
                list.appendChild(answer)
            })

            wrapper.replaceChildren(title, add, list)
            edit.appendChild(wrapper)
        })
    }
}


export default QcmEdit