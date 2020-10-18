<?php
if (!empty($this->varInIncludeFile)){
    $multi = [];
    $multi[] = $this->varInIncludeFile;
    $multi[] = 'I am a var in test_include.php';
    $this->varInIncludeFile = $multi;
} else {
    $this->varInIncludeFile = 'I am a var in test_include.php';    
}