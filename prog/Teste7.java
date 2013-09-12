import java.util.*;
import java.io.*;

public class Teste7 {

	private class Teste71 {
		
	}

	public static void main(String[] args) throws Exception {
		Object obj1 = new Teste3();
		Object obj2 = new Teste7();
		
		//System.out.println(System.identityHashCode(obj1) == System.identityHashCode(obj1));
		ClassLoader cl = obj1.getClass().getClassLoader();
		
		System.out.println(cl);
		System.out.println(obj2.getClass().getClassLoader());
		System.out.println(Boolean.valueOf(cl == obj2.getClass().getClassLoader()));
		
		System.out.println("cl instanceof java.lang.ClassLoader");
		System.out.println(Boolean.valueOf(cl instanceof ClassLoader));
		
		System.out.println(obj1);
		System.out.println(obj2);
		
		System.out.println(((Teste1)cl.loadClass("Teste1").newInstance()).getInstanceDebug());
		
		System.out.println(((Teste1)cl.loadClass("Teste1").newInstance()).getInstanceTesting());
		((Teste1)cl.loadClass("Teste1").newInstance()).setInstanceTesting(true);
		System.out.println(((Teste1)cl.loadClass("Teste1").newInstance()).getInstanceTesting());
		
		System.out.println(Teste71.class);
		System.out.println(((String)Class.forName("java.lang.String").newInstance()).length());
	}
	
}

