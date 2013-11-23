import java.util.*;
import java.io.*;

public class Teste19 {
	
	public static void main2(String[] args) throws Exception {
		List<String> coisas = new ArrayList<String>() {{
			add("coisa 1");
			add("coisa 2");
			add("terceira coisa");
			add("nada de mais");
		}};
		
		//System.out.println(Teste19.class);
		//System.out.println(coisas.getClass().getEnclosingClass());
	}
	public static void main1(String[] args) throws Exception {
		int[] nums = {1, 2, 3, 4, 5, 6};
		int[] num2 = new int[5];
		
		System.arraycopy(nums, 0, num2, 0, nums.length);
		
		for (int n : num2) {
			System.out.println(n);
		}
	}
	public static void main(String[] args) throws Exception{
		try {
			FileInputStream fileIn = new FileInputStream("C:\\java_dev\\projects\\local\\jcrawler\\a2.ser");
	        ObjectInputStream in = new ObjectInputStream(fileIn);
			
	        Object str = in.readUTF();
	        System.out.println("\""+str+"\"");
			
	        Object str2 = in.readUTF();
	        System.out.println("\""+str2+"\"");
			
	        Object str3 = in.readUTF();
	        System.out.println("\""+str3+"\"");
			
			//Object p = in.readObject();
			//System.out.println(p);
			
	        in.close();
	        fileIn.close();
	    } catch (Exception e) {
			System.out.println(e);
			//e.printStackTrace();
		}
	}
}

