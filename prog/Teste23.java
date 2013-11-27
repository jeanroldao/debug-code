import java.util.*;
import java.io.*;
import java.sql.*;

public class Teste23 implements Serializable {

	@Deprecated
	static class Coisa1 {
		public static Class getC() {return Coisa1.class; }
	}
	
	static class Node<T> {
		
		T value;
		Node<T> next;
		int size = 0;
		
		public void add(T o) {
			if (size == 0) {
				value = o;
			} else {
				if (next == null) {
					next = new Node<T>();
				}
				next.add(o);
			}
			size++;
		}
		
		public T get(int i) {
			if (i == 0) {
				return value;
			} else {
				return next.get(i - 1);
			}
		}
		
		public int size() {
			return size;
		}
	}
	
	static class PhpArray<T> {
	
		static {
			System.loadLibrary("Teste23_PhpArray_Native");
		}
		
		private Object _list;
		
		private int size = 0;
		
		public PhpArray() {
			init();
		}
		
		private native void init();
		
		public native void add(T o);
		
		public native T get(int i);
		
		public int size() {
			return size;
		}
	}
	
	public static void main(String[] args) {
		try {
			//Node<String> list = new Node<String>();
			//ArrayList<String> list = new ArrayList<String>();
			LinkedList<String> list = new LinkedList<String>();
			//PhpArray<String> list = new PhpArray<String>();
			for (int i = 0; i < 1000; i++) {
				list.add("str " + i);
				System.out.println(i + ": add str" + i);
			}
			
			//for (int i = 0; i < list.size(); i++) {
			//	System.out.println(i + ": get " + list.get(i));
			//}
			int i = 0;
			for (String s : list) {
				System.out.println((i++) + ": get " + s);
			}
			
			System.out.println("done");
			new Scanner(System.in).nextLine();
		} catch (UnsatisfiedLinkError e) {
			System.out.println("error");
		}
	}

	public static void main0(String[] args) throws Exception {
		
		
		
		//List<String> ls = new ArrayList<String>() {};
		
		//System.out.println(Arrays.asList(Teste23.class.getDeclaredClasses()));
		/*
		Map<Class, String> trad = new HashMap<Class, String>();
		trad.put(Coisa1.class, "coisa 1");
		trad.put(String.class, "string");
		
		System.out.println(trad.toString().length());
		*/
		
		//System.out.println(new Teste23() instanceof Serializable ? "Serializable" : "error!");
		
		//System.out.println(Teste23.class.getAnnotation(Deprecated.class));
		//System.out.println(Coisa1.class.getAnnotation(Deprecated.class));
		/*
		Class cls = ls.getClass();
		while (cls != null) {
			System.out.println(cls);
			System.out.println(cls.getDeclaredClasses().length);
			cls = cls.getSuperclass();
		}*/
	}
}

