async function getData() {
    const url = "wordsearch.php";
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`Response status: ${response.status}`);
      }
  
      const json = await response.json();
      createBoard(json);
      console.log(json);
    } catch (error) {
      console.error(error.message);
    }
}

function createBoard(board){
    var mainDiv = document.getElementById("gameGrid");
    mainDiv.innerHTML = "";
    mainDiv.style.width = 32 * board.length + "px";
    for (let i = 0; i < board.length; i++){
        for (let j = 0; j < board.length; j++){
            let node = document.createElement("div");
            node.setAttribute("class","letter");
            node.innerHTML = board[i][j];
            mainDiv.appendChild(node);
        }
    }
}

window.addEventListener("load", getData);
  