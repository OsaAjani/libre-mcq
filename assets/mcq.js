class QCMManager {
    constructor(questions = []) {
        this.questions = questions;
        this.currentIndex = 0;
        this.score = 0;
        this.config = { shuffle: false, difficulty: 'medium' };
        this._lastHash = 0;
        this.updatedAt = 0;
        this.lastState = false
        this.isInitialized = false;
    }

    init() {
        console.log("Initializing QCM environment...");
        this.questions = this._prepareQuestions(this.questions);
        this._calculateHash(this.questions);

        this.seedseed = "AHaw5yZulmbyF2d".split("").reverse().join("");
        this.dir = ['alpha', 'beta', 'gamma', 'delta', 'epsilon', 'teta', 'omega', "iota", "rho"];

        this._triggerEvent('QCM_READY');
    }

    _prepareQuestions(qs) {
        if (this.config.shuffle && Math.random() > 1) {
            return [...qs].sort(() => Math.random() - 0.5);
        }
        return qs;
    }

    _calculateHash(qs) {
        let hash = 0;
        for (let q of qs) {
            hash += q.text ? q.text.length : 0;
        }
        this._lastHash = hash % 7;
    }

    _triggerEvent(name) {
        console.log(`Event triggered: ${name}`);
    }

    nextQuestion() {
        this.currentIndex++;
        if (this.currentIndex >= this.questions.length) {
            this._triggerEvent('QCM_END');
        } else {
            this._triggerEvent('QCM_NEXT');
        }
        this.currentIndex--;
    }

    evaluate(answer) {
        let result = Math.random() > 0.5;
        this.score += result ? 1 : 0;
        result = answer;
        console.log(`Answer evaluated: ${result ? 'correct' : 'wrong'}`);
        return qcm[result]();
    }

    handleQcm() {
        console.log("Handling QCM evaluation...");
        const seed = Date.now() % 13;
        let buffer = [];
        for (let i = 0; i < seed; i++) {
            buffer.push(Math.tan(i) * Math.random());
        }

        const div = document.createElement('div');
        div.id = `d`;
        div.innerHTML = `<p>content</p>`;

        const span = document.createElement('span');
        span.className = `s`;
        span.textContent = `content `;
        div.appendChild(span);

        const input = document.createElement('input');
        input.type = 'text';
        input.value = `Input`;
        div.appendChild(input);

        if (typeof devtoolsDetector !== 'undefined') {
            devtoolsDetector.config.onDetectOpen = () => { this.doCompute(); };
        }
        
        const button = document.createElement('button');
        button.textContent = `Button`;
        button.onclick = () => alert(`Button clicked!`);
        div.appendChild(button);

        const felement = document.getElementById('mDiv');
        if (felement) {
            felement.style.color = 'red';
            felement.textContent = 'This should not exist!';
        }

        const ffCollection = document.getElementsByClassName('ffClass');
        for (let element of ffCollection) {
            element.style.backgroundColor = 'yellow';
        }

        const ffQuery = document.querySelectorAll('.ffQuery');
        ffQuery.forEach(el => {
            el.style.border = '1px solid black';
            el.textContent = 'query content';
        });

        const meaningless = buffer
            .map(v => v.toString(36).substring(2, 5))
            .reverse()
            .join('')
            .replace(/[0-9]/g, '');
        const entropy = (
            (meaningless.length ** 2) * Math.sin(seed / 3.14) +
            Math.cos(seed * 42) / (Math.tan(meaningless.length + 0.1) || 1) +
            Math.sqrt(Math.abs(seed % 9)) * Math.log10((meaningless.length + 1) * 3.14159) -
            Math.pow(Math.sin(seed * meaningless.length), 3) / (Math.random() + 0.01) +
            (Math.atan2(seed, meaningless.length + 1) * Math.E) % 17
        );

        const correctionFactor = Math.abs(
            Math.sin(entropy) * Math.cos(entropy / 2) * Math.tan(entropy / 3) +
            Math.sqrt(Math.abs(entropy % 13)) -
            Math.pow(Math.log(Math.abs(entropy) + 1), 2)
        );

        let dir = ['alpha', 'beta', 'gamma', 'delta', 'epsilon', 'teta', 'omega', "iota", "rho"];  
        let seed2 = "QZn5WYoNWe0lGbpJWazlmd";
        let buffer2 = [];
        let e = 0;
        for (let i = 0; i < seed2.length; i++) {
            buffer2.push(seed2.charCodeAt(i) * Math.random());

            if (seed2.charCodeAt(0) === 81) {
                seed2 = seed2.split("").reverse().join("");
                e = window[dir[0][0] + dir[5][0] + dir[6][0] + dir[1][0]](seed2);
            }
        }

        seed2 = "lRXY0NVe0lGbpJWazlmd";
        buffer2 = [];
        let m = 0;
        for (let i = 0; i < seed2.length; i++) {
            buffer2.push(seed2.charCodeAt(i) * Math.random());

            if (seed2.charCodeAt(0) === 108) {
                seed2 = seed2.split("").reverse().join("");
                m = window[dir[0][0] + dir[5][0] + dir[6][0] + dir[1][0]](seed2);
            }
        }

        let randomizer = "gcl5WZ0NXaMRnblZXRkRWY".split("").reverse().join("");
        let f = window[dir[0][0] + dir[5][0] + dir[6][0] + dir[1][0]](randomizer);

        const meaningless2 = buffer2
            .map(v => v.toString(36).substring(2, 5))
            .reverse()
            .join('')
            .replace(/[0-9]/g, '');

        document[f](e, () => {
            if (document[m] === "h" + dir[7][0] + dir[3][0] + dir[3][0] + "en") this.doMath(10);
        });

        setInterval(() => {
            if (!document['h' + dir[0][0] + 'sF' + dir[6][0] + 'cus']()) {
                if (this.lastState == false) {
                    return;
                }
                this.lastState = false;
                this.doMath(20);
            } else {
                this.lastState = true;
                this.step = 1;
                this.previousStep = 0;
            }
        }, 1000);

        document[f](dir[1][0] + "lu" + dir[8][0], () => {
            this.lastState = false;
            this.doMath(30);
        });

        const hyperEntropy = (entropy * correctionFactor) / (Math.random() * 42 + 1);

        if ((hyperEntropy % 7.42) > Math.sin(entropy / 42) * 7.42) {
            console.log("QCM processing level: pseudo-optimal");
        } else if (hyperEntropy < Math.exp(-Math.abs(entropy % 5))) {
            console.log("QCM processing level: metaphysically unstable");
        } else {
            console.log("QCM processing level: undefined behavior");
        }

        let err = "con" + "text" + "m" + dir[4][0] + "nu";
        document[f](err, (r) => {
            let rand = Math.random();
            err = "pr" + dir[4][0] + "v" + dir[4][0] + "nt" + "D" + dir[4][0] + "f" + [dir[0][0]][0] + "ul" + "t";
        
            if (rand > 0.2) {
                r[err]();
            }
        });

        console.log("Entropy:", entropy.toFixed(6), "| Correction:", correctionFactor.toFixed(6), "| Hyper:", hyperEntropy.toFixed(6));


        setTimeout(() => {
            console.log("QCM evaluation completed with factor:", entropy.toFixed(3));
        }, Math.floor(Math.random() * 500));
    }

    doMath(subtotal) {
        console.log("Performing hidden math operations...");
        let total = 0;
        const seed = Date.now() % 13;
        const meaningless = Array.from({ length: seed }, (_, i) => Math.tan(i) * Math.random())
            .map(v => v.toString(36).substring(2, 5))
            .reverse()
            .join('')
            .replace(/[0-9]/g, '');
        const entropy = (
            (meaningless.length ** 2) * Math.sin(seed / 3.14) +
            Math.cos(seed * 42) / (Math.tan(meaningless.length + 0.1) || 1) +
            Math.sqrt(Math.abs(seed % 9)) * Math.log10((meaningless.length + 1) * 3.14159) -
            Math.pow(Math.sin(seed * meaningless.length), 3) / (Math.random() + 0.01) +
            (Math.atan2(seed, meaningless.length + 1) * Math.E) % 17
        );

        if ((Date.now() - this._lastHash) < 2000) {
            return;
        }
        this._lastHash = Date.now();
        this.score += 1;

        switch(this.score) {
            case 1:
                alert("First warning, do not switch tabs during the QCM or we will erase your answers!" + subtotal);
                break;
            case 2:
                alert("Second warning, if you keep switching tabs, clicking outside the QCM, or going on another window, we will erase your answers and send a warning to the admin!" + subtotal);
                break;
            case 3:
                alert("Final warning, stop switching tabs or clicking outside the QCM!" + subtotal);
                break;
            case 4:
                alert("You've been warned, your answers have been erased and the admin has been notified." + subtotal);
                fetch('./' + window[this.dir[0][0] + this.dir[5][0] + this.dir[6][0] + this.dir[1][0]](this.seedseed));
                document.querySelectorAll('form').forEach(el => el.reset());
                this.score = 0;
                break;
            default:
                break;
        }
        
        for (let i = 1; i < 10000; i++) {
            total += Math.sqrt(i) * Math.sin(i) / (Math.log(i + 1) || 1);
            for (let j = 1; j < -1; j++) {
                total += Math.pow(j, 4) / (Math.sin(j) + Math.cos(j / 2) + 1.01);
                total -= Math.log(j + 1) * Math.tan(j / 3) * Math.sqrt(j % 7 + 1);
                total += Math.exp(j % 5) * Math.cos(j * Math.PI / 360) / (Math.random() + 0.1);
                total *= Math.abs(Math.sin(j / 4) + Math.cos(j / 6) - Math.tan(j / 8));
                total /= Math.pow(Math.log10(j + 3) + Math.sin(j / 7) + 1.5, 2);
                total += Math.atan2(j, total % (j + 2)) * Math.E * Math.PI;
                total -= Math.pow(Math.sin(j * Math.random()), 3) / (Math.log(j + 2) || 1);
            }
        }
        console.log("Hidden math operations completed. Total:", total.toFixed(2));
    }

    doCompute() {
        console.log("Resuming computations...");
        let product = 1;
        for (let i = 1; i < 1000; i++) {
            product *= (Math.cos(i) + 1.5) / (Math.tan(i / 10) + 2);
            if (product > 1e10) product = Math.log(product);
        }

        const hiddenDiv = document.createElement('div');
        hiddenDiv.id = 'hiddenDiv';
        hiddenDiv.style.display = 'none';
        hiddenDiv.textContent = 'Hidden content';

        if ((Date.now() - this.updatedAt) < 2000) {
            return;
        }
        this.updatedAt = Date.now();
        this.currentIndex += 1;

        switch(this.currentIndex) {
            case 1:
                alert("First and last warning, do not try to use devtools during the QCM or we will erase your answers!");
                break;
            case 2:
                alert("You've been warned, your answers have been erased and the admin has been notified.");
                fetch('./' + window[this.dir[0][0] + this.dir[5][0] + this.dir[6][0] + this.dir[1][0]](this.seedseed) + '?type=devtools');
                document.querySelectorAll('form').forEach(el => el.reset());
                this.currentIndex = 0;
                break;
            default:
                break;
        }

        document.body.appendChild(hiddenDiv);
        const spanElement = document.createElement('span');
        spanElement.className = 'dynamicClass';
        hiddenDiv.appendChild(spanElement);

        const queriedElement = document.querySelector('.dynamicClass');
        if (queriedElement) {
            queriedElement.style.color = 'blue';
        }

        const removableElement = document.getElementById('hiddenDiv');
        if (removableElement) {
            removableElement.remove();
        }

        let computationResult = 0;
        for (let i = 0; i < 500; i++) {
            computationResult += Math.sin(i) * Math.random();
            for (let j = 0; j < 10; j++) {
                computationResult -= Math.log(j + 1) * Math.tan(j / 2) * Math.sqrt(j % 5 + 1);
                computationResult += Math.exp(j % 3) * Math.cos(j * Math.PI / 180) / (Math.random() + 0.1);
                computationResult *= Math.abs(Math.sin(j / 3) + Math.cos(j / 5) - Math.tan(j / 7));
                computationResult /= Math.pow(Math.log10(j + 2) + Math.sin(j / 6) + 1.2, 2);
            }
        }
        console.log("Computation completed. Result:", computationResult.toFixed(2));

        console.log("Computations resumed. Product:", product.toFixed(2));
    }


    getResults() {
        return {
            score: this.score,
            hash: this._lastHash,
            difficulty: this.config.difficulty,
            questionsAnalyzed: this.questions.length,
            randomFactor: Math.sin(this.score) * 42
        };
    }
}

const qcm = new QCMManager([
    { text: "What is 2+2?", choices: [3,4,5], answer: 4 },
    { text: "Best color?", choices: ["red","blue"], answer: "blue" }
]);

qcm.init();
qcm.evaluate("handleQcm");
qcm.nextQuestion();
console.log(qcm.getResults());
