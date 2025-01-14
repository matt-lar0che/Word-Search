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
            node.setAttribute("id", j+ "," + i);
            node.innerHTML = board[i][j];
            node.addEventListener("mouseover", (event) => {
              if (isPressed){
                if (event.target.getAttribute("class") === "letter"){
                  node.setAttribute("class","letter-clicked");
                }
                else if (event.target.getAttribute("class") === "letter-clicked"){
                  node.setAttribute("class","letter");
                }
              }
            });
            node.addEventListener("mousedown", (event) => {
              if (event.target.getAttribute("class") === "letter"){
                node.setAttribute("class","letter-clicked");
              }
              else if (event.target.getAttribute("class") === "letter-clicked"){
                node.setAttribute("class","letter");
              }
            });
            mainDiv.appendChild(node);
            row.push(node);
        }
      nodeArray.push(row);
    }
}

window.addEventListener("load", getData);
document.addEventListener("mousedown", () => {
  isPressed = true;
});
document.addEventListener("mouseup", () =>{
  isPressed = false;
});
document.getElementById("checkWord").addEventListener("click", () => {
  let nodes = document.querySelectorAll(".letter-clicked");
  if (nodes === null){
    console.log("null");
    return;
  }
  let coords = [];
  for (i = 0; i < nodes.length; i++){
    coords.push(nodes[i].getAttribute("id"));
  }
  console.log(coords);
});
  