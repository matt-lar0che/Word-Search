var isPressed = false;
var nodeArray;
async function getData() {
    const url = "controller.php?action=board";
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

    try {
      const response = await fetch("controller.php?action=wordList");
      if (!response.ok) {
        throw new Error(`Response status: ${response.status}`);
      }
  
      const json = await response.json();
      console.log(json);
      json.forEach(element => {
        let node = document.createElement("p");
        node.innerHTML = element.word;
        if (element.found === true){
          node.style.textDecoration = "line-through";
        }
        document.getElementById("wordList").appendChild(node);
      });
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
document.getElementById("checkWord").addEventListener("click", async () => {
  let nodes = document.querySelectorAll(".letter-clicked");
  if (nodes === null){
    console.log("null");
    return;
  }
  let coordinates = [];
  for (i = 0; i < nodes.length; i++){
    coordinates.push(nodes[i].getAttribute("id"));
  }
  let jsonCoords = JSON.stringify(coordinates);
  console.log(jsonCoords);

  try {
    let data = 'coords={"array": ' + jsonCoords + '}';
    const response = await fetch("controller.php",{
      method: 'POST',
      headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
      body: data
    });
    if (!response.ok) {
      throw new Error(`Response status: ${response.status}`);
    }

    const json = await response.json();
    document.getElementById("response").innerHTML = JSON.stringify(json);
  } catch (error) {
    console.error(error.message);
  }
});