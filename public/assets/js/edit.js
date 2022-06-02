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

const obto = (value) => {
    if (value instanceof Ob) {
        return value._value;
    }

    if (value.constructor === Array) {
        return value.map(x => obto(x))
    }

    if (value.constructor === Object) {
        for (let xKey in value) {
            let vvalue = value[xKey];
            if (vvalue instanceof Ob) {
                value[xKey] = obto(vvalue)
            }
        }
    }

    return value;
}

let msg = new Ob("");
let a = new Ob([{'message': msg}]);

// a.sub((value) => console.log("je suis la nouvelle valeur %s", value))


// setTimeout(() => {
//     msg.value = "Yuna"
//     console.log(a)
//
//     let clone = JSON.parse(JSON.stringify(a));
//     console.log(clone)
//     let ne = clone._value.map(x => {
//         return obto(x)
//     })
//
//     console.log(ne)
//     a.value[0]._value = "Allan"
//     console.log(ne)
//     console.log(a)
//
// }, 1000)

class QcmEdit extends HTMLElement {
    constructor() {
        super();
        this.shadow = this.attachShadow({mode: 'open'})

        this.wrapper = document.createElement("div")
        this.wrapper.classList.add("qcm-edit-wrapper")
        this.max = 30;
    }

    connectedCallback() {
        this.questions = new Ob([]);
        this.qcmtitle = "";
        this.qcmid = -1;
        this.type = this.getAttribute("data-type") ?? "new";
        this.token = this.getAttribute("data-token")
        this.url = this.getAttribute("data-url")
        this.assets = this.getAttribute("data-asset")
        if (this.type === "edit") {
            this.fromJson(this.getAttribute("data"))
            this.qcmid = this.getAttribute("data-id")
            this.qcmtitle = this.getAttribute("data-title")
        }
        this.questions.sub((value) => {
            this.render();
        })
        let style = document.createElement("style")
        style.innerHTML = `@import url('${this.assets}css/styles.css');`
        this.shadow.append(style)
        this.errorElement = document.createElement("div")
        this.errorElement.classList.add("qcm-edit-error")
        this.shadow.append(this.errorElement, this.wrapper)

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

        if (value.constructor === Array) {
            return value.map(v => this.ObToJson(v))
        }

        if (value.constructor === Object) {
            if (value?._listeners) {
                return value._value;
            }

            for (let key in value) {
                let kvalue = value[key];
                if (kvalue?._listeners) {
                    value[key] = this.ObToJson(kvalue._value)
                }
            }
            return value;
        }

        return value;
    }

    markError(errors) {
        console.log(errors)
        // clear error
        this.errorElement.style.opacity = 0;
        let childs = [...this.errorElement.children]
        childs.forEach(x => x.remove())
        for (let error of errors) {
            let div = document.createElement("div")
            let link = document.createElement("span")
            if (error?.other?.id) {
                link.innerText = `${error.message}`;
                link.href = `#${error?.other?.id}`;
                link.addEventListener("click", (e) => {
                    let find = this.shadow.getElementById(error?.other?.id)
                    console.log(find)
                    if (typeof find !== "undefined") {
                        // window.scrollTo()
                        find.scrollIntoView({behavior: "smooth"});
                        find.classList.add("target")
                        setTimeout(x => {
                            find.classList.remove('target')
                        }, 2000)
                    }
                })
            } else {
                link.innerText = `${error.message}`;
            }

            div.append(link)
            this.errorElement.appendChild(div)
        }

        this.errorElement.style.opacity = 1;

    }

    toJson() {
        //https://developer.mozilla.org/en-US/docs/Glossary/Deep_copy
        // Make a deep copy to avoid undefined value
        let clone = JSON.parse(JSON.stringify(this.questions));
        let errors = [];
        let json = clone._value.map(qcm => {
            return this.ObToJson(qcm)
        }).map(question => {
            let correctIndex = -1;
            if (question.answers.length === 1) {
                errors.push({message: "Vous devez avoir plus d'un réponse !", other: {id: question.id}})
            }
            question.answers = question.answers.map((x, i) => {
                if (x.correct === 1) {
                    correctIndex = i;
                }
                return x.message;
            })
            if (correctIndex === -1) {
                errors.push({message: "Vous devez choisir une bonne réponse !", other: {id: question.id}})
            }
            return {...question, correct: correctIndex};
        })

        if (errors.length === 0) {
            return json;
        } else return {error: errors};
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
        this.wrapper.innerHTML = /*HTML*/"<input id='title' placeholder='Le titre'><div class='edit-buttons'><button id='add' class='button-cool'><i class='sm md las la-plus-circle'></i> <span class='lg'>ajout d'une question</span> </button><button id='save' class='button-cool'> <i class='sm md las la-save'></i> <span class='lg'>sauvegarder</span></button></div> <div id='qcm-edit'></div>";
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
            if (this.questions.value.length === 0) {
                return this.markError([{message: "Vous devez ajouter une question", other: {}}]);
            }
            let save = this.toJson();
            if (save?.error) {
                return this.markError(save?.error)
            }
            let url = this.type === 'edit' ? this.url + "qcm/edit" : this.url + "qcm/save";
            if (this.qcmtitle === "") {
                this.qcmtitle = "Votre titre";
                return this.markError([{message: "Vous devez remplir le titre du QCM", other: {}}]);
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
                            this.token = x?.token;
                        } else if (this.type === "new" && x?.id) {
                            window.location.replace(this.url + "qcm/view/" + x?.id)
                        }

                    }
                }
            }).catch(e => console.error(e))
        })

        let edit = this.shadow.getElementById("qcm-edit")
        this.questions.value.forEach(question => {
            let wrapper = document.createElement("div")
            wrapper.classList.add("edit-question")
            wrapper.id = question.id;
            wrapper.setAttribute("data-question-id", question.id)
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