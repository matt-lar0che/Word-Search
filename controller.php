<?php
    require "wordsearch.php";
    $TPL = [];
    if ($_SERVER["REQUEST_METHOD"] === "GET"){
        session_start();
        $action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_SPECIAL_CHARS);
        if ($action === null){
            echo "ERROR: action is not set";
        }
        if ($action === "board"){
            if (!isset($_SESSION["board"])){
                $wordList = loadWords("words.txt");
                $words = getRandomWordList($wordList, 15);
                $board = generateBoard($words, 20);
                $_SESSION["board"] = $board;
                echo json_encode($board);
            }
            else{
                echo json_encode($_SESSION["board"]);
            }
        }
        else if ($action === "wordList"){
            $list = fetchWordList();
            echo json_encode($list);
        }
        
    }
    else if ($_SERVER["REQUEST_METHOD"] === "POST"){
        session_start();
        //will filter later so json_decode dosen't mess up
        $coords = $_POST["coords"]; /*filter_input(INPUT_POST, "coords", FILTER_SANITIZE_SPECIAL_CHARS);
        if ($coords === null){
            echo "ERROR: No value provided for coordinates";
            exit();
        }
        */
        $coordinates = json_decode($coords, true);
        if ($coordinates === null){
            echo "ERRROR: Please provide valid json";
            exit();
        }
        $result = validateCoords($coordinates["array"]);
        if ($result === false){
            echo "ERRROR: Please provide valid integers";
            exit();
        }
        $matchingCoords = validateMatchingCoords($result);
        if ($matchingCoords === false){
            //TODO: Eventually change this to set an error message back to the client
            echo "ERROR: Too many false guesses inserted";
            exit(0);
        }
        validateMatchingWordsCoords($matchingCoords);
        echo "true";
    }
?>