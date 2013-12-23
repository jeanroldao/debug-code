import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public class Teste31 {
	
	public static class Novo extends Teste31 {
		public static void p(int n) {
			Teste31.p("new int: " + n);
		}
		
		public static void p(String p) {
			Teste31.p("new String: " + p);
		}
	}
	
	public static void main(String[] args) throws Exception {
		
		System.out.println(System.in.getClass());
		System.out.println(System.out.getClass());
		
		int n = 20;
		String name = "no name";
		Teste31 t = new Novo();
		t.p(n);
		((Novo)t).p(n);
		t.p(name);
		((Novo)t).p(name);
	}
	
	public static void p(String p) {
		System.out.print("String: ");
		System.out.println(p);
	}
	
	public static void p(int n) {
		System.out.print("int: ");
		System.out.println(n);
	}
}

