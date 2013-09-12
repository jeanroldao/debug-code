<?php

$codigojava = '';

for ($i = 0; $i < 10; $i++) {
  //$codigojava .= 'System.out.println("testando codigo java x'.$i.'");';
  $codigojava .= "testando($i);";
}

$codigojava .= 'System.out.println();';

$codigojava .= 'lists(new String[0]);';

$code = <<<CODE
public class HelloWorld {

     public static void main(String []args){
        System.out.println("isso eh codigo <i>java</i>!");
        {$codigojava}
     }
     
     static void testando(int x) {
        System.out.println("codigo java x" + x);
     }
	 
     static void lists(String[] args) {
        
        for (String s : args) {
          System.out.println(s);
        }
        
        System.out.println("Hello World");
        
        System.out.println("Number to int: " + ((Number)5.7).intValue());
        
        Number num = 5;
        
        System.out.println(num.toString());
        
        Integer[] num2 = {2,1};
        Number[] nums = num2; //{3, 2.5};
        
        for (Number n : nums) {
          System.out.println(n.toString());
        }
        
        int[] nums2 = {3, 2};
        
        for (Number n : nums2) {
          System.out.println(n.toString());
        }
        System.out.println();
        
        java.util.List<? extends Number> lnum;
        lnum = new java.util.ArrayList<Integer>();
     }
} 
CODE;

$postdata = http_build_query(
    array(
        'lang' => 'java',
        'code' => $code,
        'submit' => 'Execute',
        'args' => '"{F}"',
        'inputs' => ''
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context  = stream_context_create($opts);

$result = file_get_contents('http://www.compileonline.com/compile.php', false, $context);
$result = explode('"{F}"', $result);
$result = end($result);
echo html_entity_decode(strip_tags($result));
?>
   