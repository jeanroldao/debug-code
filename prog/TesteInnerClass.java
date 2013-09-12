import java.util.*;
import java.io.*;

public class TesteInnerClass {

	static String s1 = "TesteInnerClass";
	
	public static class dentro {
	
		static String s2 = s1+".dentro";
		
		public static class da {
		
			static String s3 = s2+".da";
			
			public static class classe {
			
				static String s4 = s3+".classe";
				
				public static void falarei() {
					System.out.println(s4+".falar");
				}
			}
		}
	}
	
	public static void main(String[] args) throws Exception {
		TesteInnerClass.dentro.da.classe.falarei();
	}
}