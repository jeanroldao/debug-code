import java.util.*;
import java.io.*;

public class Teste3 {

	public static void main(String[] args) throws Exception {
		
		double d1 = 2.2250738585072012e-308;
		System.out.println(d1);
		
		String s = "2.2250738585072012e-308";
		System.out.println(s);
		
		Double d2 = new Double(s);
		System.out.println(d2);
		
		System.out.println(Boolean.valueOf(d1 == (double)d2));
		
	}
}

