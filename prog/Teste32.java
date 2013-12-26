import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public class Teste32 {
	
	
	public static void main() throws Exception {
		System.out.println("wrong method()!");
	}
	
	public static void main(String args) throws Exception {
		System.out.println("wrong method("+args+")!");
	}
	
	public static void main1(String[] args) throws Exception {
		long l = Long.MAX_VALUE;
		System.out.println(l);
		System.out.println(l / 2);
		System.out.println(l * 2);
		System.out.println(l + 2);
		System.out.println(l - 2);
	}
	
	private static native void php(long l);
	
	private static void print(long l) {
		try {
			php(l);
		} catch (UnsatisfiedLinkError e) {
			System.out.println(l);
		}
	}
	
	public static void main(String[] args) throws Exception {
		//System.out.println(System.getProperties());
		//System.loadLbrary("Test");
		long l = 100000000000000L;
		System.out.println(l);
		php(l);
		System.out.println(System.getProperty("java.runtime.version"));
		System.out.println(System.getProperty("ENV", "?"));
		System.out.println(System.getProperty("LOG", "?"));
		for (String s : args) {
			System.out.print('(');
			System.out.print(s.length());
			System.out.print(')');
			System.out.println(s);
		}
	}
	
	public static void main(int i) throws Exception {
		System.out.println("wrong method("+i+")!");
	}
}

