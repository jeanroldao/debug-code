import java.util.*;
import java.io.*;

public class Teste5 {

	public static void main(String[] args) throws Exception {
		
		try {
			long ini = System.currentTimeMillis();
			System.out.println(ini);
			int num = 0;
			
			List<Integer> nums = new ArrayList<Integer>();
			while (true) {
				nums.add(num++);
				if (System.currentTimeMillis() - ini > 500) {
					ini = System.currentTimeMillis();
					System.out.println(nums.size());
				}
			}
		} catch (Exception e) {
			return;
		}
		
	}
	
}

