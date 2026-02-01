<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic-Tac-Toe Universe</title>
    <style>
        :root {
            --bg: #b4b6ba;
            --card: #ffffff;
            --primary: #2d3436;
            --accent: #f1c40f;
            --player-x: #d63031;
            --player-o: #0984e3;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: var(--bg); text-align: center; font-family: 'Segoe UI', system-ui, sans-serif; padding: 20px; }
        .screen { display: none; max-width: 800px; margin: 0 auto; }
        .screen.active { display: flex; flex-direction: column; align-items: center; }
        #main-menu { height: 80vh; justify-content: center; gap: 20px; }
        h1 { font-size: 2.5rem; margin-bottom: 20px; color: var(--primary); }
        .btn {
            padding: 15px 30px; font-size: 1.1rem; border: none; border-radius: 8px;
            background: var(--primary); color: white; cursor: pointer; transition: 0.2s;
            min-width: 250px;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .btn-secondary { background: #636e72; margin-top: 20px; }
        .classic-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;
            width: 300px; height: 300px; margin: 20px auto;
        }
        .ultimate-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
            background: #333; padding: 12px; border-radius: 10px; margin: 20px auto;
        }
        .big-region {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px;
            background: #bdc3c7; padding: 4px; position: relative;
            width: 150px; height: 150px;
        }
        .cell {
            background: white; border: none; font-weight: bold; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        .classic-cell { width: 93px; height: 93px; font-size: 2.5rem; border-radius: 8px; }
        .ultimate-cell { width: 44px; height: 44px; font-size: 1.2rem; }
        .big-region.active { background-color: var(--accent); }
        .big-region.won-X::after, .big-region.won-O::after {
            content: attr(data-winner); position: absolute; top: 0; left: 0;
            width: 100%; height: 100%; display: flex; justify-content: center;
            align-items: center; font-size: 5rem; font-weight: bold;
            background: rgba(255,255,255,0.85); z-index: 5;
        }
        .big-region.won-X::after { color: var(--player-x); }
        .big-region.won-O::after { color: var(--player-o); }
        .score-board { margin: 15px 0; font-size: 1.2rem; font-weight: bold; }
        .hide { display: none; }
    </style>
</head>
<body>

    <div id="main-menu" class="screen active">
        <h1>Tic-Tac-Toe</h1>
        <button class="btn" onclick="startClassic(1)">Classic: 1 Player</button>
        <button class="btn" onclick="startClassic(2)">Classic: 2 Players</button>
        <button class="btn" onclick="startUltimate(1)">Ultimate: 1 Player</button>
        <button class="btn" onclick="startUltimate(2)">Ultimate: 2 Players</button>
    </div>

    <div id="classic-screen" class="screen">
        <h2 id="classic-title">Classic Mode</h2>
        <div class="score-board" id="classic-score">X: 0 | O: 0</div>
        <div id="classic-status">Turn: X</div>
        <div class="classic-grid" id="classic-board"></div>
        <button class="btn hide" id="classic-next" onclick="resetClassic()">Next Round</button>
        <button class="btn btn-secondary" onclick="showMenu()">Main Menu</button>
    </div>

    <div id="ultimate-screen" class="screen">
        <h2 id="ultimate-title">Ultimate Mode</h2>
        <div class="score-board" id="ultimate-score">X: 0 | O: 0</div>
        <div id="ultimate-status">Turn: X</div>
        <div class="ultimate-grid" id="ultimate-board"></div>
        <button class="btn hide" id="ultimate-next" onclick="resetUltimate()">Next Round</button>
        <button class="btn btn-secondary" onclick="showMenu()">Main Menu</button>
    </div>

    <script>
        let currentPlayers = 2; 
        let currentGameType = "classic";
        let currentPlayer = "X";
        let gameActive = true;
        const scores = { classic1: { X: 0, O: 0 }, classic2: { X: 0, O: 0 }, ultimate1: { X: 0, O: 0 }, ultimate2: { X: 0, O: 0 } };
        const winConditions = [[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]];

        function showScreen(id) {
            document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
            document.getElementById(id).classList.add('active');
        }

        function showMenu() { showScreen('main-menu'); }
        function getScoreKey() { return currentGameType + currentPlayers; }

        function startClassic(p) {
            currentPlayers = p; currentGameType = "classic";
            document.getElementById('classic-title').innerText = p === 1 ? "Classic: vs AI" : "Classic: 2 Players";
            showScreen('classic-screen'); updateScoreUI('classic'); resetClassic();
        }

        let classicState = Array(9).fill("");
        function resetClassic() {
            classicState = Array(9).fill(""); gameActive = true;
            currentPlayer = Math.random() < 0.5 ? "X" : "O";
            document.getElementById('classic-next').classList.add('hide');
            renderClassicBoard(); updateStatus('classic');
            if (currentPlayers === 1 && currentPlayer === "O") setTimeout(classicAiMove, 500);
        }

        function renderClassicBoard() {
            const board = document.getElementById('classic-board');
            board.innerHTML = "";
            classicState.forEach((val, i) => {
                const cell = document.createElement('button');
                cell.className = 'cell classic-cell';
                cell.innerText = val;
                if (val === "X") cell.style.color = "var(--player-x)";
                if (val === "O") cell.style.color = "var(--player-o)";
                cell.onclick = () => { if (gameActive && classicState[i] === "" && !(currentPlayers === 1 && currentPlayer === "O")) executeClassicMove(i); };
                board.appendChild(cell);
            });
        }

        function executeClassicMove(i) {
            classicState[i] = currentPlayer;
            renderClassicBoard();
            if (checkWin(classicState, currentPlayer)) endGame('classic', currentPlayer);
            else if (classicState.every(s => s !== "")) endGame('classic', "Tie");
            else {
                currentPlayer = currentPlayer === "X" ? "O" : "X";
                updateStatus('classic');
                if (currentPlayers === 1 && currentPlayer === "O" && gameActive) setTimeout(classicAiMove, 500);
            }
        }

        function classicAiMove() {
            let bestScore = -Infinity; let move;
            for (let i = 0; i < 9; i++) {
                if (classicState[i] === "") {
                    classicState[i] = "O";
                    let score = minimax(classicState, 0, false);
                    classicState[i] = "";
                    if (score > bestScore) { bestScore = score; move = i; }
                }
            }
            executeClassicMove(move);
        }

        function minimax(board, depth, isMax) {
            if (checkWin(board, "O")) return 10 - depth;
            if (checkWin(board, "X")) return depth - 10;
            if (board.every(s => s !== "")) return 0;
            if (isMax) {
                let best = -Infinity;
                for (let i = 0; i < 9; i++) { if (board[i] === "") { board[i] = "O"; best = Math.max(best, minimax(board, depth + 1, false)); board[i] = ""; } }
                return best;
            } else {
                let best = Infinity;
                for (let i = 0; i < 9; i++) { if (board[i] === "") { board[i] = "X"; best = Math.min(best, minimax(board, depth + 1, true)); board[i] = ""; } }
                return best;
            }
        }

        function startUltimate(p) {
            currentPlayers = p; currentGameType = "ultimate";
            document.getElementById('ultimate-title').innerText = p === 1 ? "Ultimate: vs AI" : "Ultimate: 2 Players";
            showScreen('ultimate-screen'); updateScoreUI('ultimate'); resetUltimate();
        }

        let uGlobal = Array(9).fill("");
        let uSmall = Array.from({length: 9}, () => Array(9).fill(""));
        let nextBoard = -1;

        function resetUltimate() {
            uGlobal.fill(""); uSmall = Array.from({length: 9}, () => Array(9).fill(""));
            gameActive = true; nextBoard = -1; currentPlayer = Math.random() < 0.5 ? "X" : "O";
            document.getElementById('ultimate-next').classList.add('hide');
            renderUltimateBoard(); updateStatus('ultimate');
            if (currentPlayers === 1 && currentPlayer === "O") setTimeout(ultimateAiMove, 600);
        }

        function renderUltimateBoard() {
            const board = document.getElementById('ultimate-board');
            board.innerHTML = "";
            for (let b = 0; b < 9; b++) {
                const region = document.createElement('div');
                region.className = 'big-region';
                if (gameActive && (nextBoard === -1 || nextBoard === b) && uGlobal[b] === "") region.classList.add('active');
                if (uGlobal[b] !== "" && uGlobal[b] !== "T") { region.classList.add(`won-${uGlobal[b]}`); region.setAttribute('data-winner', uGlobal[b]); }
                for (let c = 0; c < 9; c++) {
                    const cell = document.createElement('button');
                    cell.className = 'cell ultimate-cell';
                    cell.innerText = uSmall[b][c];
                    if (uSmall[b][c] === "X") cell.style.color = "var(--player-x)";
                    if (uSmall[b][c] === "O") cell.style.color = "var(--player-o)";
                    cell.onclick = () => { if (gameActive && uSmall[b][c] === "" && (nextBoard === -1 || b === nextBoard) && uGlobal[b] === "" && !(currentPlayers === 1 && currentPlayer === "O")) executeUltimateMove(b, c); };
                    region.appendChild(cell);
                }
                board.appendChild(region);
            }
        }

        function executeUltimateMove(b, c) {
            uSmall[b][c] = currentPlayer;
            if (checkWin(uSmall[b], currentPlayer)) uGlobal[b] = currentPlayer;
            else if (uSmall[b].every(s => s !== "")) uGlobal[b] = "T";

            if (checkWin(uGlobal, currentPlayer)) endGame('ultimate', currentPlayer);
            else if (uGlobal.every(s => s !== "")) endGame('ultimate', "Tie");
            else {
                currentPlayer = currentPlayer === "X" ? "O" : "X";
                nextBoard = (uGlobal[c] === "") ? c : -1;
                renderUltimateBoard(); updateStatus('ultimate');
                if (currentPlayers === 1 && currentPlayer === "O" && gameActive) setTimeout(ultimateAiMove, 600);
            }
        }

        function ultimateAiMove() {
            let bestScore = -Infinity; let move = null;
            let possible = [];
            for (let b = 0; b < 9; b++) {
                if ((nextBoard === -1 || b === nextBoard) && uGlobal[b] === "") {
                    for (let c = 0; c < 9; c++) if (uSmall[b][c] === "") possible.push({b, c});
                }
            }
            
            for (let m of possible) {
                uSmall[m.b][m.c] = "O";
                let score = alphaBetaUltimate(uSmall, uGlobal, m.c, 4, -Infinity, Infinity, false);
                uSmall[m.b][m.c] = "";
                if (score > bestScore) { bestScore = score; move = m; }
            }
            executeUltimateMove(move.b, move.c);
        }

        function alphaBetaUltimate(small, global, nxtB, depth, alpha, beta, isMax) {
            if (checkWin(global, "O")) return 1000;
            if (checkWin(global, "X")) return -1000;
            if (depth === 0) return evaluateUltimate(small, global);

            let moves = [];
            let target = (global[nxtB] === "") ? nxtB : -1;
            for (let b = 0; b < 9; b++) {
                if (target === -1 || b === target) {
                    if (global[b] === "") for (let c = 0; c < 9; c++) if (small[b][c] === "") moves.push({b, c});
                }
            }

            if (isMax) {
                let v = -Infinity;
                for (let m of moves) {
                    small[m.b][m.c] = "O";
                    let oldG = global[m.b];
                    if (checkWin(small[m.b], "O")) global[m.b] = "O";
                    v = Math.max(v, alphaBetaUltimate(small, global, m.c, depth - 1, alpha, beta, false));
                    global[m.b] = oldG; small[m.b][m.c] = "";
                    alpha = Math.max(alpha, v);
                    if (beta <= alpha) break;
                }
                return v;
            } else {
                let v = Infinity;
                for (let m of moves) {
                    small[m.b][m.c] = "X";
                    let oldG = global[m.b];
                    if (checkWin(small[m.b], "X")) global[m.b] = "X";
                    v = Math.min(v, alphaBetaUltimate(small, global, m.c, depth - 1, alpha, beta, true));
                    global[m.b] = oldG; small[m.b][m.c] = "";
                    beta = Math.min(beta, v);
                    if (beta <= alpha) break;
                }
                return v;
            }
        }

        function evaluateUltimate(small, global) {
            let score = 0;
            global.forEach((val, i) => {
                if (val === "O") score += (i === 4 ? 50 : 30);
                if (val === "X") score -= (i === 4 ? 50 : 30);
            });
            return score;
        }

        function checkWin(board, p) { return winConditions.some(c => board[c[0]] === p && board[c[1]] === p && board[c[2]] === p); }
        function updateStatus(type) { document.getElementById(`${type}-status`).innerText = `Turn: ${currentPlayer}`; }
        function updateScoreUI(type) {
            const key = getScoreKey();
            const l1 = currentPlayers === 1 ? "You (X)" : "Player X";
            const l2 = currentPlayers === 1 ? "AI (O)" : "Player O";
            document.getElementById(`${type}-score`).innerText = `${l1}: ${scores[key].X} | ${l2}: ${scores[key].O}`;
        }
        function endGame(type, res) {
            gameActive = false;
            if (res !== "Tie") { scores[getScoreKey()][res]++; updateScoreUI(type); document.getElementById(`${type}-status`).innerText = `${res} Wins!`; }
            else document.getElementById(`${type}-status`).innerText = "It's a Draw!";
            document.getElementById(`${type}-next`).classList.remove('hide');
            if (type === 'ultimate') renderUltimateBoard();
        }
    </script>
</body>
</html>