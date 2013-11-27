import java.util.*;
import java.io.*;
import java.sql.*;

public class Teste25 implements Serializable {
	
	static class Container<T> {
		private Object store;
		
		public void set(T o) {
			store = o;
		}
		
		public T get() {
			return (T) store;
		}
	}
	
	public static void main(String[] args) {
		
		Container<String> c = new Container<String>();
		
		c.set("qual");
		
		Container c1 = c;
		
		c1.set(1);
		
		System.out.println(((Container)c).get());
	}
}

