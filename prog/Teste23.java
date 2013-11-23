import java.util.*;
import java.io.*;
import java.sql.*;

public class Teste23 implements Serializable {

	@Deprecated
	static class Coisa1 {
		public static Class getC() {return Coisa1.class; }
	}
	
	public static void main(String[] args) throws Exception {
		List<String> ls = new ArrayList<String>() {};
		
		//System.out.println(Arrays.asList(Teste23.class.getDeclaredClasses()));
		/*
		Map<Class, String> trad = new HashMap<Class, String>();
		trad.put(Coisa1.class, "coisa 1");
		trad.put(String.class, "string");
		
		System.out.println(trad.toString().length());
		*/
		
		//System.out.println(new Teste23() instanceof Serializable ? "Serializable" : "error!");
		
		System.out.println(Teste23.class.getAnnotation(Deprecated.class));
		System.out.println(Coisa1.class.getAnnotation(Deprecated.class));
		/*
		Class cls = ls.getClass();
		while (cls != null) {
			System.out.println(cls);
			System.out.println(cls.getDeclaredClasses().length);
			cls = cls.getSuperclass();
		}*/
	}
}

