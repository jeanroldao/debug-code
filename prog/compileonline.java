//http://www.compileonline.com/compile_java_online.php

import java.util.Scanner;

public class compileonline {

     public static void main(String args[]) {
        lists(args);
        scanner();
        System.out.println("EOF?");
     }
     
     static void scanner() {
        Scanner scan = new Scanner(System.in);
        
        while (scan.hasNextLine()) {
          String linha = scan.nextLine();
          linha = linha.replace(";", "|");
          System.out.println(linha);
        }
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
   