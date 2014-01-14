import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste38 {

	public static void main(String[] args) throws Exception {
		try {
			Method[] ms = Teste38_T2.class.getMethods();
			
			System.out.println(ms.length);
			for (Method m : ms) {
				//System.out.println(m);
			}
			Teste38_T2.class.getMethod("nd", String.class).invoke(new Teste38_T2("LOL"), "vai");
			Teste38_T2.class.getMethod("nd", Integer.TYPE).invoke(new Teste38_T2("LOL"), 123);
			Teste38_T2.class.getMethod("nd", Integer.class).invoke(new Teste38_T2("LOL"), 123);
			Teste38_T2.class.getMethod("s_nd", Integer.TYPE).invoke(null, 123);
			//System.out.println(Reflection.quickCheckMemberAccess(clazz, modifiers));
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	
	
}

class Teste38_T1 {
	private String name;
	
	public Teste38_T1(String name) {
		this.name = name;
	}
	
	public static void s_nd(int i) {
		System.out.println("static: s_nd(i_" + i + ")");
	}
	
	public void nd(int i) {
		System.out.println(name + ": nd(i_" + (i + 1) + ")");
	}
	
	public void nd(Integer i) {
		System.out.println(name + ": nd(Integer_" + i + ")");
	}
	
	public void nd(String s) {
		System.out.println(name + ": nd(" + s + ")");
	}
}

class Teste38_T2 extends Teste38_T1 {
	public Teste38_T2(String name) {
		super("t2-" + name);
	}
	public void nd1() {}
	public void nd2() {}
	public void nd3() {}
}
