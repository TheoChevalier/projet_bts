<?php
public function receive()  
{
    if (!$this->isValid()) {  
        throw new Exception('No file uploaded!');  
    }  
  
    $fileReader = fopen('php://input', "r");  
    $fileWriter = fopen($this->_destination . $this->_fileName, "w+");  
  
    while(true) {  
        $buffer = fgets($fileReader, 4096);  
        if (strlen($buffer) == 0) {  
            fclose($fileReader);  
            fclose($fileWriter);  
            return true;  
        }  
  
        fwrite($fileWriter, $buffer);  
    }  
  
    return false;  
}
    ?>