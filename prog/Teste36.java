import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste36 {

	static String getString(String s) {
		return new String(s.toCharArray());
	}
	
	static class L1 {
		public static final L2 l2 = new L2();
		public static final String NOME = ("NOME_L1");
		static {
			System.out.println("class L1 loaded!");
		}
		
		L1() {
			System.out.println("class L1 instance done! - " + NOME);
		}
		
		protected void finalize() {
			System.out.println("class L1 finalize done!");
		}
	}

	static class L2 extends L1 {
		public static final L1 l1 = new L1();
		public static final String NOME = ("NOME_L2");
		static {
			System.out.println("class L2 loaded!");
		}
		
		L2() {
			System.out.println("class L2 instance done! - " + NOME);
		}
		
		protected void finalize() {
			System.out.println("class L2 finalize done!");
		}
	}
	
	public static void main(String[] args) throws Exception {
		Class<?> cls = Class.forName("Teste36$L2");
		System.out.println(cls.newInstance());
		//Class<?> cls = sun.reflect.Reflection.getCallerClass(1);
		Class<?> cls_io = Class.forName("java.io.BufferedInputStream");
		ClassLoader cl = cls_io.getClassLoader();
		System.out.println(cl);
		
	}
	
}

