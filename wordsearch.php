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
                $wordArray[] = $line;
            }
            fclose($fh);
            return $wordArray;
        }

    /**
     * This takes an array of words, and randomly selectes a specified amount of them from the list
     *
     * @param string $wordList the array of words to search
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
                $newWord = $wordList[rand(0, count($wordList) - 1)];
                $validated = true;
                foreach($randomWords as $word){
                    if ($newWord === $word){
                        $validated = false;
                        break;
                    }
                }
                if ($validated === true){
                    $randomWords[] = $newWord;
                }
            }
        }
        return $randomWords;
    }
?>