import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste33 {
	
	public static void main(String[] args) {
		System.out.println(Teste33.getImpl("TesTes").getClass());
	}
	
	public static Teste33 getImpl(String name) {
		return new Teste33Impl(name);
	}
	
	public abstract String getName();
	
	public static class Teste33Impl extends Teste33{
		
		private String name;
		
		public Teste33Impl(String name) {
			this.name = name;
		}
		
		public String getName() {
			return name;
		}
	}
	
}

