import java.util.Scanner;
import java.io.Console;

public class java_teste1 {

     public static void main(String[] args) {
		Object i = 10;
		System.out.println(i.toString());
	 }
	 
	 public static void main3(String[] args) {
		String mul = null;
		System.out.println(String.valueOf(mul));
	 }
	 
	 public static void main2(String[] args) {
		Console console = System.console();
		System.out.print("pass: ");
		//String pass = new String(console.readPassword());
		char[] pass = console.readPassword();
		
		System.out.println(pass);
		printpass(pass);
		System.out.println(pass);
	 }
	 
	 static void printpass(final char[] pass) {
		for (int i = 0; i < pass.length; i++) {
			System.out.print(pass[i]);
			pass[i] = ' ';
		}
		System.out.println();
		
	 }
	 
	 public static void main1(String[] args) {
        //System.out.println("oi?");
		
		int[][] nums = {{1,2,3}, {4,5},{6}};
		
		for (int i = 0; i < nums.length; i++) {
			for (int k = 0; k < nums[i].length; k++) {
				System.out.println("["+i+"]["+k+"] = "+nums[i][k]);
			}
			System.out.println();
		}
     }
     
} 
   