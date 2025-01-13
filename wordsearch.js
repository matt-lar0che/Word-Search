var isPressed = false;
var nodeArray;
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
    nodeArray = [];
    mainDiv.style.width = 32 * board.length + "px";
    for (let i = 0; i < board.length; i++){
        var row = [];
        for (let j = 0; j < board.length; j++){
            let node = document.createElement("div");
            node.setAttribute("class","letter");
            node.innerHTML = board[i][j];
            node.addEventListener("mouseover", () => {
              if (isPressed){
                node.setAttribute("class","letter-clicked");
              }
            });
            node.addEventListener("mousedown", () => {
              node.setAttribute("class","letter-clicked");
            });
            mainDiv.appendChild(node);
            row.push(node);
        }
      nodeArray.push(row);
    }
}
function checkValidGuess(){
  let nodes = document.querySelectorAll(".letter-clicked");
}

window.addEventListener("load", getData);
document.addEventListener("mousedown", () => {
  isPressed = true;
});
document.addEventListener("mouseup", () =>{
  isPressed = false;
});
document.getElementById("checkWord").addEventListener("click", () => {
  console.log(nodeArray);
});
  