import java.util.*;
import java.io.*;

public class Teste18 {
	
	public static void main(String[] args) throws Exception{
		Scanner scan = new Scanner(System.in);
		
		int[] nums = new int[2147483];
		
		int old_n = 0;
		while (true) {
			int n = scan.nextInt();
			
			nums[n] = n*2;
			
			System.out.println(nums[n]);
		}
	}
}

