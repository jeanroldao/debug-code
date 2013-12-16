import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public class Teste30 {
	
	public interface ITeste {
		public static final String NOME = "Ninguem";
		public static final int IDADE = 25;
	}
	
	public static void main(String[] args) {
		t();
	}
	public static void main1(String[] args) {
		
		//System.out.println(Integer.highestOneBit(10));
		
		System.out.println(ITeste.NOME);
		System.out.println(ITeste.IDADE);
		
		Field[] fields = ITeste.class.getFields();
		for (Field f : fields) {
			System.out.println(f);
		}
		
		System.out.println(ITeste.class.getModifiers());
	}
	
	static void t() {
		System.out.println(sun.reflect.Reflection.getCallerClass(0));
		System.out.println(sun.reflect.Reflection.getCallerClass(1));
		System.out.println(sun.reflect.Reflection.getCallerClass(2));
		System.out.println(sun.reflect.Reflection.getCallerClass(3));
		
	}
}

