<?php
    /*
    Author: Matthieu Laroche
    Date: January 12th 2025
    Purpose: This file contains the model for all functions of the crossword website.
    */
    /**
     * This function opens a file and reads the words it contains into an array.
     * It assumes that the file will contain one word per line.
     *
     * @param string $filename The name of the file to load from
     * 
     * @author Matthieu Laroche
     * @return array|null Returns an array of words on success, or null if there was an error opening the file
     */    
    function loadWords($filename){
            $wordArray = [];
            $fh = fopen($filename, "r");
            if ($fh === false){
                echo "There was an opening the file";
                return null;
            }
            while (!feof($fh)){
                $line = fgets($fh);
                $line = substr($line,0, strlen($line) - 2);
                $newWord = filter_var($line, FILTER_SANITIZE_SPECIAL_CHARS);
                $wordArray[] = $newWord;
            }
            fclose($fh);
            return $wordArray;
        }

    /**
     * This takes an array of words, and randomly selectes a specified amount of them from the list
     *
     * @param array|Countable $wordList the array of words to search
     * @param int $amount the amount of words to search for
     * 
     * @author Matthieu Laroche
     * @return array|null Returns an array of randomly selected words on success,
     * or null if there was an error with the provided parameters
     */  
    function getRandomWordList($wordList, $amount){
        $randomWords = [];
        if ($wordList === null){
            echo "ERROR: Provided list is null";
            return null;
        }
        if ($amount === null || $amount < 1){
            echo "ERROR: Amount of words to search for is invalid";
            return null;
        }
        for ($i = 0; $i < $amount; $i++){
            $validated = false;
            while (!$validated){
                $newWord = strtoupper($wordList[rand(0, count($wordList) - 1)]);
                $validated = true;
                foreach($randomWords as $word){
                    if ($newWord === $word){
                        $validated = false;
                        break;
                    }
                }
                if ($validated === true){
                    $randomWords[] = strtoupper($newWord);
                }
            }
        }
        return $randomWords;
    }
    /**
     * This takes an array of words, and creates a word search board containing them
     *
     * @param array|Countable $wordList the array of words to use
     * @param int $size the size of the board
     * 
     * @author Matthieu Laroche
     * @return array|null Returns an array representing the word search board,
     * or null if there was an error with the provided parameters
     */  
    function generateBoard($wordList, $size){
        global $TPL;
        if ($wordList === null || $size === null){
            echo "ERROR: Parameters not set properly";
            return null;
        }
        foreach($wordList as $word){
            if ($size < strlen($word)){
                echo "ERROR: size must be greater than provided words";
                return null;
            }
        }

        //generate a blank board
        $board = [];
        for ($i = 0; $i < $size; $i++){
            $line = [];
            for ($j = 0; $j < $size; $j++){
                $line[] = " ";
            }
            $board[] = $line;
        }

        foreach($wordList as $word){
            $orientation = rand(0,8);
            if ($orientation === 0){
                //this is for vertical words
                $validated = false;
                while (!$validated){
                    //generate a random starting coord for the word
                    $randX = rand(0,$size - 1);
                    $randY = rand(0, $size - 1);
                    //make sure it will not go out of bounds of the board, if so, reroll
                    if ($randY + (strlen($word) - 1) > $size - 1){
                        continue;
                    }
                    $temp = $randY;
                    $validated = true;
                    //check and make sure either all the slots are empty or the correct letter
                    for ($i = 0; $i < strlen($word); $i++){
                        if ($board[$temp][$randX] !== " " && $board[$temp][$randX] !== $word[$i]){
                            $validated = false;
                            break;
                        }
                        $temp++;
                    }
                    if ($validated === false){
                        //if it does not work, reroll a new spot and try again
                        continue;
                    }
                    else{
                        //place the word into the array
                        $wordCoords = [];
                        for ($i = 0; $i < strlen($word); $i++){
                            $board[$randY][$randX] = $word[$i];
                            $wordCoords[] = "$randX,$randY";
                            $randY++;
                        }
                        $TPL["words"][$word] = $wordCoords;
                    }
                }
            }
            else if ($orientation === 1){
                //this is for horizontal words
                $validated = false;
                while (!$validated){
                    //generate a random starting coord for the word
                    $randX = rand(0,$size - 1);
                    $randY = rand(0, $size - 1);
                    //make sure it will not go out of bounds of the board, if so, reroll
                    if ($randX + (strlen($word) - 1) > $size - 1){
                        continue;
                    }
                    $temp = $randX;
                    $validated = true;
                    //check and make sure either all the slots are empty or the correct letter
                    for ($i = 0; $i < strlen($word); $i++){
                        if ($board[$randY][$temp] !== " " && $board[$randY][$temp] !== $word[$i]){
                            $validated = false;
                            break;
                        }
                        $temp++;
                    }
                    if ($validated === false){
                        //if it does not work, reroll a new spot and try again
                        continue;
                    }
                    else{
                        //place the word into the array
                        $wordCoords = [];
                        for ($i = 0; $i < strlen($word); $i++){
                            $board[$randY][$randX] = $word[$i];
                            $wordCoords[] = "$randX,$randY";
                            $randX++;
                        }
                        $TPL["words"][$word] = $wordCoords;
                    }
                }
            }
            else if ($orientation === 2){
                //this is for reverse vertical words (down to up)
                $validated = false;
                while (!$validated){
                    //generate a random starting coord for the word
                    $randX = rand(0,$size - 1);
                    $randY = rand(0, $size - 1);
                    //make sure it will not go out of bounds of the board, if so, reroll
                    if ($randY - (strlen($word) - 1) < 0){
                        continue;
                    }
                    $temp = $randY;
                    $validated = true;
                    //check and make sure either all the slots are empty or the correct letter
                    for ($i = 0; $i < strlen($word); $i++){
                        if ($board[$temp][$randX] !== " " && $board[$temp][$randX] !== $word[$i]){
                            $validated = false;
                            break;
                        }
                        $temp--;
                    }
                    if ($validated === false){
                        //if it does not work, reroll a new spot and try again
                        continue;
                    }
                    else{
                        //place the word into the array
                        $wordCoords = [];
                        for ($i = 0; $i < strlen($word); $i++){
                            $board[$randY][$randX] = $word[$i];
                            $wordCoords[] = "$randX,$randY";
                            $randY--;
                        }
                        $TPL["words"][$word] = $wordCoords;
                    }
                }
            }
            else if ($orientation === 3){
                //this is for reverse horizontal words (right to left)
                $validated = false;
                while (!$validated){
                    //generate a random starting coord for the word
                    $randX = rand(0,$size - 1);
                    $randY = rand(0, $size - 1);
                    //make sure it will not go out of bounds of the board, if so, reroll
                    if ($randX - (strlen($word) - 1) < 0){
                        continue;
                    }
                    $temp = $randX;
                    $validated = true;
                    //check and make sure either all the slots are empty or the correct letter
                    for ($i = 0; $i < strlen($word); $i++){
                        if ($board[$randY][$temp] !== " " && $board[$randY][$temp] !== $word[$i]){
                            $validated = false;
                            break;
                        }
                        $temp--;
                    }
                    if ($validated === false){
                        //if it does not work, reroll a new spot and try again
                        continue;
                    }
                    else{
                        //place the word into the array
                        $wordCoords = [];
                        for ($i = 0; $i < strlen($word); $i++){
                            $board[$randY][$randX] = $word[$i];
                            $wordCoords[] = "$randX,$randY";
                            $randX--;
                        }
                        $TPL["words"][$word] = $wordCoords;
                    }
                }
            }
            else if ($orientation === 4){
                //this is for diagonal (top left to bottom right)
                $validated = false;
                while (!$validated){
                    //generate a random starting coord for the word
                    $randX = rand(0,$size - 1);
                    $randY = rand(0, $size - 1);
                    //make sure it will not go out of bounds of the board, if so, reroll
                    if ($randY + (strlen($word) - 1) > $size - 1 || $randX + (strlen($word) - 1) > $size - 1 ){
                        continue;
                    }
                    $tempX = $randX;
                    $tempY = $randY;
                    $validated = true;
                    //check and make sure either all the slots are empty or the correct letter
                    for ($i = 0; $i < strlen($word); $i++){
                        if ($board[$tempY][$tempX] !== " " && $board[$tempY][$tempX] !== $word[$i]){
                            $validated = false;
                            break;
                        }
                        $tempX++;
                        $tempY++;

                    }
                    if ($validated === false){
                        //if it does not work, reroll a new spot and try again
                        continue;
                    }
                    else{
                        //place the word into the array
                        $wordCoords = [];
                        for ($i = 0; $i < strlen($word); $i++){
                            $board[$randY][$randX] = $word[$i];
                            $wordCoords[] = "$randX,$randY";
                            $randY++;
                            $randX++;
                        }
                        $TPL["words"][$word] = $wordCoords;
                    }
                }
            }
            else if ($orientation === 5){
                //this is for diagonal (top right to bottom left)
                $validated = false;
                while (!$validated){
                    //generate a random starting coord for the word
                    $randX = rand(0,$size - 1);
                    $randY = rand(0, $size - 1);
                    //make sure it will not go out of bounds of the board, if so, reroll
                    if ($randY + (strlen($word) - 1) > $size - 1 || $randX - (strlen($word) - 1) < 0){
                        continue;
                    }
                    $tempX = $randX;
                    $tempY = $randY;
                    $validated = true;
                    //check and make sure either all the slots are empty or the correct letter
                    for ($i = 0; $i < strlen($word); $i++){
                        if ($board[$tempY][$tempX] !== " " && $board[$tempY][$tempX] !== $word[$i]){
                            $validated = false;
                            break;
                        }
                        $tempX--;
                        $tempY++;

                    }
                    if ($validated === false){
                        //if it does not work, reroll a new spot and try again
                        continue;
                    }
                    else{
                        //place the word into the array
                        $wordCoords = [];
                        for ($i = 0; $i < strlen($word); $i++){
                            $board[$randY][$randX] = $word[$i];
                            $wordCoords[] = "$randX,$randY";
                            $randY++;
                            $randX--;
                        }
                        $TPL["words"][$word] = $wordCoords;
                    }
                }
            }
            else if ($orientation === 6){
                //this is for diagonal (bottom right to top left)
                $validated = false;
                while (!$validated){
                    //generate a random starting coord for the word
                    $randX = rand(0,$size - 1);
                    $randY = rand(0, $size - 1);
                    //make sure it will not go out of bounds of the board, if so, reroll
                    if ($randY - (strlen($word) - 1) < 0 || $randX - (strlen($word) - 1) < 0){
                        continue;
                    }
                    $tempX = $randX;
                    $tempY = $randY;
                    $validated = true;
                    //check and make sure either all the slots are empty or the correct letter
                    for ($i = 0; $i < strlen($word); $i++){
                        if ($board[$tempY][$tempX] !== " " && $board[$tempY][$tempX] !== $word[$i]){
                            $validated = false;
                            break;
                        }
                        $tempX--;
                        $tempY--;

                    }
                    if ($validated === false){
                        //if it does not work, reroll a new spot and try again
                        continue;
                    }
                    else{
                        //place the word into the array
                        $wordCoords = [];
                        for ($i = 0; $i < strlen($word); $i++){
                            $board[$randY][$randX] = $word[$i];
                            $wordCoords[] = "$randX,$randY";
                            $randY--;
                            $randX--;
                        }
                        $TPL["words"][$word] = $wordCoords;
                    }
                }
            }
            else if ($orientation === 7){
                //this is for diagonal (top right to bottom left)
                $validated = false;
                while (!$validated){
                    //generate a random starting coord for the word
                    $randX = rand(0,$size - 1);
                    $randY = rand(0, $size - 1);
                    //make sure it will not go out of bounds of the board, if so, reroll
                    if ($randY + (strlen($word) - 1) > $size - 1 || $randX - (strlen($word) - 1) < 0){
                        continue;
                    }
                    $tempX = $randX;
                    $tempY = $randY;
                    $validated = true;
                    //check and make sure either all the slots are empty or the correct letter
                    for ($i = 0; $i < strlen($word); $i++){
                        if ($board[$tempY][$tempX] !== " " && $board[$tempY][$tempX] !== $word[$i]){
                            $validated = false;
                            break;
                        }
                        $tempX--;
                        $tempY++;

                    }
                    if ($validated === false){
                        //if it does not work, reroll a new spot and try again
                        continue;
                    }
                    else{
                        //place the word into the array
                        $wordCoords = [];
                        for ($i = 0; $i < strlen($word); $i++){
                            $board[$randY][$randX] = $word[$i];
                            $wordCoords[] = "$randX,$randY";
                            $randY++;
                            $randX--;
                        }
                        $TPL["words"][$word] = $wordCoords;
                    }
                }
            }
            else if ($orientation === 8){
                //this is for diagonal (bottom left to top right)
                $validated = false;
                while (!$validated){
                    //generate a random starting coord for the word
                    $randX = rand(0,$size - 1);
                    $randY = rand(0, $size - 1);
                    //make sure it will not go out of bounds of the board, if so, reroll
                    if ($randY - (strlen($word) - 1) < 0 || $randX + (strlen($word) - 1) > ($size - 1)){
                        continue;
                    }
                    $tempX = $randX;
                    $tempY = $randY;
                    $validated = true;
                    //check and make sure either all the slots are empty or the correct letter
                    for ($i = 0; $i < strlen($word); $i++){
                        if ($board[$tempY][$tempX] !== " " && $board[$tempY][$tempX] !== $word[$i]){
                            $validated = false;
                            break;
                        }
                        $tempX++;
                        $tempY--;

                    }
                    if ($validated === false){
                        //if it does not work, reroll a new spot and try again
                        continue;
                    }
                    else{
                        //place the word into the array
                        $wordCoords = [];
                        for ($i = 0; $i < strlen($word); $i++){
                            $board[$randY][$randX] = $word[$i];
                            $wordCoords[] = "$randX,$randY";
                            $randY--;
                            $randX++;
                        }
                        $TPL["words"][$word] = $wordCoords;
                    }
                }
            }
        }
        /*
        $letters = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
        for($i = 0; $i < $size; $i++){
            for($j = 0; $j < $size; $j++){
                if ($board[$i][$j] === " "){
                    $randLetter = $letters[rand(0, count($letters) - 1)];
                    $board[$i][$j] = $randLetter;
                }  
            }
        }
        */
        return $board;
    }
    function validateCoords($coords){
        $pairs = [];
        foreach($coords as $coord){
            $pair = explode(",",$coord);
            $x = filter_var($pair[0],FILTER_VALIDATE_INT);
            $y = filter_var($pair[0],FILTER_VALIDATE_INT);
            if ($x === false|| $y === false){
                return false;
            }
            $pairs[] = array($x,$y);
        }
        return $pairs;
    }
?>