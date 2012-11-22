<?php
if( !function_exists('ftok') )
{
    function ftok($filename = "", $proj = "")
    {
        if( empty($filename) || !file_exists($filename) )
        {
            return -1;
        }
        else
        {
            $filename = $filename . (string) $proj;
            for($key = array(); sizeof($key) < strlen($filename); $key[] = ord(substr($filename, sizeof($key), 1)));
            return dechex(array_sum($key));
        }
    }
}

class SharedMemory{
    private $nameToKey = array();
    private $key;
    private $id;
    function __construct($key = null){
        if($key === null){
            $tmp = tempnam('/tmp', 'PHP');
            $this->key = ftok($tmp, 'a');
            $this->id = shm_attach($this->key);
            $this->nameToKey[] = '';
            $this->nameToKey[] = '';
            $this->updateMemoryVarList();
            shm_put_var($this->id, 1, 1);
        }else{
            $this->key = $key;
            $this->id = sem_get($this->key);
            $this->refreshMemoryVarList();
            shm_put_var($this->id, 1, shm_get_var($this->id, 1) + 1);
        }
        if(!$this->id)
            die('Unable to create shared memory segment');
    }
    function __sleep(){
        shm_detach($this->id);
    }
    function __destruct(){
        if(shm_get_var($this->id, 1) == 1){
            // I am the last listener so kill shared memory space
            $this->remove();
        }else{
            shm_detach($this->id);
            shm_put_var($this->id, 1, shm_get_var($this->id, 1) - 1);
        }
    }
    function __wakeup(){
        $this->id = sem_get($this->key);
        shm_attach($this->id);
        $this->refreshMemoryVarList();
        shm_put_var($this->id, 1, shm_get_var($this->id, 1) + 1);
    }
    function getKey(){
        return $this->key;
    }
    function remove(){
        shm_remove($this->id);
    }
    function refreshMemoryVarList(){
        $this->nameToKey = shm_get_var($this->id, 0);
    }
    function updateMemoryVarList(){
        shm_put_var($this->id, 0, $this->nameToKey);
    }
    function __get($var){
        if(!in_array($var, $this->nameToKey)){
            $this->refreshMemoryVarList();
        }
        return shm_get_var($this->id, array_search($var, $this->nameToKey));
    }
    function __set($var, $val){
        if(!in_array($var, $this->nameToKey)){
            $this->refreshMemoryVarList();
            $this->nameToKey[] = $var;
            $this->updateMemoryVarList();
        }
        shm_put_var($this->id, array_search($var, $this->nameToKey), $val);
    }
}

// Example
$sharedMem = new SharedMemory();
$pid = pcntl_fork();
if($pid){
    //parent
    sleep(1);
    echo "Parent Says: " . $sharedMem->a . "\n";
    echo "Parent Changed to 0\n";
    $sharedMem->a = 0;
    //Parent just changed it to 0
    echo "Parent Says: " . $sharedMem->a . "\n";
    sleep(2);
    // Parent think's it's 0, but child has changed it to 1
    echo "Parent Says: " . $sharedMem->a . "\n";
}else{
    //child
    $sharedMem->a = 2;
    echo "Child Changed to 2\n";
    // Should be 2 as child just set it to 2
    echo "Child Says: " . $sharedMem->a . "\n";
    sleep(2);
    // Child think's it's 2, but the parent set it to 0.
    echo "Child Says: " . $sharedMem->a . "\n";
    echo "Child Added 1\n";
    $sharedMem->a++;
    echo "Child Says: " . $sharedMem->a . "\n";
}
?>