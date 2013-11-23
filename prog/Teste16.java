import java.util.*;
import java.io.*;

public class Teste16 {
	static Runtime runtime = Runtime.getRuntime();
	
	public static void main(String[] args) throws Exception{
		
		long usableFreeMemory = getUsableFreeMemory();
		
		//int[] ar = new int[1000000];
		/*
		List<Integer> ar = new ArrayList<Integer>();
		for (int i = 0; i < 1000000; i++) {
			ar.add(i);
		}
		*/
		/*
		StringBuilder ar = new StringBuilder();
		for (int i = 0; i < 1000000; i++) {
			ar.append(i).append(',');
		}
		String ars = ar.toString();
		ar = null;
		*/
		File file = new File("rt.jar");
		//String content = new Scanner(new File("rt.jar")).useDelimiter("\\Z").next();
		FileInputStream is = new FileInputStream(file);
		byte[] b = new byte[(int) file.length()];  
		is.read(b, 0, (int) file.length());
		//String content = new String(b);
		//b = null;
		
		System.out.println((double)(usableFreeMemory - getUsableFreeMemory()) / (1024));
		System.out.println((char)b[50250075]);
	}
	
	static long getUsableFreeMemory() {
		System.gc();
		return runtime.maxMemory() - runtime.totalMemory() + runtime.freeMemory();
	}
}

