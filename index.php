<!DOCTYPE html>
<html lang="fr">
<head>
    <style>
        canvas {
            border: 2px solid;
        }

        .popup {
            display: none; /* Caché par défaut */
            position: fixed; /* Positionné par rapport à la fenêtre */
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            border: 2px solid #000;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
        }

        .popup ul {
            list-style-type: none;
            padding: 0;
        }

        .popup ul li {
            margin: 5px 0;
        }

        .popup button {
            display: block;
            margin: 20px auto 0;
        }
        #playerForm {
            display: none;
        }
    </style>
</head>

<body>
    <canvas width="400" height="400"></canvas>

    <div class="popup" id="popup" >
        <h1>ScoreBoard</h1>
        <div id="score"></div>
        <ul id="scoreList"></ul>
        <div id="addName"></div>
        <form id="playerForm" action="score.php" method="POST">
            <label for="playerName">Your part of the 20 best player! Fill your name to be in the leaderboard:</label>
            <input type="text" id="playerName" name="playerName">
            <input type="hidden" id="playerScoreHidden" name="playerScoreHidden">
            <button type="submit">Submit</button>
        </form>
        <button onclick="closePopup()">Fermer</button>
    </div>

    <script>
        const canvas = document.querySelector("canvas")
        const context = canvas.getContext("2d")

        let box = 20

        let snake = []

        let score = 0

        snake[0] = {x: 10*box, y:10*box}

        let food = getNewFood(snake)

        let d

        document.addEventListener("keydown", getDirection)

        function gameOver(score) {
            fetch('score.php')
                .then(response => response.json())
                .then(data => {
                    const scoreList = document.getElementById("scoreList");
                    const playerFinalScore = document.getElementById("score");
                    const addScore = document.getElementById("addName");
                    var form = document.getElementById("playerForm");

                    scoreList.innerHTML = "";

                    const minScore = Math.min(...data.map(score => score.score));

                    data.forEach(score => {
                        const li = document.createElement("li");
                        li.textContent = `${score.name}: ${score.score}`;
                        scoreList.appendChild(li);
                    });
                    if (score > minScore) {
                        form.style.display = "block"
                        document.getElementById("playerScoreHidden").value = score;
                    } else {
                        form.style.display = "none"
                    }
                    playerFinalScore.textContent = "Youre score is " + minScore

                    const popup = document.getElementById("popup");
                    popup.style.display = "block";
                })
                .catch(error => console.error('Error fetching scores:', error));
        }

        function closePopup() {
            const popup = document.getElementById("popup");
            popup.style.display = "none";
            snake = []
            score = 0

            snake[0] = {x: 10*box, y:10*box}

            food = getNewFood(snake)

            d = null
            game = setInterval(draw, 100);
        }

        function getDirection(event){
            if (event.keyCode == 37 && d != "R") {
                d = "L"
            } else if (event.keyCode == 38 && d != "D") {
                d = "U"
            } else if (event.keyCode == 39 && d != "L") {
                d = "R"
            } else if (event.keyCode == 40 && d != "U") {
                d = "D"
            }
        }

        function getNewFood(snake) {
            let check = true
            while(check == true) {
                check = false
                x = Math.floor(Math.random()*15+1) * box
                y = Math.floor(Math.random()*15+1) * box
                for (let i = 0; i < snake.length; i++) {
                    if (x == snake[i].x && y == snake[i].y) {
                        check = true
                        break
                    }
                }
            }
            return {x: x, y: y}
        }

        function isCollision(x, y, snake) {
            for (let i = 0; i<snake.length; i++) {
                if (snake[i].x == x && snake[i].y == y) {
                    return true
                }
            }
        }

        function draw() {
            context.clearRect(0,0,400,400)

            for (let i=0; i<snake.length; i++) {
                if (i == 0) {
                    context.fillStyle = "red"
                } else {
                    context.fillStyle = "green"
                }
                context.fillRect(snake[i].x, snake[i].y, box, box)
            }

            context.fillStyle = "orange"
            context.fillRect(food.x, food.y, box, box)

            let X = snake[0].x
            let Y = snake[0].y

            if (d == "L") X = X-box
            if (d == "U") Y = Y-box
            if (d == "R") X = X+box
            if (d == "D") Y = Y+box

            if (X == food.x && Y == food.y) {
                score++
                food = getNewFood(snake)
            } else {
                snake.pop()
            }
            
            if (X > 19*box) X = 0
            if (X < 0) X = 19*box
            if (Y > 19*box) Y = 0
            if (Y < 0) Y = 19*box

            if (isCollision(X, Y, snake) || score > 3) {
                clearInterval(game)
                gameOver(score)
            }

            snake.unshift({x: X, y: Y})

            context.fillStyle = "black"
            context.font = "40px Arial"
            context.fillText(score, 9*box, 2*box)

        }

        let game = setInterval(draw, 100)

    </script>
</html>