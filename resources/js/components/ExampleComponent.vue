<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{title}} <span class="badge badge-success">{{categoryName}}</span></div>
                    <div class="card-body text-center drill-body">
                        <button class="btn btn-primary " @click="doDrill" v-if="!isStarted">
                            START
                        </button>
                        <p v-if="isCountDown" style="font-size: 100px;">{{countDownNum}}</p>
                        <template v-if="isStarted && !isCountDown && !isEnd">
                            <p>{{timerNum}}</p>
                            <p class="problem-text">
                            <span v-for="(word, index) in problemWords" :key='index' :class="{'text-primary' :index < currentWordNum}">{{word}}</span>
                            </p>
                        </template>
                        <template v-if="isEnd">
                            <p>あなたのスコア</p>
                            <p>{{typingScore}}</p>    
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import KeyCodeMap from '../master/keymap'
    export default {
        props:['title', 'drill', 'categoryName'],
        data:function() {
            return {
                countDownNum: 3,//カウントダウン用
                timerNum: 30,//タイマー
                missNum: 0,//ミス数
                wpm: 0,//wpm
                isStarted: false,
                isEnd: false,
                isCountDown: false,
                currentWordNum: 0,//現在解答中の文字数
                currentProblemNum: 0,//現在の問題番号
            }
        },
        computed: {
            //問題テキスト
            problemText: function() {
                return this.drill['problem' + this.currentProblemNum]
            },
            //問題テキスト(配列テキスト)
            problemWords: function() {
                return Array.from(this.drill['problem' + this.currentProblemNum])
            },
            //問題の解答キーコード配列
            problemKeyCodes: function() {
                if(!Array.from(this.drill['problem' + this.currentProblemNum]).length) {
                    return null
                }
                //テキストから問題のキーコード配列を生成
                let problemKeyCodes = []
                console.log(Array.from(this.drill['problem' + this.currentProblemNum]))
                Array.from(this.drill['problem' + this.currentProblemNum]).forEach((text) => {
                $.each(KeyCodeMap, (keyText, keyCode) => {

                        if(text === keyText) {
                            problemKeyCodes.push(keyCode);

                        }
                    })
                });

                console.log(problemKeyCodes)

                return problemKeyCodes
            },
            //問題の文字数
            totalWordNum: function() {
                    return this.problemKeyCodes.length
            },
            //タイピングスコア
            typingScore: function() {
                return (this.wpm * 2) * (1 - this.missNum / (this.wpm * 2))
            }
        },
        methods: {
            doDrill: function() {
                this.isStarted = true
                this.isCountDown = true
                this.countDown()
            },
            countDown: function() {
                //効果音読み込み
                const countSound = new Audio('../sounds/Countdown06-2.mp3')

                this.soundPlay(countSound)

                let timer = window.setInterval(() => {
                     this.countDownNum -= 1

                     if(this.countDownNum <= 0) {
                         this.isCountDown = false


                         window.clearInterval(timer)
                         this.countTimer()
                         this.showFirstProblem()

                         return
                     }
                }, 1000)

            },
            showFirstProblem: function() {

                //効果音読み込み
                const okSound = new Audio('../sounds/Quiz-Correct_Answer02-1.mp3');
                const noSound = new Audio('../sounds/Quiz-Wrong_Buzzer02-1.mp3');
                const nextSound = new Audio('../sounds/Quiz-Question03-1.mp3');
                const finishSound = new Audio('../sounds/Phrase03-1.mp3');

                //入力イベント時に入力キーと解答キーをチェック
                $(window).on('keypress', e => {
                    console.log(e.which)
                    if(e.which === this.problemKeyCodes[this.currentWordNum]) {
                        console.log('正解')

                        this.soundPlay(okSound)

                        ++this.currentWordNum
                        ++this.wpm
                        console.log('現在解答の文字数目:' + this.currentWordNum)

                        //全文字正解終わったら次の問題へ
                        if(this.totalWordNum === this.currentWordNum) {
                            console.log('次の問題へ')
                            if(this.currentProblemNum < 9) {
                                ++this.currentProblemNum
                                this.currentWordNum = 0

                                this.soundPlay(nextSound)
                            } else {
                                this.isEnd = true
                                this.soundPlay(finishSound)
                            }
                            
                        }
                    } else {
                        console.log('不正解です')

                        this.soundPlay(noSound)
                        ++this.missNum

                        console.log('現在解答の文字数目:' + this.currentWordNum)
                    }
                })
            },
            soundPlay: function(sound) {
                sound.currentTime = 0
                sound.play()
            },
            countTimer: function() {
                const finishSound = new Audio('../sounds/Phrase03-1.mp3');

                let timer = window.setInterval(() => {
                    this.timerNum -= 1

                    if(this.timerNum <= 0) {
                        this.isEnd = true

                        window.clearInterval(timer)
                        this.soundPlay(finishSound)
                    }
                }, 1000)
            } 

        }
    }
</script>
